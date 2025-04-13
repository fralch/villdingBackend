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

            // Check for duplicates based on project_id, tracking_id, and name
            $existingActivity = Activity::where('project_id', $validatedData['project_id'])
                ->where('tracking_id', $validatedData['tracking_id'])
                ->where('name', $validatedData['name']);
                
                   
            $existingActivity = $existingActivity->first();
            
            if ($existingActivity) {
                return response()->json([
                    'message' => 'Ya existe una actividad con estos datos.',
                    'activity' => $existingActivity,
                ], 409); // 409 Conflict status code
            }

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
    
    function completeActivity(Request $request){
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $activity = Activity::findOrFail($id);
            $activity->status = 'completado';
            $activity->save();

            DB::commit();
            return response()->json(['message' => 'Actividad completada exitosamente.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al completar actividad: ' . $e->getMessage());
            return response()->json(['message' => 'Error al completar actividad', 'error' => $e->getMessage()], 500);
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