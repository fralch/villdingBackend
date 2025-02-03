<?php

namespace App\Http\Controllers\Trackings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importar la clase DB
use App\Models\Tracking;
use App\Models\Day;
use App\Models\Week;
use App\Models\Project;
use App\Models\User;
use App\Models\Activity;


class TrackingController extends Controller
{
    public function trackingAll()
    {
        $trackings = Tracking::with(['week', 'project'])->get();
        return response()->json($trackings);
    }

    public function trackingByWeekByProject($week_id, $project_id){
        $trackings = Tracking::where('week_id', $week_id)->where('project_id', $project_id)->get();
        return response()->json($trackings);
    }

    public function trackingByProject($project_id){
        // Obtener las semanas con sus trackings asociados
        $weeks = Week::where('project_id', $project_id)
                     ->with('trackings') // Asegúrate de que la relación esté definida en el modelo Week
                     ->get();
    
        return response()->json($weeks);
    }

    public function trackingByWeekByProjectByUser($week_id, $project_id, $user_id)
    {
        $trackings = Tracking::where('week_id', $week_id)
                             ->where('project_id', $project_id)
                             ->where('user_id', $user_id)
                             ->with(['week', 'project', 'user']) // Cargar relaciones adicionales
                             ->get();
        return response()->json($trackings);
    }


    public function getWeeksByProject($project_id)
    {
        $weeks = Week::where('project_id', $project_id)
                    ->with('trackings') // Cargar trackings si es necesario
                    ->get();
        return response()->json($weeks);
    }

    public function getDaysByWeek($week_id)
    {
        $days = Day::where('week_id', $week_id)
                ->with('tracking') // Cargar tracking si es necesario
                ->get();
        return response()->json($days);
    }

    public function getDaysByProject($project_id)
    {
        $days = Day::where('project_id', $project_id)
                ->with(['week', 'tracking']) // Cargar semana y tracking si es necesario
                ->get();
        return response()->json($days);
    }
    
   

    // crear tracking
    public function createTracking(Request $request) {
        DB::beginTransaction();
        try {
            // Validar los datos de entrada
            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
    
            $project_id = $request->project_id;
            $user_id = $request->user_id;
            $title = $request->title;
            $description = $request->description ?? null;
    
            // Obtener todas las semanas del proyecto con sus fechas
            $weeks = Week::where('project_id', $project_id)->get();
    
            if ($weeks->isEmpty()) {
                return response()->json([
                    'message' => 'El proyecto no tiene semanas registradas.',
                    'trackings' => []
                ], 400);
            }
    
            // Obtener semanas sin trackings existentes
            $existingTrackings = Tracking::whereIn('week_id', $weeks->pluck('id'))
                ->where('project_id', $project_id)
                ->where('user_id', $user_id)
                ->pluck('week_id')
                ->toArray();
    
            $createdTrackings = [];
            foreach ($weeks as $week) {
                if (!in_array($week->id, $existingTrackings)) {
                    $tracking = Tracking::create([
                        'week_id' => $week->id,
                        'project_id' => $project_id,
                        'user_id' => $user_id,
                        'title' => $title,
                        'description' => $description,
                        'date_start' => $week->start_date,
                        'date_end' => $week->end_date, // Opcional: usar end_date de la semana
                        'status' => 1 // Estado por defecto
                    ]);
                    $createdTrackings[] = $tracking;
                }
            }
    
            DB::commit();
    
            return response()->json([
                'message' => 'Trackings creados exitosamente.',
                'trackings' => $createdTrackings
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear trackings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


