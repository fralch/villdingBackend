<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('web'); // Habilita el middleware web que maneja las sesiones
    }

    /**
     * Método para iniciar sesión y crear la sesión del usuario.
     */
    public function create(Request $request)
    {
        
        // Procesar la imagen si se proporciona
        $profileImagePath = null;

        if ($request->hasFile('uri')) {
            $image = $request->file('uri');

            // Ruta temporal del archivo subido
            $rutaTemporal = $image->getPathname();
            
            // Nombre de la imagen con marca de tiempo para evitar conflictos de nombres
            $profileImagePath = time() . '.' . $image->getClientOriginalExtension();
            
            // Ruta de destino donde se guardará la imagen
            $rutaDestino = public_path('images/profile') . '/' . $profileImagePath;
            
            // Usar move_uploaded_file para mover el archivo desde la ubicación temporal a la ubicación final
            if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
                // Imagen movida exitosamente
            } else {
                // Manejar error en caso de que no se pueda mover el archivo
                return response()->json(['error' => 'Error al mover el archivo.'], 500);
            }
        }

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'edad' => $request->edad,
            'genero' => $request->genero,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->input('password')),
            'is_paid_user' => 0,
            'user_code' =>  $this->generateUniqueUserCode(),
            'role' => $request->input('role', 'user'), // Valor por defecto es 'user' si no se pasa
            'uri' => $profileImagePath  ? $profileImagePath : '' , // Almacena la ruta de la imagen si existe
        ]);

        // Retornar la respuesta con los datos del usuario
        return response([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }
     /**
     * Genera un código de usuario único con 7 caracteres: una letra mayúscula seguida de 6 números.
     */
    private function generateUniqueUserCode()
    {
        do {
            $code = $this->generateUserCode();
        } while (User::where('user_code', $code)->exists());

        return $code;
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

    // buscar usuario por user_code
    public function searchUserByCode(Request $request)
    {
        $user_code = $request->user_code;
        $user = User::where('user_code', $user_code)->first();
        return response()->json($user);
    }

    public function login(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar el usuario por email
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe y si la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'These credentials do not match our records.'
            ], 200);
        }

        // Obtener el tamaño de la imagen de perfil si existe
        $profileImageSize = null;
        if ($user->uri) {
            $imagePath = public_path('images/profile/' . $user->uri);
            if (file_exists($imagePath)) {
                $profileImageSize = filesize($imagePath); // Obtener el tamaño del archivo en bytes
            }
        }

        // Guardar la sesión del usuario
        session(['user' => $user]);

        return response([
            'message' => 'Login successful',
            'user' => $user,
            'profile_image_size' => $profileImageSize, // Devolver el tamaño de la imagen
        ], 200);
    }

    public function emailExists(Request $request)  {

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response([
                'message' => 'User already exists',
                'user' => $user
            ], 200);
        } else {
            return response([
                'message' => 'User does not exist'
            ], 200);
        }
        
    }
    /**
     * Método para obtener la sesión del usuario.
     */
    public function getSession()
    {
        // Obtener toda la sesión
        $sessionData = session()->all();

        return response()->json($sessionData, 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function all()
    {
        $users = User::all();
        return $users;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
    
        // Procesar la imagen si se proporciona
        $profileImagePath = null;
        if ($request->hasFile('uri')) {
            $image = $request->file('uri');
            $profileImagePath = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/profile'), $profileImagePath); // Guardar la imagen en public/images/profile
        }
    
        // Construir el arreglo de actualización solo con los campos que existen en el Request
        $dataToUpdate = array_filter([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'uri' => $profileImagePath ? $profileImagePath : '', // Almacena la ruta de la imagen si existe
            'telefono' => $request->telefono,
        ], function ($value) {
            return $value !== null;
        });
    
        // Actualizar solo los campos que existen en $dataToUpdate
        $user = User::where('id', $id)->update($dataToUpdate);
    
        // Retornar la respuesta con los datos del usuario
        return response([
            'message' => 'User updated successfully',
            'user' => $user
        ], 201);
    }

    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

   // check attachment user on project: verifica los proyectos vinculados a un usuario específico
   public function checkAttachmentUserProject(Request $request)
   {
       try {
           // Validar que el ID del usuario esté presente y exista
           $validatedData = $request->validate([
               'user_id' => 'required|exists:users,id',
           ]);
   
           // Obtener el usuario con todos los datos de los proyectos vinculados
           $user = User::with(['projects' => function ($query) {
               $query->select('projects.*', 'project_user.is_admin'); // Seleccionar todos los campos de "projects" + "is_admin"
           }])->find($validatedData['user_id']);
   
           if (!$user) {
               return response()->json(['message' => 'User not found'], 404);
           }
   
           // Mapear los proyectos para incluir los datos completos y el pivote
           $projects = $user->projects->map(function ($project) {
               return [
                   'id' => $project->id,
                   'name' => $project->name,
                   'location' => $project->location,
                   'company' => $project->company,
                   'code' => $project->code,
                   'start_date' => $project->start_date,
                   'end_date' => $project->end_date,
                   'uri' => $project->uri,
                   'project_type_id' => $project->project_type_id,
                   'project_subtype_id' => $project->project_subtype_id,
                   'is_admin' => $project->pivot->is_admin, // Agregar el valor del pivote
               ];
           });
   
           return response()->json([
               'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_code' => $user->user_code,
                    'uri' => $user->uri, 
               ],
               'projects' => $projects,
           ], 200);
       } catch (\Illuminate\Validation\ValidationException $e) {
           return response()->json([
               'message' => 'Validation failed',
               'errors' => $e->errors(),
           ], 422);
       }
   }

   // volver a un usuario administrador del proyecto
    public function makeAdmin(Request $request)
    {
         try {
              // Validar los datos de entrada
              $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'project_id' => 'required|exists:projects,id',
              ]);
    
              // Buscar la relación entre el usuario y el proyecto
              $projectUser = ProjectUser::where('user_id', $validatedData['user_id'])
                ->where('project_id', $validatedData['project_id'])
                ->first();
    
              if (!$projectUser) {
                return response()->json(['message' => 'User is not attached to the project'], 404);
              }
    
              // Actualizar el campo "is_admin" a 1
              $projectUser->is_admin = 1;
              $projectUser->save();
    
              return response()->json(['message' => 'User is now an admin of the project'], 200);
         } catch (\Illuminate\Validation\ValidationException $e) {
              return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
              ], 422);
         }
    }
   

}