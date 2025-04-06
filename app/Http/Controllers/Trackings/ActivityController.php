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
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'tracking_id' => 'required|exists:trackings,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'horas' => 'nullable|string',
                'status' => 'nullable|string',
                'icon' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'comments' => 'nullable|string',
                'fecha_creacion' => 'nullable|date',
            ]);

            // Ensure the storage directory exists
            $activityImagePath = public_path('images/activities');
            if (!file_exists($activityImagePath)) {
                mkdir($activityImagePath, 0755, true);
            }

            // Process the image if provided
            $imagePath = $this->processImage($request);

            // Create the activity
            $activity = Activity::create(array_merge($validatedData, [
                'image' => $imagePath,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            DB::commit();

            return response()->json([
                'message' => 'Actividad creada exitosamente para el proyecto.',
                'activity' => $activity,
                'image_path' => $imagePath ? asset('images/activities/' . $imagePath) : null,
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
                'project_id' => 'required|exists:projects,id',
                'tracking_id' => 'required|exists:trackings,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'horas' => 'nullable|string',
                'status' => 'nullable|string',
                'icon' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'images_to_delete' => 'nullable|array',
                'comments' => 'nullable|string',
                'fecha_creacion' => 'nullable|date',
            ]);

            // Prepare update data
            $updateData = array_merge($validatedData, [
                'updated_at' => now(),
            ]);

            // Para manejar la eliminación de imágenes
            if ($request->has('images_to_delete')) {
                $currentImages = json_decode($activity->images ?? '[]', true);
                foreach ($request->images_to_delete as $imageToDelete) {
                    if (in_array($imageToDelete, $currentImages)) {
                        // Elimina el archivo físico
                        $imagePath = public_path('images/activities/' . $imageToDelete);
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                        // Elimina de la lista de imágenes
                        $currentImages = array_diff($currentImages, [$imageToDelete]);
                    }
                }
                $updateData['images'] = json_encode(array_values($currentImages));
            }

            // Para manejar la subida de nuevas imágenes
            if ($request->hasFile('images')) {
                $newImages = $this->processImages($request);
                $currentImages = json_decode($activity->images ?? '[]', true);
                $allImages = array_merge($currentImages, json_decode($newImages, true));
                $updateData['images'] = json_encode($allImages);
            }

            // Update the activity
            $activity->update($updateData);
            
            DB::commit();

            return response()->json([
                'message' => 'Actividad actualizada exitosamente.',
                'activity' => $activity,
                'images' => json_decode($activity->images ?? '[]'),
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

    private function processImage(Request $request)
    {
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $imagePath = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/activities'), $imagePath);
                return $imagePath;
            } catch (\Exception $e) {
                \Log::error('Error processing image: ' . $e->getMessage());
                throw new \Exception('Error processing image: ' . $e->getMessage());
            }
        }
        return null;
    }

    private function processImages(Request $request)
    {
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                try {
                    $imagePath = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/activities'), $imagePath);
                    $imagePaths[] = $imagePath;
                } catch (\Exception $e) {
                    \Log::error('Error processing image: ' . $e->getMessage());
                    throw new \Exception('Error processing image: ' . $e->getMessage());
                }
            }
        }
        return json_encode($imagePaths);
    }
}