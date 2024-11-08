<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectType;

class ProjectTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         // Obtiene todos los tipos de proyecto y los retorna
         $projectTypes = ProjectType::all();
         return response()->json($projectTypes);
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Crear el tipo de proyecto
        $projectType = ProjectType::create($request->all());

        return response()->json($projectType, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Buscar el tipo de proyecto por ID
        $projectType = ProjectType::findOrFail($id);
        return response()->json($projectType);
    }

   
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Buscar el tipo de proyecto y actualizarlo
        $projectType = ProjectType::findOrFail($id);
        $projectType->update($request->all());

        return response()->json($projectType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $projectType = ProjectType::findOrFail($id);
        $projectType->delete();

        return response()->json(['message' => 'Tipo de proyecto eliminado correctamente']);
    }
}
