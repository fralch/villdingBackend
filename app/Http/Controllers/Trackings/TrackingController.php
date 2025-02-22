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
                'user_id'     => 'required|exists:users,id',
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'week_id'     => 'required|integer'
            ]);
    
            $tracking = Tracking::create([
                'week_id'     => $validatedData['week_id'],
                'project_id'  => $validatedData['project_id'],
                'user_id'     => $validatedData['user_id'],
                'title'       => $validatedData['title'],
                'description' => $validatedData['description'] ?? null,
                'status'      => true
            ]);
    
            DB::commit();
    
            return response()->json([
                'message'   => 'Tracking created successfully.',
                'tracking' => $tracking
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating tracking: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error creating tracking',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}


