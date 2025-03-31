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
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // For handling multiple images
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',    // Keep original validation
                'comments' => 'nullable|string',
                'fecha_creacion' => 'nullable|date',
            ]);

            // Ensure the storage directory exists
            $activityImagePath = public_path('images/activities');
            if (!file_exists($activityImagePath)) {
                mkdir($activityImagePath, 0755, true);
            }

            // Process the images and get paths as JSON string
            $imagePaths = $this->processImages($request);

            // Create the activity
            $activity = Activity::create(array_merge($validatedData, [
                'image' => $imagePaths, // Store all image paths as JSON in the 'image' column
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            DB::commit();
            
            // For response, decode the JSON to get array of paths
            $imagePathArray = json_decode($imagePaths) ?: [];

            return response()->json([
                'message' => 'Actividad creada exitosamente para el proyecto.',
                'activity' => $activity,
                'image_paths' => array_map(function($path) {
                    return asset('images/activities/' . $path);
                }, $imagePathArray),
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'comments' => 'nullable|string',
            'fecha_creacion' => 'nullable|date',
        ]);

        // Get existing images
        $existingImages = json_decode($activity->image, true) ?: [];
        if (!is_array($existingImages)) {
            // Handle case where existing image is a single path string
            $existingImages = [$activity->image];
        }
        
        // Process new images if provided
        $newImagePaths = [];
        if ($request->hasFile('images')) {
            // Process multiple images
            foreach ($request->file('images') as $image) {
                $imagePath = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/activities'), $imagePath);
                $newImagePaths[] = $imagePath;
            }
        } else if ($request->hasFile('image')) {
            // Process single image
            $image = $request->file('image');
            $imagePath = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/activities'), $imagePath);
            $newImagePaths[] = $imagePath;
        }
        
        // Merge existing and new images if needed
        if (!empty($newImagePaths)) {
            $allImagePaths = array_merge($existingImages, $newImagePaths);
            $validatedData['image'] = json_encode($allImagePaths);
        }

        // Update the activity
        $activity->update($validatedData);

        DB::commit();

        $responseImagePaths = json_decode($activity->image) ?: [];
        if (!is_array($responseImagePaths)) {
            $responseImagePaths = [$activity->image];
        }

        return response()->json([
            'message' => 'Actividad actualizada exitosamente.',
            'activity' => $activity,
            'image_paths' => array_map(function($path) {
                return asset('images/activities/' . $path);
            }, $responseImagePaths),
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

    private function processImages(Request $request)
    {
        $imagePaths = [];
        
        // Process multiple images if present
        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $image) {
                    $imagePath = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/activities'), $imagePath);
                    $imagePaths[] = $imagePath;
                }
            } catch (\Exception $e) {
                \Log::error('Error processing multiple images: ' . $e->getMessage());
                throw new \Exception('Error processing multiple images: ' . $e->getMessage());
            }
        } 
        // Process single image if present (for backward compatibility)
        else if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $imagePath = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/activities'), $imagePath);
                $imagePaths[] = $imagePath;
            } catch (\Exception $e) {
                \Log::error('Error processing image: ' . $e->getMessage());
                throw new \Exception('Error processing image: ' . $e->getMessage());
            }
        }
        
        // Return JSON string of image paths
        return !empty($imagePaths) ? json_encode($imagePaths) : null;
    }
}