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
    /** * Obtiene todos los trackings */
    public function trackingAll(){
        $trackings = Tracking::all();
        return response()->json($trackings);
    }

   

    /** * Obtiene trackings de un proyecto específico  */
    public function trackingByProject($project_id){
        $trackings = Tracking::where('project_id', $project_id)->get();
        return response()->json($trackings);
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
}


