<?php

namespace App\Http\Controllers\Trackings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
    

    
   

    // crear tracking
    public function createTracking(Request $request){
        DB::beginTransaction();
        try {
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
    
            // Obtener las semanas de un proyecto que no tienen trackings
            $weeks = Week::where('project_id', $project_id)
                         ->whereDoesntHave('trackings')
                         ->get();
    
            $trackings = [];
            foreach ($weeks as $week) {
                $tracking = Tracking::create([
                    'week_id' => $week->id,
                    'project_id' => $project_id,
                    'user_id' => $user_id,
                    'title' => $title,
                    'description' => $description,
                    'date_start' => $week->start_date // Asumiendo que `start_date` es un campo de la semana
                ]);
                $trackings[] = $tracking;
            }
    
            DB::commit();
            return response()->json([
                'message' => 'Trackings creados correctamente',
                'trackings' => $trackings
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el tracking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /* 
     public function createTracking(Request $request){
        try {
            $project_id = $request->project_id;
            $user_id = $request->user_id;
            $title = $request->title;
            $description = $request->description ?? null;

            // Obtener las semanas de un proyecto
            $weeks = Week::where('project_id', $project_id)->get();

            // Crear los trackings de las semanas
            foreach ($weeks as $week) {
                Tracking::create([
                    'week_id' => $week->id,
                    'project_id' => $project_id,
                    'user_id' => $user_id,
                    'title' => $title,
                    'description' => $description,
                    'date_start' => now()
                ]);
            }

            return response()->json(['message' => 'Tracking creado correctamente'], 200);

        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un mensaje de error
            return response()->json([
                'message' => 'Error al crear el tracking',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    */
}


