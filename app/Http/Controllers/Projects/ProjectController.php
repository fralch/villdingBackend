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
            'code' => Str::random(10),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'uri' => $imagePath ? $imagePath : 'https://www.ajcproyectos.com/wp/wp-content/uploads/2018/12/Ingeniera-de-proyectos-y-construccion.jpg',
            'project_type_id' => $request->project_type_id,
            'project_subtype_id' => $request->project_subtype_id ?? null,
        ]);

        return response()->json($project, 201);
    }

     /**
     * Create semanas, dias, seguimientos and actividades desde una ruta post .
     */
    public function createProjectEntities( Request $request )
    {
        // Obtener los datos de la solicitud
        $project_id = $request->project_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $numeroSemanas = $request->numero_semanas;

        $fechaInicio = Carbon::parse($startDate);
        $fechaFin = Carbon::parse($endDate);

        // Crear las semanas
        for ($i = 0; $i < $numeroSemanas; $i++) {
            $fechaInicioSemana = $fechaInicio->copy()->addWeeks($i);
            $fechaFinSemana = $fechaInicioSemana->copy()->addDays(6);

            $semana = Semana::create([
                'proyecto_id' => $project_id,
                'numero_semana' => $i + 1,
                'fecha_inicio' => $fechaInicioSemana,
                'fecha_fin' => $fechaFinSemana,
            ]);

            // Crear los días de la semana
            $fechaDia = $fechaInicioSemana->copy();
            for ($j = 0; $j < 7; $j++) {
                $dia = Dia::create([
                    'semana_id' => $semana->id,
                    'fecha' => $fechaDia,
                ]);

                $fechaDia->addDay();
            }
        }
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
    public function attachProject(Request $request){
        try {
            // Validar que los IDs de usuario y proyecto están presentes en la solicitud
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'project_id' => 'required|exists:projects,id',
                'is_admin' => 'nullable|boolean', // Validar el campo adicional
            ]);

            // Obtener el usuario
            $user = User::findOrFail($validatedData['user_id']);

            // Asociar el proyecto con datos adicionales
            $user->projects()->attach($validatedData['project_id'], [
                'is_admin' => $validatedData['is_admin'] ?? false, // Usar false si no se envía
            ]);

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
        try {
            // Validar que el ID del proyecto esté presente y exista
            $validatedData = $request->validate([
                'project_id' => 'required|exists:projects,id',
            ]);
    
            // Obtener el proyecto con los usuarios vinculados
            $project = Project::with(['users' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.uri' , 'users.user_code', 'project_user.is_admin'); // Seleccionar campos relevantes
            }])->find($validatedData['project_id']);
    
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }
    
            // Formatear los datos
            $users = $project->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'uri' => $user->uri, 
                    'user_code' => $user->user_code, 
                    'is_admin' => $user->pivot->is_admin, // Extraer el valor del pivote

                ];
            });
    
            return response()->json([
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'location' => $project->location,
                    'company' => $project->company,
                    'code' => $project->code,
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'uri' => $project->uri,
                ],
                'users' => $users,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }
    
}
