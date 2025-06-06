<?php

namespace App\Http\Controllers\Projects;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon; 

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectUser;
use App\Models\ProjectType;
use App\Models\ProjectSubtype;
use App\Models\Week;
use App\Models\Day;
use App\Models\Tracking;
use App\Models\Activity;

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
            'nearest_monday' => $request->nearest_monday,
            'uri' => $imagePath ? $imagePath : 'https://www.hycproyectos.com/wp-content/uploads/2021/06/28_06_-%C2%BFPor-que-es-necesaria-la-interventoria-de-obras-civiles-en-los-proyectos_-970x485.jpg',
            'project_type_id' => $request->project_type_id,
            'project_subtype_id' => $request->project_subtype_id ?? null,
        ]);

        return response()->json($project, 201);
    }

     /**
     * Create semanas, dias, seguimientos and actividades desde una ruta post .
     */
    public function createProjectEntities(Request $request){
        // Obtener los datos de la solicitud
        $project_id = $request->project_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $numeroSemanas = $request->numero_semanas;

        // Comprobar si ya se crearon las semanas
        $weeks = Week::where('project_id', $project_id)->get();
        if (!$weeks->isEmpty()) {
            // Retornar semanas creadas si ya existen
            return response()->json($weeks);
        }

        $fechaInicio = Carbon::parse($startDate);
        $fechaFin = Carbon::parse($endDate);

        // Crear las semanas
        for ($i = 0; $i < $numeroSemanas; $i++) {
            $fechaInicioSemana = $fechaInicio->copy()->addWeeks($i);
            $fechaFinSemana = $fechaInicioSemana->copy()->addDays(6);

            $semana = Week::create([
                'project_id' => $project_id,
                'start_date' => $fechaInicioSemana,
                'end_date' => $fechaFinSemana,
            ]);

            // Crear los días de la semana
            for ($j = 0; $j < 7; $j++) {
                $fechaInicioDia = $fechaInicioSemana->copy()->addDays($j);

                Day::create([
                    'project_id' => $project_id,
                    'week_id' => $semana->id,
                    'date' => $fechaInicioDia,
                ]);
            }
        }

        // Retornar satisfactoriamente
        return response()->json(['message' => 'Semanas y días creados correctamente']);
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
    public function updateProject(Request $request, string $id)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'location' => 'sometimes|string|max:255',
                'company' => 'sometimes|string|max:255',
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date|after_or_equal:start_date',
                'nearest_monday' => 'sometimes|date',
                'project_type_id' => 'sometimes|exists:project_types,id',
                'project_subtype_id' => 'sometimes|nullable|exists:project_subtypes,id',
                'uri' => 'sometimes|file|image|max:2048' // Máximo 2MB
            ]);

            // Buscar el proyecto
            $project = Project::findOrFail($id);

            // Verificar si se está actualizando el nombre y si ya existe otro proyecto con ese nombre
            if (isset($validatedData['name']) && $validatedData['name'] !== $project->name) {
                $existingProject = Project::where('name', $validatedData['name'])
                    ->where('id', '!=', $id)
                    ->first();
                
                if ($existingProject) {
                    return response()->json([
                        'message' => 'Ya existe un proyecto con este nombre',
                        'error' => 'El nombre del proyecto debe ser único'
                    ], 422);
                }
            }

            // Procesar la imagen si se proporciona una nueva
            if ($request->hasFile('uri')) {
                // Eliminar la imagen anterior si existe y no es la imagen por defecto
                if ($project->uri && $project->uri !== 'https://www.hycproyectos.com/wp-content/uploads/2021/06/28_06_-%C2%BFPor-que-es-necesaria-la-interventoria-de-obras-civiles-en-los-proyectos_-970x485.jpg') {
                    $oldImagePath = public_path('images/projects/') . $project->uri;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Guardar la nueva imagen
                $image = $request->file('uri');
                $imagePath = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/projects'), $imagePath);
                $validatedData['uri'] = $imagePath;
            }

            // Actualizar el proyecto
            $project->update($validatedData);

            return response()->json([
                'message' => 'Proyecto actualizado correctamente',
                'project' => $project
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Proyecto no encontrado'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyProject(string $id)
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

    function checkProjectEntities(Request $request)  {
        $project_id = $request->project_id;
        $weeks = Week::where('project_id', $project_id)->get();
        if (!$weeks->isEmpty()) {
            // Retornar semanas creadas si ya existen
            return response()->json($weeks);
        }
    }

      /*  
      Obtener los tipos de  un proyecto
      GET /endpoint/project/types/{project_id}
    */
    public function getProjectTypes(string $project_id)
    {
        try {
            // Buscar el proyecto por ID
            $project = Project::findOrFail($project_id);
            
            // Obtener el tipo y subtipo del proyecto
            $projectType = $project->type;
            $projectSubtype = $project->subtype;
            
            return response()->json([
                'project_id' => $project->id,
                'project_name' => $project->name,
                'type' => $projectType,
                'subtype' => $projectSubtype
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Proyecto no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los tipos del proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
     /*
      Obtener todos los subtipos de un proyecto
      GET /endpoint/project/subtypes/{project_id}
    */
    public function getProjectSubtypes(string $project_id)
    {
        try {
            // Buscar el proyecto por ID
            $project = Project::findOrFail($project_id);
            
            // Obtener el tipo de proyecto
            $projectType = $project->type;
            
            if (!$projectType) {
                return response()->json([
                    'message' => 'El proyecto no tiene un tipo asignado'
                ], 404);
            }
            
            // Obtener todos los subtipos asociados a ese tipo de proyecto
            $subtypes = ProjectSubtype::where('project_type_id', $projectType->id)->get();
            
            return response()->json([
                'project_id' => $project->id,
                'project_name' => $project->name,
                'project_type_id' => $projectType->id,
                'project_type_name' => $projectType->name,
                'subtypes' => $subtypes
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Proyecto no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los subtipos del proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
