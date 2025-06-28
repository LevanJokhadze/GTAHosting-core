<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\User;
use App\Models\Task;

class TaskFactory extends Factory
{
    protected $model = Task::class;
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'due_date' => now()->addDays(rand(1, 10)),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'status' => 'pending',
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
