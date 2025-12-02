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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'locale' => 'es',
            'last_renew_password_at' => now(),
            'force_renew_password' => false,
        ];
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

    /**
     * Indicate that the user has an expired password (older than 90 days).
     */
    public function withExpiredPassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_renew_password_at' => now()->subDays(91),
        ]);
    }

    /**
     * Indicate that the user must renew their password on next login.
     */
    public function mustRenewPassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'force_renew_password' => true,
        ]);
    }
}
