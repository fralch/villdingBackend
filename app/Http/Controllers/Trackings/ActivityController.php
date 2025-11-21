<?php

namespace App\Http\Controllers\Trackings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
                'images' => 'nullable|array|max:10', // Limit to max 10 images
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
                'image' => !empty($imagePaths) ? $imagePaths : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            DB::commit();
            
            return response()->json([
                'message' => 'Actividad creada exitosamente.',
                'activity' => $activity,
                'image_paths' => $this->formatImageUrls($imagePaths),
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
            
            // Manejar imagenes
            $rawCurrentImages = json_decode($activity->getRawOriginal('image') ?? '[]', true) ?: [];
            $finalImagePaths = [];

            if ($request->has('existing_images')) {
                $existingImagesInput = $request->existing_images;
                if (is_string($existingImagesInput)) {
                    $existingImagesInput = json_decode($existingImagesInput, true) ?: [];
                }
                if (is_array($existingImagesInput)) {
                    $finalImagePaths = $this->normalizeImageReferences($existingImagesInput);
                }
            } elseif (!empty($rawCurrentImages)) {
                $finalImagePaths = $rawCurrentImages;
            }

            $finalImagePaths = array_values(array_unique(array_filter($finalImagePaths)));
            \Log::info('Imagenes existentes procesadas: ' . json_encode($finalImagePaths));

            $imagesToDelete = array_diff($rawCurrentImages, $finalImagePaths);
            foreach ($imagesToDelete as $imageKey) {
                $this->deleteImageFromStorage($imageKey);
            }

            $remainingSlots = max(0, 5 - count($finalImagePaths));
            if ($remainingSlots > 0) {
                $newImagePaths = $this->processImages($request, $remainingSlots);
                if (!empty($newImagePaths)) {
                    $finalImagePaths = array_merge($finalImagePaths, $newImagePaths);
                    \Log::info('Imagenes nuevas procesadas: ' . json_encode($newImagePaths));
                }
            }

            $finalImagePaths = array_slice($finalImagePaths, 0, 5);

            $validatedData['image'] = !empty($finalImagePaths) ? $finalImagePaths : null;

            // Actualizar fecha de modificacion
            $validatedData['updated_at'] = now();

            // Update the activity
            $activity->update($validatedData);
            \Log::info('Actividad actualizada correctamente con imagenes: ' . $activity->id);

            DB::commit();

            return response()->json([
                'message' => 'Actividad actualizada exitosamente con imagenes.',
                'activity' => $activity,
                'image_paths' => $this->formatImageUrls($finalImagePaths),
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

    private function processImages(Request $request, int $maxImages = 5)
    {
        \Log::info('Iniciando procesamiento de imagenes');
        $imagePaths = [];

        if ($maxImages <= 0) {
            \Log::info('No hay espacio disponible para nuevas imagenes');
            return $imagePaths;
        }

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            $images = is_array($images) ? $images : [$images];

            \Log::info('Numero de imagenes a procesar: ' . count($images));

            $images = array_slice($images, 0, $maxImages);

            foreach ($images as $index => $image) {
                $storedPath = $this->storeImageOnS3($image);
                if ($storedPath) {
                    $imagePaths[] = $storedPath;
                    \Log::info('Imagen guardada en S3: ' . $storedPath);
                } else {
                    \Log::warning('Imagen no valida en indice: ' . $index);
                }
            }
        } elseif ($request->hasFile('image')) {
            $image = $request->file('image');
            $storedPath = $this->storeImageOnS3($image);
            if ($storedPath) {
                $imagePaths[] = $storedPath;
                \Log::info('Imagen unica guardada en S3: ' . $storedPath);
            }
        } else {
            \Log::info('No se encontraron imagenes para procesar');
        }

        return $imagePaths;
    }

    private function storeImageOnS3($image): ?string
    {
        if (!$image || !$image->isValid()) {
            return null;
        }

        $fileName = Str::uuid()->toString() . '.' . $image->getClientOriginalExtension();

        try {
            $storedPath = Storage::disk('s3')->putFileAs('activities', $image, $fileName);
            return $storedPath;
        } catch (\Exception $e) {
            \Log::error('Error subiendo imagen a S3: ' . $e->getMessage());
            throw new \Exception('Error processing image: ' . $e->getMessage());
        }
    }

    private function normalizeImageReferences(array $images): array
    {
        return array_values(array_filter(array_map(function ($value) {
            if (is_array($value)) {
                return null;
            }

            $value = trim((string) $value);
            if ($value === '') {
                return null;
            }

            if (filter_var($value, FILTER_VALIDATE_URL)) {
                $path = parse_url($value, PHP_URL_PATH) ?? '';
                $path = ltrim($path, '/');

                if (Str::contains($path, 'images/activities/')) {
                    $segments = explode('images/activities/', $path);
                    return end($segments);
                }

                return $path;
            }

            return $value;
        }, $images)));
    }

    private function deleteImageFromStorage(string $imageKey): void
    {
        $imageKey = trim($imageKey);
        if ($imageKey === '') {
            return;
        }

        if (Storage::disk('s3')->exists($imageKey)) {
            Storage::disk('s3')->delete($imageKey);
            \Log::info('Imagen eliminada de S3: ' . $imageKey);
            return;
        }

        $localPath = public_path('images/activities/' . basename($imageKey));
        if (file_exists($localPath)) {
            unlink($localPath);
            \Log::info('Imagen eliminada de almacenamiento local: ' . $localPath);
        }
    }

    private function formatImageUrls(?array $imagePaths): array
    {
        if (empty($imagePaths)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($path) {
            if (!$path) {
                return null;
            }

            $path = trim($path);
            if ($path === '') {
                return null;
            }

            if (Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }

            if (Str::startsWith($path, 'images/activities/')) {
                return asset($path);
            }

            if (Str::contains($path, '/')) {
                return Storage::disk('s3')->url($path);
            }

            return asset('images/activities/' . ltrim($path, '/'));
        }, $imagePaths)));
    }

    public function deleteActivity($id)
    {
        DB::beginTransaction();
        try {
            // Find the activity
            $activity = Activity::findOrFail($id);

            // Delete associated images from storage
            $storedImages = json_decode($activity->getRawOriginal('image') ?? '[]', true) ?: [];
            foreach ($storedImages as $imagePath) {
                $this->deleteImageFromStorage($imagePath);
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
