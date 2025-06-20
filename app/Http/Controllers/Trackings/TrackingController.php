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
    /** * Obtiene todos los trackings (solo los no eliminados) */
    public function trackingAll(){
        $trackings = Tracking::all(); // Solo obtiene los no eliminados por defecto
        return response()->json($trackings);
    }

    /** * Obtiene todos los trackings incluyendo los eliminados */
    public function trackingAllWithTrashed(){
        $trackings = Tracking::withTrashed()->get();
        return response()->json($trackings);
    }

    /** * Obtiene solo los trackings eliminados */
    public function trackingOnlyTrashed(){
        $trackings = Tracking::onlyTrashed()->get();
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
        // Solo obtiene trackings no eliminados
        $trackings = Tracking::with('activities')->where('project_id', $project_id)->get();
        
        $result = [];
        foreach ($trackings as $tracking) {
            // Convertir el tracking a un array para poder manipularlo
            $trackingArray = $tracking->toArray();
        
            // Calcula la fecha de finalización basada en date_start y duration_days
            $start_date = new \DateTime($tracking->date_start);
            $end_date = (clone $start_date)->modify('+' . ($tracking->duration_days - 1) . ' days');
        
            // Formato para almacenar resumen de actividades por día
            $days_summary = [];
        
            // Inicializa el array de días con fechas desde date_start hasta date_start + duration_days
            $current_date = clone $start_date;
            while ($current_date <= $end_date) {
                $date_string = $current_date->format('Y-m-d');
                $days_summary[$date_string] = [
                    'date' => $date_string,
                    'has_pending' => false,
                    'has_scheduled' => false,
                    'has_completed' => false,
                    'pending_count' => 0,
                    'scheduled_count' => 0,
                    'completed_count' => 0
                ];
                $current_date->modify('+1 day');
            }
        
            // Para cada actividad, actualiza el estado del día correspondiente
            foreach ($tracking->activities as $activity) {
                // Si la actividad no tiene una fecha específica, la omitimos
                if (empty($activity->fecha_creacion)) {
                    continue;
                }
                
                $activity_date = $activity->fecha_creacion; // Ya está en formato Y-m-d
                
                // Si la fecha no existe en days_summary, la agregamos
                if (!isset($days_summary[$activity_date])) {
                    $days_summary[$activity_date] = [
                        'date' => $activity_date,
                        'has_pending' => false,
                        'has_scheduled' => false,
                        'has_completed' => false,
                        'pending_count' => 0,
                        'scheduled_count' => 0,
                        'completed_count' => 0
                    ];
                }
                
                // Procesamos la actividad
                $status = strtolower($activity->status);
                
                if ($status === 'pendiente') {
                    $days_summary[$activity_date]['has_pending'] = true;
                    $days_summary[$activity_date]['pending_count']++;
                } elseif ($status === 'programado') {
                    $days_summary[$activity_date]['has_scheduled'] = true;
                    $days_summary[$activity_date]['scheduled_count']++;
                } elseif ($status === 'completado') {
                    $days_summary[$activity_date]['has_completed'] = true;
                    $days_summary[$activity_date]['completed_count']++;
                }
            }
            
            // Quitar las actividades del array antes de devolverlo
            unset($trackingArray['activities']);
            
            // Filtrar sólo los días que tengan al menos una actividad (pendiente, programada o completada)
            $filtered_days_summary = array_filter($days_summary, function($day) {
                return $day['has_pending'] || $day['has_scheduled'] || $day['has_completed'];
            });
            
            // Agregar el resumen de días filtrado al array del tracking
            $trackingArray['days_summary'] = array_values($filtered_days_summary);
            
            // Agregar contadores totales (calculados a partir del array original para mantener los totales correctos)
            $trackingArray['total_pending'] = array_sum(array_column($days_summary, 'pending_count'));
            $trackingArray['total_scheduled'] = array_sum(array_column($days_summary, 'scheduled_count'));
            $trackingArray['total_completed'] = array_sum(array_column($days_summary, 'completed_count'));
            
            $result[] = $trackingArray;
        }
        
        return response()->json($result);
    }

   
    /** * Obtiene trackings de un proyecto específico incluyendo los eliminados con soft delete */
    public function trackingByProjectWithTrashed($project_id){
        // Obtiene trackings incluyendo los eliminados con soft delete
        $trackings = Tracking::withTrashed()->with('activities')->where('project_id', $project_id)->get();
        
        $result = [];
        foreach ($trackings as $tracking) {
            // Convertir el tracking a un array para poder manipularlo
            $trackingArray = $tracking->toArray();
        
            // Calcula la fecha de finalización basada en date_start y duration_days
            $start_date = new \DateTime($tracking->date_start);
            $end_date = (clone $start_date)->modify('+' . ($tracking->duration_days - 1) . ' days');
        
            // Formato para almacenar resumen de actividades por día
            $days_summary = [];
        
            // Inicializa el array de días con fechas desde date_start hasta date_start + duration_days
            $current_date = clone $start_date;
            while ($current_date <= $end_date) {
                $date_string = $current_date->format('Y-m-d');
                $days_summary[$date_string] = [
                    'date' => $date_string,
                    'has_pending' => false,
                    'has_scheduled' => false,
                    'has_completed' => false,
                    'pending_count' => 0,
                    'scheduled_count' => 0,
                    'completed_count' => 0
                ];
                $current_date->modify('+1 day');
            }
        
            // Para cada actividad, actualiza el estado del día correspondiente
            foreach ($tracking->activities as $activity) {
                // Si la actividad no tiene una fecha específica, la omitimos
                if (empty($activity->fecha_creacion)) {
                    continue;
                }
                
                $activity_date = $activity->fecha_creacion; // Ya está en formato Y-m-d
                
                // Si la fecha no existe en days_summary, la agregamos
                if (!isset($days_summary[$activity_date])) {
                    $days_summary[$activity_date] = [
                        'date' => $activity_date,
                        'has_pending' => false,
                        'has_scheduled' => false,
                        'has_completed' => false,
                        'pending_count' => 0,
                        'scheduled_count' => 0,
                        'completed_count' => 0
                    ];
                }
                
                // Procesamos la actividad
                $status = strtolower($activity->status);
                
                if ($status === 'pendiente') {
                    $days_summary[$activity_date]['has_pending'] = true;
                    $days_summary[$activity_date]['pending_count']++;
                } elseif ($status === 'programado') {
                    $days_summary[$activity_date]['has_scheduled'] = true;
                    $days_summary[$activity_date]['scheduled_count']++;
                } elseif ($status === 'completado') {
                    $days_summary[$activity_date]['has_completed'] = true;
                    $days_summary[$activity_date]['completed_count']++;
                }
            }
            
            // Quitar las actividades del array antes de devolverlo
            unset($trackingArray['activities']);
            
            // Filtrar sólo los días que tengan al menos una actividad (pendiente, programada o completada)
            $filtered_days_summary = array_filter($days_summary, function($day) {
                return $day['has_pending'] || $day['has_scheduled'] || $day['has_completed'];
            });
            
            // Agregar el resumen de días filtrado al array del tracking
            $trackingArray['days_summary'] = array_values($filtered_days_summary);
            
            // Agregar contadores totales (calculados a partir del array original para mantener los totales correctos)
            $trackingArray['total_pending'] = array_sum(array_column($days_summary, 'pending_count'));
            $trackingArray['total_scheduled'] = array_sum(array_column($days_summary, 'scheduled_count'));
            $trackingArray['total_completed'] = array_sum(array_column($days_summary, 'completed_count'));
            
            $result[] = $trackingArray;
        }
        
        return response()->json($result);
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

    /** * Actualiza el título de un tracking específico */
    public function updateTrackingTitle(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $tracking = Tracking::findOrFail($id);
            $tracking->title = $validatedData['title'];
            $tracking->updated_at = now();
            $tracking->save();
            
            DB::commit();

            return response()->json([
                'message' => 'Título del tracking actualizado exitosamente.',
                'tracking' => $tracking
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar el título del tracking: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al actualizar el título del tracking',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /** * Elimina un tracking específico (soft delete) */
    public function deleteTracking($id, Request $request)
    {
        DB::beginTransaction();
        try {
            // Buscar el tracking
            $tracking = Tracking::findOrFail($id);
            
            // Usar timestamp del request o now() por defecto
            $deletedAt = $request->input('deleted_at', now());
            
            $tracking->deleted_at = $deletedAt;
            $tracking->save();
            
            DB::commit();
    
            return response()->json([
                'message' => 'Tracking eliminado exitosamente (soft delete).'
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al eliminar el tracking: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al eliminar el tracking',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /** * Restaura un tracking eliminado */
    public function restoreTracking($id)
    {
        DB::beginTransaction();
        try {
            // Buscar el tracking eliminado
            $tracking = Tracking::onlyTrashed()->findOrFail($id);
            
            // Restaurar el tracking
            $tracking->restore();
            
            DB::commit();

            return response()->json([
                'message' => 'Tracking restaurado exitosamente.',
                'tracking' => $tracking
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al restaurar el tracking: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al restaurar el tracking',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /** * Elimina permanentemente un tracking */
    public function forceDeleteTracking($id)
    {
        DB::beginTransaction();
        try {
            // Buscar el tracking (incluyendo eliminados)
            $tracking = Tracking::withTrashed()->findOrFail($id);
            
            // Eliminar permanentemente
            $tracking->forceDelete();
            
            DB::commit();

            return response()->json([
                'message' => 'Tracking eliminado permanentemente.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al eliminar permanentemente el tracking: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al eliminar permanentemente el tracking',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
