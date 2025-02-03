<?php

namespace App\Http\Controllers\Trackings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tracking;
use App\Models\Day;
use App\Models\Week;
use App\Models\Project;
use App\Models\User;
use App\Models\Activity;


class TrackingController extends Controller
{
    public function trackingAll(){
        $trackings = Tracking::all();
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

    public function trackingByWeekByProjectByUser($week_id, $project_id, $user_id){
        $trackings = Tracking::where('week_id', $week_id)->where('project_id', $project_id)->where('user_id', $user_id)->get();
        return response()->json($trackings);
    }


    // obtener semanas de un proyecto
    public function getWeeksByProject($project_id){
        $weeks = Week::where('project_id', $project_id)->get();
        return response()->json($weeks);
    }

    // obtener dias de una semana
    public function getDaysByWeek($week_id){
        $days = Day::where('week_id', $week_id)->get();
        return response()->json($days);
    }

    // obtener dias de un proyecto
    public function getDaysByProject($project_id){
        $days = Day::where('project_id', $project_id)->get();
        return response()->json($days);
    }
    

   

    public function createTracking(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'project_id'  => 'required|exists:projects,id',
                'user_id'     => 'required|exists:users,id',
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
    
            $project_id  = $validatedData['project_id'];
            $user_id     = $validatedData['user_id'];
            $title       = $validatedData['title'];
            $description = $validatedData['description'] ?? null;
    
            // Obtener todas las semanas del proyecto
            $weeks = Week::where('project_id', $project_id)->get();
    
            if ($weeks->isEmpty()) {
                return response()->json([
                    'message'   => 'El proyecto no tiene semanas registradas.',
                    'trackings' => []
                ], 400);
            }
    
            $trackings = [];
            foreach ($weeks as $week) {
                // Crear tracking para cada semana sin verificar existencias previas
                $tracking = Tracking::create([
                    'week_id'     => $week->id,
                    'project_id'  => $project_id,
                    'user_id'     => $user_id,
                    'title'       => $title,
                    'description' => $description,
                    'status'      => true
                ]);
                $trackings[] = $tracking;
            }
    
            DB::commit();
    
            return response()->json([
                'message'   => 'Trackings creados exitosamente.',
                'trackings' => $trackings
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear trackings: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al crear trackings',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}


