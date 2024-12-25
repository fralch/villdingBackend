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
            'user_code' => Str::random(10),
            'role' => $request->input('role', 'user'), // Valor por defecto es 'user' si no se pasa
            'uri' => $profileImagePath  ? $profileImagePath : '' , // Almacena la ruta de la imagen si existe
        ]);

        // Retornar la respuesta con los datos del usuario
        return response([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
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

    // check attachment user on project: significa que se verifica si un usuario está vinculado a un proyecto específico 
    public function checkAttachmentUserProject(Request $request)
    {
        $user_id = $request->input('user_id');
    
        $user = User::find($user_id); // Usuario con ID 1
        $projects = $user->projects->unique(); // Proyectos vinculados al usuario sin duplicados
        return response()->json($projects);
    }
}