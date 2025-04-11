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
    
    public function updateActivity(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Find the activity
            $activity = Activity::findOrFail($id);
            
            // Validate the input data
            $validatedData = $request->validate([
                'project_id' => 'sometimes|required|exists:projects,id',
                'tracking_id' => 'sometimes|required|exists:trackings,id',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'horas' => 'nullable|string',
                'status' => 'nullable|string',
                'icon' => 'nullable|string',
                'images' => 'sometimes|nullable|array|max:5',
                'images.*' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'comments' => 'nullable|string',
                'fecha_creacion' => 'nullable|date',
            ]);

            // Process new images if provided
            if ($request->hasFile('images') || $request->hasFile('image')) {
                // Get existing images
                $existingImages = json_decode($activity->image, true) ?: [];
                
                // Check if adding new images would exceed the limit of 5
                $newImageCount = $request->hasFile('images') ? count($request->file('images')) : 
                                ($request->hasFile('image') ? 1 : 0);
                
                $totalImageCount = count($existingImages) + $newImageCount;
                
                if ($totalImageCount > 5) {
                    return response()->json([
                        'message' => 'No se pueden agregar más imágenes. El límite es de 5 imágenes por actividad.',
                    ], 422);
                }
                
                $newImagePaths = $this->processImages($request);
                
                // Merge with existing images
                $allImages = array_merge($existingImages, json_decode($newImagePaths, true) ?: []);
                $validatedData['image'] = json_encode($allImages);
            }

            // Update the activity
            $activity->update($validatedData);

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
            return response()->json([
                'message' => 'Error al actualizar actividad',
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
        $imagePaths = [];
        
        // Handle both 'images' (multiple) and 'image' (single) uploads
        $images = $request->hasFile('images') ? $request->file('images') : 
                 ($request->hasFile('image') ? [$request->file('image')] : []);
        
        // Ensure we only process up to 5 images
        $images = array_slice($images, 0, 5);

        foreach ($images as $image) {
            try {
                $imagePath = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/activities'), $imagePath);
                $imagePaths[] = $imagePath;
            } catch (\Exception $e) {
                \Log::error('Error processing image: ' . $e->getMessage());
                throw new \Exception('Error processing image: ' . $e->getMessage());
            }
        }
        
        return !empty($imagePaths) ? json_encode($imagePaths) : null;
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