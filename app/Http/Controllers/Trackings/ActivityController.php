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

    public function activityByTracking($tracking_id)
    {
        // Obtener las actividades asociadas a la actividad
        $activities = Activity::where('tracking_id', $tracking_id)->get();
        return response()->json($activities);
    }
    
    public function createActivity(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the input data
            $validatedData = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'tracking_id' => 'required|exists:trackings,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'horas' => 'nullable|string',
                'status' => 'nullable|string',
                'icon' => 'nullable|string',
                'images' => 'nullable|array|max:5', // Limit to max 5 images
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'comments' => 'nullable|string',
                'fecha_creacion' => 'nullable|date',
            ]);

            // Establecer un valor predeterminado para 'horas' si es null
            $validatedData['horas'] = $validatedData['horas'] ?? '0';

            // Duplicate validation removed - activities can now have the same name

            // Set timezone to Lima, Peru
            date_default_timezone_set('America/Lima');
            
            // Check if fecha_creacion is after today
            if (isset($validatedData['fecha_creacion'])) {
                $activityDate = \Carbon\Carbon::parse($validatedData['fecha_creacion']);
                $today = \Carbon\Carbon::now('America/Lima')->startOfDay();
                
                if ($activityDate->gt($today)) {
                    $validatedData['status'] = 'programado';
                }
            }

            // Process images
            $imagePaths = $this->processImages($request);

            // Create the activity
            $activity = Activity::create(array_merge($validatedData, [
                'image' => $imagePaths,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            DB::commit();
            
            return response()->json([
                'message' => 'Actividad creada exitosamente.',
                'activity' => $activity,
                'image_paths' => array_map(function($path) {
                    return asset('images/activities/' . $path);
                }, json_decode($imagePaths) ?: []),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear actividad: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al crear actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualiza una actividad sin imágenes (usando JSON)
     */
    public function updateActivity(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            \Log::info('Iniciando actualización básica de actividad ID: ' . $id);
            \Log::info('Datos recibidos: ' . json_encode($request->all()));
            
            // Find the activity
            $activity = Activity::findOrFail($id);
            \Log::info('Actividad encontrada: ' . $activity->id . ' - ' . $activity->name);
            
            // Validate the input data - sin campos de imágenes
            $validatedData = $request->validate([
                'project_id' => 'sometimes|required|exists:projects,id',
                'tracking_id' => 'sometimes|required|exists:trackings,id',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'horas' => 'nullable|string',
                'status' => 'nullable|string',
                'icon' => 'nullable|string',
                'comments' => 'nullable|string',
                'fecha_creacion' => 'nullable|date',
            ]);
            \Log::info('Datos validados: ' . json_encode($validatedData));
            
            // Duplicate validation removed - activities can now have the same name during updates

            // Mantener las imágenes actuales
            // Nota: No se modifican las imágenes en esta función
            
            // Actualizar fecha de modificación
            $validatedData['updated_at'] = now();

            // Update the activity
            $activity->update($validatedData);
            \Log::info('Actividad actualizada correctamente: ' . $activity->id);

            DB::commit();

            return response()->json([
                'message' => 'Actividad actualizada exitosamente.',
                'activity' => $activity,
                'image_paths' => array_map(function($path) {
                    return asset('images/activities/' . $path);
                }, json_decode($activity->image) ?: []),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar actividad: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Error al actualizar actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualiza una actividad con imágenes (usando FormData)
     */
    public function updateActivityWithImages(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            \Log::info('Iniciando actualización de actividad con imágenes ID: ' . $id);
            \Log::info('Datos recibidos: ' . json_encode($request->except(['images', 'image'])));
            
            // Find the activity
            $activity = Activity::findOrFail($id);
            \Log::info('Actividad encontrada: ' . $activity->id . ' - ' . $activity->name);
            
            // Validar datos básicos (no incluimos validación de imágenes todavía)
            $validatedData = $request->validate([
                'project_id' => 'sometimes|required|exists:projects,id',
                'tracking_id' => 'sometimes|required|exists:trackings,id',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'horas' => 'nullable|string',
                'status' => 'nullable|string',
                'icon' => 'nullable|string',
                'comments' => 'nullable|string',
                'fecha_creacion' => 'nullable|date',
            ]);
            
            // Verificar si se está intentando cambiar el nombre
            if (isset($validatedData['name']) && $validatedData['name'] !== $activity->name) {
                \Log::info('Verificando duplicados para el nuevo nombre: ' . $validatedData['name']);
                
                // Determinar project_id y tracking_id a usar para la verificación
                $projectId = $validatedData['project_id'] ?? $activity->project_id;
                $trackingId = $validatedData['tracking_id'] ?? $activity->tracking_id;
                
                // Verificar si ya existe otra actividad con el mismo nombre (excluyendo la actual)
                $existingActivity = Activity::where('project_id', $projectId)
                    ->where('tracking_id', $trackingId)
                    ->where('name', $validatedData['name'])
                    ->where('id', '!=', $id) // Excluir la actividad actual
                    ->first();
                
                if ($existingActivity) {
                    \Log::warning('Se encontró una actividad existente con el mismo nombre: ' . $existingActivity->id);
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Ya existe otra actividad con este nombre.',
                        'activity' => $existingActivity,
                    ], 409); // 409 Conflict status code
                }
            }
            
            // Manejar imágenes
            $finalImagePaths = [];
            
            // Procesar imágenes existentes enviadas desde el cliente
            // Esto viene como JSON string del campo 'existing_images'
            if ($request->has('existing_images')) {
                if (is_string($request->existing_images)) {
                    $existingImages = json_decode($request->existing_images, true);
                    if (is_array($existingImages)) {
                        $finalImagePaths = $existingImages;
                    }
                } else if (is_array($request->existing_images)) {
                    $finalImagePaths = $request->existing_images;
                }
            } 
            // Si no hay existing_images especificadas, mantenemos las actuales
            else if (empty($finalImagePaths) && $activity->image) {
                $currentImages = json_decode($activity->image, true);
                if (is_array($currentImages)) {
                    $finalImagePaths = $currentImages;
                }
            }
            
            \Log::info('Imágenes existentes procesadas: ' . json_encode($finalImagePaths));
            
            // Procesar nuevas imágenes si existen
            $newImagePaths = $this->processImages($request);
            if ($newImagePaths) {
                $newImagePathsArray = json_decode($newImagePaths, true) ?: [];
                $finalImagePaths = array_merge($finalImagePaths, $newImagePathsArray);
                \Log::info('Imágenes nuevas procesadas: ' . json_encode($newImagePathsArray));
            }
            
            // Guardar la ruta de imágenes final en formato JSON
            $validatedData['image'] = !empty($finalImagePaths) ? json_encode($finalImagePaths) : null;
            
            // Actualizar fecha de modificación
            $validatedData['updated_at'] = now();

            // Update the activity
            $activity->update($validatedData);
            \Log::info('Actividad actualizada correctamente con imágenes: ' . $activity->id);

            DB::commit();

            return response()->json([
                'message' => 'Actividad actualizada exitosamente con imágenes.',
                'activity' => $activity,
                'image_paths' => array_map(function($path) {
                    return asset('images/activities/' . $path);
                }, json_decode($activity->image) ?: []),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar actividad con imágenes: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Error al actualizar actividad con imágenes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateActivityStatusByDate(Request $request, $project_id)
    {
        DB::beginTransaction();
        try {
            $activities = Activity::where('project_id', $project_id)->get();

            if ($activities->isEmpty()) {
                DB::rollBack();
                return response()->json(['message' => 'No se encontraron actividades para el proyecto especificado.'], 404);
            }

            $updatedActivities = [];
            $errors = [];

            // Set timezone to Lima, Peru
            date_default_timezone_set('America/Lima');
            $today = \Carbon\Carbon::now('America/Lima')->startOfDay();

            foreach ($activities as $activity) {
                if (!$activity->fecha_creacion) {
                    $errors[] = ['activity_id' => $activity->id, 'error' => 'La actividad no tiene una fecha de creación definida.'];
                    continue; // Skip this activity
                }
                
                // No cambiar el estado si ya está completado
                if ($activity->status === 'completado') {
                    continue; // Mantener el estado completado
                }

                $activityDate = \Carbon\Carbon::parse($activity->fecha_creacion)->startOfDay();

                if ($activityDate->lte($today)) {
                    $activity->status = 'pendiente';
                } else {
                    $activity->status = 'programado';
                }

                $activity->updated_at = now();
                $activity->save();
                $updatedActivities[] = $activity;
            }

            DB::commit();
            
            $responseMessage = 'Estado de las actividades actualizado exitosamente.';
            if (!empty($errors)) {
                $responseMessage .= ' Algunas actividades no pudieron ser actualizadas.';
            }

            return response()->json([
                'message' => $responseMessage,
                'updated_activities' => $updatedActivities,
                'errors' => $errors
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar estado de actividades por proyecto: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al actualizar estado de actividades por proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    function completeActivity(Request $request){
        DB::beginTransaction();
        try {
            $id = intval($request->input('id'));
            // Añade un log para ver qué valor llega realmente
            \Log::info('ID a buscar:', ['id' => $id]);
            
            $activity = Activity::findOrFail($id);
            
            
            // Log activity found for debugging
            \Log::info('Activity found:', [
                'id' => $activity->id,
                'name' => $activity->name,
                'current_status' => $activity->status
            ]);
            
            $activity->status = 'completado';
            $activity->save();
            
            // Log after status update
            \Log::info('Activity status updated:', [
                'id' => $activity->id,
                'new_status' => $activity->status
            ]);

            DB::commit();
            return response()->json(['message' => 'Actividad completada exitosamente.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al completar actividad: ' . $e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        
    }

    private function processImages(Request $request)
    {
        \Log::info('Iniciando procesamiento de imágenes');
        $imagePaths = [];
        
        // Handle 'images' array from form-data (from React Native)
        if ($request->hasFile('images')) {
            $images = is_array($request->file('images')) ? 
                $request->file('images') : [$request->file('images')];
            
            \Log::info('Número de imágenes a procesar: ' . count($images));
            
            // Ensure we only process up to 5 images
            $images = array_slice($images, 0, 5);
    
            foreach ($images as $index => $image) {
                try {
                    if ($image && $image->isValid()) {
                        \Log::info('Procesando imagen #' . ($index + 1));
                        $imagePath = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('images/activities'), $imagePath);
                        $imagePaths[] = $imagePath;
                        \Log::info('Imagen guardada en: ' . $imagePath);
                    } else {
                        \Log::warning('Imagen no válida o no proporcionada en índice: ' . $index);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error procesando imagen #' . ($index + 1) . ': ' . $e->getMessage());
                    throw new \Exception('Error processing image: ' . $e->getMessage());
                }
            }
        }
        // Handle single 'image' field (fallback)
        else if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                if ($image && $image->isValid()) {
                    \Log::info('Procesando imagen única');
                    $imagePath = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/activities'), $imagePath);
                    $imagePaths[] = $imagePath;
                    \Log::info('Imagen guardada en: ' . $imagePath);
                }
            } catch (\Exception $e) {
                \Log::error('Error procesando imagen única: ' . $e->getMessage());
                throw new \Exception('Error processing image: ' . $e->getMessage());
            }
        } else {
            \Log::info('No se encontraron imágenes para procesar');
        }
        
        $result = !empty($imagePaths) ? json_encode($imagePaths) : null;
        \Log::info('Finalizado procesamiento de imágenes. Resultado: ' . $result);
        return $result;
    }

    public function deleteActivity($id)
    {
        DB::beginTransaction();
        try {
            // Find the activity
            $activity = Activity::findOrFail($id);

            // Delete associated images from storage
            if ($activity->image) {
                $images = json_decode($activity->image, true);
                if (is_array($images)) {
                    foreach ($images as $imagePath) {
                        $fullPath = public_path('images/activities/' . $imagePath);
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                }
            }

            // Delete the activity
            $activity->delete();

            DB::commit();
            return response()->json(['message' => 'Actividad eliminada exitosamente.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al eliminar actividad: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al eliminar actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}