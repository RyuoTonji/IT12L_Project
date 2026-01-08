<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'code' => $this->faker->unique()->bothify('BR-####'),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'is_active' => true,
        ];
    }
}
