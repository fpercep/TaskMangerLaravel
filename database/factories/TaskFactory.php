<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
