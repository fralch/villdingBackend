<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function all()
    {
        //
        $projects = Project::all();
        return response()->json($projects);
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Procesar la imagen si se proporciona
        $imagePath = null;
        if ($request->hasFile('uri')) {
            $image = $request->file('uri');
            $imagePath = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/projects'), $imagePath);

        }
            // Crear el proyecto
            $project = Project::create([
                'name' => $request->name,
                'location' => $request->location,
                'company' => $request->company,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'uri' => $imagePath ? $imagePath : '',
                'project_type_id' => $request->project_type_id,
                'project_subtype_id' => $request->project_subtype_id                
            ]);

            return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Buscar el proyecto por ID
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

   
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        

        // Buscar el proyecto y actualizarlo
        $project = Project::findOrFail($id);
        $project->update($request->all());

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
          // Buscar el proyecto y eliminarlo
          $project = Project::findOrFail($id);
          $project->delete();
  
          return response()->json(['message' => 'Proyecto eliminado correctamente']);
    }
}
