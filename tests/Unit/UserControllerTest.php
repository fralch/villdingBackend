<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que verifica la generación de user_code único.
     */
    public function test_user_code_generation_is_unique()
    {
        // Crear dos usuarios
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Verificar que los user_code son diferentes
        $this->assertNotEquals($user1->user_code, $user2->user_code);

        // Verificar el formato del user_code (1 letra mayúscula + 6 números)
        $this->assertMatchesRegularExpression('/^[A-Z][0-9]{6}$/', $user1->user_code);
        $this->assertMatchesRegularExpression('/^[A-Z][0-9]{6}$/', $user2->user_code);
    }

    /**
     * Test que verifica la creación de un usuario con todos los datos.
     */
    public function test_user_can_be_created_with_all_fields()
    {
        $userData = [
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'juan.perez@example.com',
            'edad' => 25,
            'genero' => 'M',
            'telefono' => '1234567890',
            'password' => 'password123',
            'role' => 'user',
        ];

        $user = User::create([
            'name' => $userData['name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email'],
            'edad' => $userData['edad'],
            'genero' => $userData['genero'],
            'telefono' => $userData['telefono'],
            'password' => Hash::make($userData['password']),
            'is_paid_user' => 0,
            'user_code' => 'A123456',
            'role' => $userData['role'],
            'uri' => '',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'juan.perez@example.com',
            'edad' => 25,
            'genero' => 'M',
            'telefono' => '1234567890',
            'role' => 'user',
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test que verifica que el email debe ser único.
     */
    public function test_user_email_must_be_unique()
    {
        $user1 = User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $user2 = User::factory()->create([
            'email' => 'test@example.com'
        ]);
    }

    /**
     * Test que verifica el hash de contraseñas.
     */
    public function test_user_password_is_hashed()
    {
        $password = 'SecurePassword123';

        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);

        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /**
     * Test que verifica el valor por defecto de is_paid_user.
     */
    public function test_user_default_is_paid_user_is_zero()
    {
        $user = User::factory()->create([
            'is_paid_user' => 0
        ]);

        $this->assertEquals(0, $user->is_paid_user);
    }

    /**
     * Test que verifica el valor por defecto del role.
     */
    public function test_user_default_role_is_user()
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $this->assertEquals('user', $user->role);
    }

    /**
     * Test que verifica la relación con proyectos.
     */
    public function test_user_can_have_projects()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->projects);
    }
}
