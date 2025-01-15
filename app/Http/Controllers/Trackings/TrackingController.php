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


    // obtener semanas de un proyecto
    public function getWeeksByProject($project_id){
        $weeks = Week::where('project_id', $project_id)->get();
        return response()->json($weeks);
    }

    // obtener dias de una semana
    public function getDaysByWeek($week_id){
        $days = Day::where('week_id', $week_id)->get();
        return response()->json($days);
    }

    // obtener dias de un proyecto
    public function getDaysByProject($project_id){
        $days = Day::where('project_id', $project_id)->get();
        return response()->json($days);
    }
    

    
   

    // crear tracking
    public function createTracking(Request $request){
        
        $project_id = $request->project_id;
        $user_id = $request->user_id;
        $title = $request->title;
        $description = $request->description ?? null;

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
                'date_start' => now()
            ]);
        }
        return response()->json(['message' => 'Tracking creado correctamente']);
    }
}


