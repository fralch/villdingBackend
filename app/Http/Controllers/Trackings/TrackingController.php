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
        $trackings = Tracking::with(['week', 'project', 'user'])->get();
        return response()->json($trackings);
    }

    public function trackingByWeekByProject($week_id, $project_id){
        $trackings = Tracking::where('week_id', $week_id)->where('project_id', $project_id)->get();
        return response()->json($trackings);
    }

    public function trackingByProject($project_id)
    {
        $weeks = Week::where('project_id', $project_id)
                    ->with(['trackings' => function($query) {
                        $query->with('user'); // Cargar la relación user
                    }])
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
    
            // Obtener todas las semanas del proyecto
            $weeks = Week::where('project_id', $project_id)->get();
    
            $trackings = [];
            foreach ($weeks as $week) {
                // Verificar si ya existe un tracking para esta semana
                $existingTracking = Tracking::where('week_id', $week->id)
                                            ->where('project_id', $project_id)
                                            ->where('user_id', $user_id)
                                            ->first();
    
                // Si no existe, crear un nuevo tracking
                if (!$existingTracking) {
                    $tracking = Tracking::create([
                        'week_id' => $week->id,
                        'project_id' => $project_id,
                        'user_id' => $user_id,
                        'title' => $title,
                        'description' => $description,
                        'date_start' => $week->start_date // Usar la fecha de inicio de la semana
                    ]);
                    $trackings[] = $tracking;
                }
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
}


