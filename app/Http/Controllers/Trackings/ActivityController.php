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


class ActivityController extends Controller
{
    public function activityAll(){
        $activities = Activity::all();
        return response()->json($activities);
    }

    public function activityByWeekByProject($week_id, $project_id){
        $activities = Activity::where('week_id', $week_id)->where('project_id', $project_id)->get();
        return response()->json($activities);
    }
    
    public function activityByProject($project_id){
        // Obtener las semanas con sus actividades asociadas
        $weeks = Week::where('project_id', $project_id)
                     ->with('activities') // Asegúrate de que la relación esté definida en el modelo Week
                     ->get();
    
        return response()->json($weeks);
    }
    
   // Crear actividades para un proyecto específico
   public function createActivity(Request $request)
   {
       DB::beginTransaction();
       try {
           // Validar los datos de entrada
           $validatedData = $request->validate([
               'project_id' => 'required|exists:projects,id',
               'name' => 'required|string|max:255',
               'description' => 'nullable|string',
               'hour_start' => 'required|date_format:H:i',
               'hour_end' => 'required|date_format:H:i',
               'status' => 'required|string|max:255',
               'icon' => 'nullable|string',
           ]);

           $projectId = $validatedData['project_id'];
           $name = $validatedData['name'];
           $description = $validatedData['description'] ?? null;
           $hourStart = $validatedData['hour_start'];
           $hourEnd = $validatedData['hour_end'];
           $status = $validatedData['status'];
           $icon = $validatedData['icon'] ?? null;

           // Obtener todas las semanas del proyecto
           $weeks = Week::where('project_id', $projectId)->get();

           foreach ($weeks as $week) {
               // Obtener los días de la semana
               $days = $week->days;

               foreach ($days as $day) {
                   // Obtener los trackings asociados al proyecto y la semana
                   $trackings = Tracking::where('project_id', $projectId)
                                        ->where('week_id', $week->id)
                                        ->get();

                   foreach ($trackings as $tracking) {
                       // Crear una actividad para cada tracking en el día
                       $activity = new Activity([
                           'day_id' => $day->id,
                           'project_id' => $projectId,
                           'user_id' => $tracking->user_id,
                           'name' => $name,
                           'description' => $description,
                           'hour_start' => $hourStart,
                           'hour_end' => $hourEnd,
                           'status' => $status,
                           'icon' => $icon,
                       ]);

                       // Guardar la actividad en la base de datos
                       $activity->save();
                   }
               }
           }

           DB::commit();

           return response()->json([
               'message' => 'Actividades creadas exitosamente para el proyecto.'
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
        


}
