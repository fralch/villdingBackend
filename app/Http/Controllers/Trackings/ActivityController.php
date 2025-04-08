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
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'comments' => 'nullable|string',
                'fecha_creacion' => 'nullable|date',
            ]);

            // Set timezone to Lima, Peru
            date_default_timezone_set('America/Lima');
            
            // Check if fecha_creacion is after today
            if (isset($validatedData['fecha_creacion'])) {
                $activityDate = \Carbon\Carbon::parse($validatedData['fecha_creacion']);
                $today = \Carbon\Carbon::now('America/Lima')->startOfDay();
                
                if ($activityDate->gt($today)) {
                    $validatedData['status'] = 'completado';
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
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'comments' => 'nullable|string',
                'fecha_creacion' => 'nullable|date',
            ]);

            // Process new images if provided
            if ($request->hasFile('images') || $request->hasFile('image')) {
                $newImagePaths = $this->processImages($request);
                
                // Merge with existing images if any
                $existingImages = json_decode($activity->image, true) ?: [];
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

    private function processImages(Request $request)
    {
        $imagePaths = [];
        
        // Handle both 'images' (multiple) and 'image' (single) uploads
        $images = $request->hasFile('images') ? $request->file('images') : 
                 ($request->hasFile('image') ? [$request->file('image')] : []);

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
}