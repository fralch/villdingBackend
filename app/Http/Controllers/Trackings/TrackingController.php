<?php

namespace App\Http\Controllers\Trackings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tracking;
use App\Models\Project;
use App\Models\User;
use App\Models\Activity;


class TrackingController extends Controller
{
    /** * Obtiene todos los trackings */
    public function trackingAll(){
        $trackings = Tracking::all();
        return response()->json($trackings);
    }

   

    /** * Obtiene trackings de un proyecto específico  */
    /*
     public function trackingByProject($project_id){
        $trackings = Tracking::with('activities')->where('project_id', $project_id)->get();
        return response()->json($trackings);
    }
    */
    /** * Obtiene trackings de un proyecto específico con actividades pendientes/programadas por día */
public function trackingByProject($project_id){
    $trackings = Tracking::with('activities')->where('project_id', $project_id)->get();
    
    foreach ($trackings as $tracking) {
        // Calcula la fecha de finalización basada en date_start y duration_days
        $start_date = new \DateTime($tracking->date_start);
        $end_date = (clone $start_date)->modify('+' . ($tracking->duration_days - 1) . ' days');
        
        // Formato para almacenar actividades por día
        $tracking->activities_by_day = [];
        
        // Inicializa el array de días con fechas desde date_start hasta date_start + duration_days
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $date_string = $current_date->format('Y-m-d');
            $tracking->activities_by_day[$date_string] = [
                'date' => $date_string,
                'pending_activities' => [],
                'scheduled_activities' => []
            ];
            $current_date->modify('+1 day');
        }
        
        // Para cada actividad, verifica su estado y la agrega al día correspondiente
        foreach ($tracking->activities as $activity) {
            // Asumimos que hay un campo fecha_creacion en la actividad
            // Si la actividad no tiene una fecha específica, la omitimos
            if (empty($activity->fecha_creacion)) {
                continue;
            }
            
            $activity_date = substr($activity->fecha_creacion, 0, 10); // Formato Y-m-d
            
            // Solo procesa si la fecha está dentro del rango del tracking
            if (isset($tracking->activities_by_day[$activity_date])) {
                // Clasificamos según el status (asumiendo que status=false significa pendiente)
                if ($activity->status) {
                    $tracking->activities_by_day[$activity_date]['scheduled_activities'][] = [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'description' => $activity->description,
                        'horas' => $activity->horas
                    ];
                } else {
                    $tracking->activities_by_day[$activity_date]['pending_activities'][] = [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'description' => $activity->description,
                        'horas' => $activity->horas
                    ];
                }
            }
        }
        
        // Agregar contadores de actividades
        $tracking->total_pending_activities = 0;
        $tracking->total_scheduled_activities = 0;
        
        foreach ($tracking->activities_by_day as $day_data) {
            $tracking->total_pending_activities += count($day_data['pending_activities']);
            $tracking->total_scheduled_activities += count($day_data['scheduled_activities']);
        }
    }
    
    return response()->json($trackings);
}

   
    /**  * Crea un nuevo tracking */
    public function createTracking(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'project_id'  => 'required|exists:projects,id',
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'date_start'  => 'required|date',
                'duration_days' => 'required|integer|min:1',
            ]);

            $project_id  = $validatedData['project_id'];
            $title       = $validatedData['title'];
            $description = $validatedData['description'] ?? null;
            $date_start  = $validatedData['date_start'];
            $duration_days = $validatedData['duration_days'];

            $trackings = Tracking::create([
                'project_id' => $project_id,
                'title' => $title,
                'description' => $description,
                'date_start' => $date_start,
                'duration_days' => $duration_days,      
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]); 
            
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
