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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'remember_token' => "token",
            'is_paid_user' => $request->is_paid_user,
            'role' => $request->role
        ]);


        return   response([
            'message' => 'User created successfully',
            'user' => $user
        ]);
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
            ], 404);
        }

        // Guardar la sesión del usuario
        session(['user' => $user]);

        return response([
            'message' => 'Login successful',
            'user' => $user
        ], 200);
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