<?php

namespace App\Http\Controllers\Trackings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Tracking;
use App\Models\Project;
use App\Models\User;
use App\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;

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
            
            // Soft delete activities
            $tracking->activities()->delete();

            // Si se proporciona una fecha específica, validarla o usarla
            if ($request->has('deleted_at')) {
                $tracking->deleted_at = $request->input('deleted_at');
                $tracking->save();
            } else {
                // Si no, usar el método delete() estándar que usa now()
                $tracking->delete();
            }
            
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

            // Restaurar activities individually
            $activities = $tracking->activities()->withTrashed()->get();
            foreach($activities as $activity) {
                $activity->restore();
            }

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

            // Eliminar actividades asociadas primero y sus imagenes
            $activities = $tracking->activities()->withTrashed()->get();
            foreach ($activities as $activity) {
                 // Delete images
                 $storedImages = json_decode($activity->getRawOriginal('image') ?? '[]', true) ?: [];
                 foreach ($storedImages as $imagePath) {
                      if (Storage::disk('s3')->exists($imagePath)) {
                         Storage::disk('s3')->delete($imagePath);
                     } else {
                         $localPath = public_path('images/activities/' . basename($imagePath));
                         if (file_exists($localPath)) {
                             unlink($localPath);
                         }
                     }
                 }
                 $activity->forceDelete();
            }

            // Eliminar permanentemente el tracking
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

    /** * Genera un reporte diario de actividades en PDF
     * @param int $tracking_id ID del tracking
     * @param string $date Fecha del reporte (formato Y-m-d)
     */
    public function generateDailyReport(Request $request, $tracking_id)
    {
        // Validar la fecha (esto lanzará ValidationException si falla)
        $validatedData = $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
        ]);

        try {
            // Incrementar límites para producción
            ini_set('max_execution_time', 300); // 5 minutos
            ini_set('memory_limit', '512M');

            $reportDate = $validatedData['date'];

            // Obtener el tracking con sus relaciones (incluyendo eliminados)
            $tracking = Tracking::withTrashed()
                ->with(['project.type', 'project.subtype'])
                ->findOrFail($tracking_id);

            // Obtener las actividades del día específico
            $activities = Activity::where('tracking_id', $tracking_id)
                ->whereDate('fecha_creacion', $reportDate)
                ->orderBy('created_at', 'asc')
                ->get();

            // Calcular el número de semana basado en date_start del tracking
            $startDate = \Carbon\Carbon::parse($tracking->date_start);
            $currentDate = \Carbon\Carbon::parse($reportDate);
            $weekNumber = $startDate->diffInWeeks($currentDate) + 1;

            // Formatear la fecha para el reporte (con fallback si locale español no está disponible)
            try {
                $formattedDate = \Carbon\Carbon::parse($reportDate)
                    ->locale('es')
                    ->isoFormat('dddd, D [de] MMMM [de] YYYY');
            } catch (\Exception $e) {
                // Fallback manual si locale español no está disponible
                \Log::warning('Locale español no disponible, usando formato alternativo');
                $months = [
                    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
                ];
                $days = [
                    0 => 'domingo', 1 => 'lunes', 2 => 'martes', 3 => 'miércoles',
                    4 => 'jueves', 5 => 'viernes', 6 => 'sábado'
                ];
                $date = \Carbon\Carbon::parse($reportDate);
                $dayName = $days[$date->dayOfWeek];
                $day = $date->day;
                $monthName = $months[$date->month];
                $year = $date->year;
                $formattedDate = ucfirst($dayName) . ', ' . $day . ' de ' . $monthName . ' de ' . $year;
            }

            // Preparar los datos para la vista
            $data = [
                'tracking' => $tracking,
                'project' => $tracking->project,
                'activities' => $activities,
                'reportDate' => $reportDate,
                'formattedDate' => $formattedDate,
                'weekNumber' => str_pad($weekNumber, 3, '0', STR_PAD_LEFT),
            ];

            // Generar el PDF con manejo mejorado de errores
            $pdf = Pdf::loadView('reports.daily-activity-report', $data);

            // Configurar el PDF con opciones de producción
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            // Opciones adicionales para producción
            $pdf->setOption('chroot', [storage_path('app/public'), public_path()]);
            $pdf->setOption('enable_remote', true);
            $pdf->setOption('defaultFont', 'Helvetica');

            // Timeout para carga de imágenes externas
            $pdf->setOption('httpContext', [
                'http' => [
                    'timeout' => 30,
                    'ignore_errors' => true
                ]
            ]);

            // Nombre del archivo (sanitizar nombre del proyecto)
            $projectName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $tracking->project->name);
            $fileName = 'reporte_diario_' . $projectName . '_' . $reportDate . '.pdf';

            // Retornar el PDF para descarga
            return $pdf->download($fileName);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Tracking no encontrado: ' . $tracking_id);
            return response()->json([
                'message' => 'Tracking no encontrado'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error al generar reporte diario: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Tracking ID: ' . $tracking_id);
            \Log::error('Report Date: ' . ($reportDate ?? 'N/A'));

            return response()->json([
                'message' => 'Error al generar reporte diario',
                'error'   => $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : 'Enable debug mode for details'
            ], 500);
        }
    }

    /**
     * Genera un reporte de múltiples trackings con sus fechas
     */
    public function generateMultiReport(Request $request)
    {
        $validatedData = $request->validate([
            'report_data' => 'required|array',
            'report_data.*.tracking_id' => 'required|exists:trackings,id',
            'report_data.*.date' => 'required|date|date_format:Y-m-d',
        ]);

        try {
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', '512M');

            $reports = [];

            foreach ($validatedData['report_data'] as $data) {
                $trackingId = $data['tracking_id'];
                $reportDate = $data['date'];

                // Obtener el tracking con sus relaciones
                $tracking = Tracking::withTrashed()
                    ->with(['project.type', 'project.subtype'])
                    ->findOrFail($trackingId);

                // Obtener las actividades del día específico
                $activities = Activity::where('tracking_id', $trackingId)
                    ->whereDate('fecha_creacion', $reportDate)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Formatear fecha
                try {
                    $formattedDate = \Carbon\Carbon::parse($reportDate)
                        ->locale('es')
                        ->isoFormat('dddd, D [de] MMMM [de] YYYY');
                } catch (\Exception $e) {
                    $months = [
                        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
                    ];
                    $days = [
                        0 => 'domingo', 1 => 'lunes', 2 => 'martes', 3 => 'miércoles',
                        4 => 'jueves', 5 => 'viernes', 6 => 'sábado'
                    ];
                    $date = \Carbon\Carbon::parse($reportDate);
                    $dayName = $days[$date->dayOfWeek];
                    $day = $date->day;
                    $monthName = $months[$date->month];
                    $year = $date->year;
                    $formattedDate = ucfirst($dayName) . ', ' . $day . ' de ' . $monthName . ' de ' . $year;
                }

                // Calcular el número de semana basado en date_start del tracking
                $startDate = \Carbon\Carbon::parse($tracking->date_start);
                $currentDate = \Carbon\Carbon::parse($reportDate);
                $weekNumber = $startDate->diffInWeeks($currentDate) + 1;

                $reports[] = [
                    'tracking' => $tracking,
                    'project' => $tracking->project,
                    'activities' => $activities,
                    'reportDate' => $reportDate,
                    'formattedDate' => $formattedDate,
                    'weekNumber' => str_pad($weekNumber, 3, '0', STR_PAD_LEFT),
                ];
            }

            $pdf = Pdf::loadView('reports.multi-activity-report', ['reports' => $reports]);

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('chroot', [storage_path('app/public'), public_path()]);
            $pdf->setOption('enable_remote', true);
            $pdf->setOption('defaultFont', 'Helvetica');
            $pdf->setOption('httpContext', [
                'http' => [
                    'timeout' => 30,
                    'ignore_errors' => true
                ]
            ]);

            $fileName = 'reporte_multiple_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            \Log::error('Error al generar reporte múltiple: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al generar reporte múltiple',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
