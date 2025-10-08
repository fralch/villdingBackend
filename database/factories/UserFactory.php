<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'edad' => fake()->numberBetween(18, 65),
            'genero' => fake()->randomElement(['M', 'F', 'O']),
            'telefono' => fake()->numerify('##########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_paid_user' => 0,
            'user_code' => $this->generateUserCode(),
            'role' => 'user',
            'uri' => '',
        ];
    }

    /**
     * Genera un código de usuario con 7 caracteres: una letra mayúscula seguida de 6 números.
     */
    private function generateUserCode(): string
    {
        $letter = chr(random_int(65, 90)); // ASCII de A-Z es 65-90
        $numbers = '';
        for ($i = 0; $i < 6; $i++) {
            $numbers .= random_int(0, 9);
        }
        return $letter . $numbers;
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
