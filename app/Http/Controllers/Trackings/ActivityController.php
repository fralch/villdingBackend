<?php

namespace App\Http\Controllers\Trackings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Tracking;
use App\Models\Day;
use App\Models\Week;
use App\Models\Project;
use App\Models\User;
use App\Models\Activity;

class ActivityController extends Controller
{
    public function activityAll()
    {
        $activities = Activity::all();
        return response()->json($activities);
    }

   
    public function activityByProject($project_id)
    {
        // Obtener las semanas con sus actividades asociadas
        $weeks = Week::where('project_id', $project_id)
                     ->with('activities') // Asegúrate de que la relación esté definida en el modelo Week
                     ->get();

        return response()->json($weeks);
    }

    // obtener actividades por dia enlazadas a una semana y proyecto
    public function activityByWeekByProject($week_id, $project_id){
             // Obtener los días de la semana de un proyecto
        $days = Day::where('week_id', $week_id)->where('project_id', $project_id)->get();

        // Obtener las actividades asociadas a cada día
        $activities = Activity::whereIn('day_id', $days->pluck('id'))->get();   
        return response()->json($activities);
    }

    public function createActivity(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar los datos de entrada (sin tracking_id)
            $validatedData = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'hour_start' => 'required|date_format:H:i|before:hour_end',
                'hour_end' => 'required|date_format:H:i|after:hour_start',
                'status' => 'required|string|max:255',
                'icon' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'comments' => 'nullable|string',
            ]);
    
            // Procesar la imagen si se proporciona
            $imagePath = $this->processImage($request);
    
            // Obtener los IDs de los días del proyecto
            $daysIds = Day::where('project_id', $validatedData['project_id'])->pluck('id')->toArray();
    
            // Generar el array de actividades
            $activities = array_map(function ($dayId) use ($validatedData, $imagePath) {
                return array_merge($validatedData, [
                    'day_id' => $dayId,
                    'image' => $imagePath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }, $daysIds);
    
            // Insertar todas las actividades en una sola consulta
            Activity::insert($activities);
    
            DB::commit();
    
            return response()->json([
                'message' => 'Actividades creadas exitosamente para el proyecto.',
                'image_path' => $imagePath ? asset('images/activities/' . $imagePath) : null,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear actividades: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al crear actividades',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Método para procesar la imagen
    private function processImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/activities'), $imagePath);
            return $imagePath;
        }
        return null;
    }

}
