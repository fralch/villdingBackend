<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserEndpointTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test del endpoint de creación de usuario sin imagen.
     */
    public function test_user_create_endpoint_without_image()
    {
        $response = $this->post('/endpoint/user/create', [
            'name' => 'Carlos',
            'last_name' => 'González',
            'email' => 'carlos@example.com',
            'edad' => 30,
            'genero' => 'M',
            'telefono' => '5551234567',
            'password' => 'password123',
            'role' => 'user',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'User created successfully',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'carlos@example.com',
            'name' => 'Carlos',
            'last_name' => 'González',
        ]);
    }

    /**
     * Test del endpoint de login exitoso.
     */
    public function test_user_login_endpoint_successful()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/endpoint/user/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Login successful',
                 ]);
    }

    /**
     * Test del endpoint de login con credenciales incorrectas.
     */
    public function test_user_login_endpoint_with_wrong_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/endpoint/user/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'These credentials do not match our records.',
                 ]);
    }

    /**
     * Test del endpoint para verificar si un email existe.
     */
    public function test_email_exists_endpoint()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/endpoint/user/email_exists', [
            'email' => 'existing@example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'User already exists',
                 ]);
    }

    /**
     * Test del endpoint para verificar email que no existe.
     */
    public function test_email_does_not_exist_endpoint()
    {
        $response = $this->post('/endpoint/user/email_exists', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'User does not exist',
                 ]);
    }

    /**
     * Test del endpoint para obtener todos los usuarios.
     */
    public function test_get_all_users_endpoint()
    {
        User::factory()->count(3)->create();

        $response = $this->get('/endpoint/user/all');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }

    /**
     * Test del endpoint para buscar usuario por código.
     */
    public function test_search_user_by_code_endpoint()
    {
        $user = User::factory()->create([
            'user_code' => 'A123456',
        ]);

        $response = $this->post('/endpoint/user/user_code', [
            'user_code' => 'A123456',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'user_code' => 'A123456',
                 ]);
    }

    /**
     * Test del endpoint para actualizar usuario.
     */
    public function test_update_user_endpoint()
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $response = $this->post('/endpoint/user/update', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'User updated successfully',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Test de validación de login sin email.
     */
    public function test_login_validation_without_email()
    {
        $response = $this->post('/endpoint/user/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(302); // Redirección por validación fallida
    }

    /**
     * Test de validación de login sin password.
     */
    public function test_login_validation_without_password()
    {
        $response = $this->post('/endpoint/user/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(302); // Redirección por validación fallida
    }
}
