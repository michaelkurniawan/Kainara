<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User; // Pastikan model User diimpor

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Nama model yang sesuai dengan factory.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Kata sandi saat ini yang digunakan oleh factory.
     */
    protected static ?string $password;

    /**
     * Mendefinisikan status default model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'dob' => fake()->date('Y-m-d', '2005-01-01'),
            'role' => fake()->randomElement(['user', 'admin']), // Mengatur peran secara acak
            'email_verified_at' => now(),
            'last_login' => fake()->dateTimeBetween('-1 year', 'now'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Menunjukkan bahwa alamat email model harus diverifikasi.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Menunjukkan bahwa model harus memiliki peran 'admin'.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Menunjukkan bahwa model harus memiliki peran 'user'.
     */
    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
        ]);
    }
}