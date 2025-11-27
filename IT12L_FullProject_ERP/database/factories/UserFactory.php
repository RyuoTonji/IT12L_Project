<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'given_name'     => fake()->firstName(),
            'surname'        => fake()->lastName(),
            'middle_initial' => fake()->optional(0.4)->randomLetter() . '.',
            'suffix'         => fake()->optional(0.05)->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
            'name'           => fn(array $attr) => trim($attr['given_name'] . ' ' . $attr['surname']),
            'email'          => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'contact_number' => '09' . fake()->unique()->numerify('##########'),
            'address'        => fake()->streetAddress() . ', ' . fake()->city() . ', ' . fake()->state() . ' ' . fake()->postcode(),
            'password'       => static::$password ??= Hash::make('password123'),
            'is_admin'       => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn() => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn() => [
            'given_name'     => 'Admin',
            'surname'        => 'BBQ',
            'email'          => 'admin@bbq-lagao.test',
            'contact_number' => '09171234567',
            'password'       => Hash::make('admin123'),
            'is_admin'       => true,
        ]);
    }
}