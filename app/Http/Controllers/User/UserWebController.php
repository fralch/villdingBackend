<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class UserWebController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'edad' => 'nullable|integer',
            'genero' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'role' => 'nullable|string|in:user,admin',
        ]);

        // Procesar la imagen si se proporciona
        $profileImagePath = null;

        if ($request->hasFile('uri')) {
            $image = $request->file('uri');

            if ($image->isValid()) {
                $fileName = Str::uuid()->toString() . '.' . $image->getClientOriginalExtension();
                $profileImagePath = Storage::disk('s3')->putFileAs('profiles', $image, $fileName);
            }
        }

        try {
            // Crear el usuario
            $user = User::create([
                'name' => $validatedData['name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'edad' => $validatedData['edad'] ?? null,
                'genero' => $validatedData['genero'] ?? null,
                'telefono' => $validatedData['telefono'] ?? null,
                'password' => Hash::make($validatedData['password']),
                'is_paid_user' => 0,
                'user_code' => $this->generateUniqueUserCode(),
                'role' => $validatedData['role'] ?? 'user',
                'uri' => $profileImagePath,
            ]);

            return redirect()->route('users.show', $user->id)
                ->with('success', 'Usuario creado exitosamente.');
        } catch (\Exception $e) {
            // Si falla la creación del usuario y se subió una imagen, eliminarla de S3
            if ($profileImagePath) {
                Storage::disk('s3')->delete($profileImagePath);
            }

            return back()->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::with(['projects' => function ($query) {
            $query->select('projects.*', 'project_user.is_admin');
        }])->findOrFail($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Validar los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'edad' => 'nullable|integer',
            'genero' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'role' => 'nullable|string|in:user,admin',
        ]);

        // Procesar la imagen si se proporciona
        $profileImagePath = null;
        if ($request->hasFile('uri')) {
            $image = $request->file('uri');
            if ($image->isValid()) {
                $fileName = Str::uuid()->toString() . '.' . $image->getClientOriginalExtension();

                // Eliminar imagen anterior
                $previousImage = $user->getRawOriginal('uri');
                if ($previousImage) {
                    if (Storage::disk('s3')->exists($previousImage)) {
                        Storage::disk('s3')->delete($previousImage);
                    } else {
                        $localPath = public_path('images/profile/' . $previousImage);
                        if (file_exists($localPath)) {
                            unlink($localPath);
                        }
                    }
                }

                $profileImagePath = Storage::disk('s3')->putFileAs('profiles', $image, $fileName);
            }
        }

        // Preparar datos para actualizar
        $dataToUpdate = [
            'name' => $validatedData['name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'edad' => $validatedData['edad'] ?? null,
            'genero' => $validatedData['genero'] ?? null,
            'telefono' => $validatedData['telefono'] ?? null,
            'role' => $validatedData['role'] ?? 'user',
        ];

        // Actualizar contraseña solo si se proporciona
        if (!empty($validatedData['password'])) {
            $dataToUpdate['password'] = Hash::make($validatedData['password']);
        }

        // Actualizar imagen solo si se subió una nueva
        if ($profileImagePath) {
            $dataToUpdate['uri'] = $profileImagePath;
        }

        $user->update($dataToUpdate);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Eliminar imagen de perfil si existe
        $profileImage = $user->getRawOriginal('uri');
        if ($profileImage) {
            if (Storage::disk('s3')->exists($profileImage)) {
                Storage::disk('s3')->delete($profileImage);
            }
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
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
}
