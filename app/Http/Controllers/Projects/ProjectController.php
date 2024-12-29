<?php

namespace App\Http\Controllers\Projects;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

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
    public function store(Request $request){
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
                'code' =>  $this->generateUserCode(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'uri' => $imagePath ? $imagePath : '',
                'project_type_id' => $request->project_type_id,
                'project_subtype_id' => $request->project_subtype_id                
            ]);

            return response()->json($project, 201);
    }

     /**
     * Genera un código de usuario con 7 caracteres: una letra mayúscula seguida de 6 números.
     */
    private function generateUserCode()
    {
        // Generar una letra mayúscula aleatoria
        $letter = chr(random_int(65, 90)); // ASCII de A-Z es 65-90

        // Generar 6 números aleatorios
        $numbers = '';
        for ($i = 0; $i < 6; $i++) {
            $numbers .= random_int(0, 9);
        }

        // Combinar la letra y los números
        $code = $letter . $numbers;

        return $code;
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


    // attachProject se encarga de vincular un proyecto a un usuario
    public function attachProject(Request $request) 
    {
        try {
            // Validar que los IDs de usuario y proyecto están presentes en la solicitud
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'project_id' => 'required|exists:projects,id',
            ]);

            // Obtener el usuario y agregar el proyecto
            $user = User::findOrFail($validatedData['user_id']);
            $user->projects()->attach($validatedData['project_id']);

            return response()->json([
                'message' => 'Project successfully linked to user',
                'user' => $user->load('projects'), // Cargar los proyectos para mostrar la relación actualizada
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /// desvincular un proyecto de un usuario
    public function detachProject(Request $request) 
    {
        try {
            // Validar que los IDs de usuario y proyecto están presentes en la solicitud
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'project_id' => 'required|exists:projects,id',
            ]);

            // Obtener el usuario y eliminar el proyecto
            $user = User::findOrFail($validatedData['user_id']);
            $user->projects()->detach($validatedData['project_id']);

            return response()->json([
                'message' => 'Project successfully unlinked from user',
                'user' => $user->load('projects'), // Cargar los proyectos para mostrar la relación actualizada
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function checkAttachmentProjectUser(Request $request)
    {   
        $project_id = $request->project_id; 
        $project = Project::find($project_id); // Proyecto con ID 1
        $users = $project->users->unique(); // Usuarios vinculados al proyecto sin duplicados
        return response()->json($users);
    }
}
