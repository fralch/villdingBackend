<?php

namespace App\Http\Controllers\Trackings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tracking;
use App\Models\Day;
use App\Models\Week;
use App\Models\Project;
use App\Models\User;
use App\Models\Activity;


class TrackingController extends Controller
{
    public function trackingAll(){
        $trackings = Tracking::all();
        return response()->json($trackings);
    }

    public function trackingByWeekByProject($week_id, $project_id){
        $trackings = Tracking::where('week_id', $week_id)->where('project_id', $project_id)->get();
        return response()->json($trackings);
    }

    public function trackingByWeekByProjectByUser($week_id, $project_id, $user_id){
        $trackings = Tracking::where('week_id', $week_id)->where('project_id', $project_id)->where('user_id', $user_id)->get();
        return response()->json($trackings);
    }


    
   

    // crear tracking
    public function createTracking(Request $request){
        
        $project_id = $request->project_id;
        $user_id = $request->user_id;
        $title = $request->title;
        $description = $request->description;

        // obtener las semanas de un proyecto
        $weeks = Week::where('project_id', $project_id)->get();

        // crear los trackings de las semanas
        foreach($weeks as $week){
            Tracking::create([
                'week_id' => $week->id,
                'project_id' => $project_id,
                'user_id' => $user_id,
                'title' => $title,
                'description' => $description,
                'date' => now(),
            ]);
        }
        return response()->json(['message' => 'Tracking creado correctamente']);
    }
}


