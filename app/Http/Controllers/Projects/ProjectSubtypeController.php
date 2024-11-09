<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectSubtype;

class ProjectSubtypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function all()
    {
        // Obtiene todos los subtipos de proyecto y los retorna
        $projectSubtypes = ProjectSubtype::all();
        return response()->json($projectSubtypes);
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {       
        // Crear el subtipo de proyecto
        $projectSubtype = ProjectSubtype::create( $request->all());

        return response()->json($projectSubtype, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       // Buscar el subtipo de proyecto por ID
       $projectSubtype = ProjectSubtype::findOrFail($id);
       return response()->json($projectSubtype);
    }

    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        // Buscar el subtipo de proyecto y actualizarlo
        $projectSubtype = ProjectSubtype::findOrFail($id);
        $projectSubtype->update( $request->all());

        return response()->json($projectSubtype);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Buscar el subtipo de proyecto y eliminarlo
        $projectSubtype = ProjectSubtype::findOrFail($id);
        $projectSubtype->delete();

        return response()->json(['message' => 'Subtipo de proyecto eliminado correctamente']);
    }
}
