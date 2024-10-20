<?php

namespace App\Http\Controllers\User;

use App\Models\User;
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
    public function create(Request $request): Response
{
    // Validar los datos de entrada
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'], // Asegúrate de que 'users' sea el nombre correcto de la tabla
        'password' => ['required', 'string', 'min:8'],
        'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Validación para la imagen (opcional)
    ]);

    // Procesar la imagen si se proporciona
    $profileImagePath = null;
    if ($request->hasFile('profile_image')) {
        $image = $request->file('profile_image');
        $profileImagePath = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images/profile'), $profileImagePath); // Guardar la imagen en public/images/profile
    }

    // Crear el usuario
    $user = User::create([
        'name' => $request->name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'password' => Hash::make($request->input('password')),
        'is_paid_user' => 0,
        'role' => $request->input('role', 'user'), // Valor por defecto es 'user' si no se pasa
        'uri' => $profileImagePath || '', // Almacena la ruta de la imagen si existe
    ]);

    // Retornar la respuesta con los datos del usuario
    return response([
        'message' => 'User created successfully',
        'user' => $user
    ], 201);
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

        // Guardar la sesión del usuario
        session(['user' => $user]);

        return response([
            'message' => 'Login successful',
            'user' => $user
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}