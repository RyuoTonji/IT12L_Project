<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'branch_id' => \App\Models\Branch::factory(),
            'table_id' => null,
            'customer_name' => $this->faker->name,
            'order_type' => 'dine-in',
            'status' => 'new',
            'total_amount' => $this->faker->randomFloat(2, 10, 100),
            'payment_status' => 'pending',
        ];
    }
}
