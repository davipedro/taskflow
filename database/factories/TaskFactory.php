<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $status = fake()->randomElement(TaskStatus::cases());
        $hasDeadline = fake()->boolean(60);

        return [
            'title' => fake()->sentence(rand(3, 6)),
            'description' => fake()->optional(0.7)->paragraph(),
            'status' => $status,
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'deadline' => $hasDeadline ? fake()->dateTimeBetween('now', '+2 months') : null,
            'completed_at' => $status === TaskStatus::COMPLETED ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function forProject(Project $project): static
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::PENDING,
            'completed_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::IN_PROGRESS,
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::COMPLETED,
            'completed_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function lowPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::LOW,
        ]);
    }

    public function mediumPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::MEDIUM,
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::HIGH,
        ]);
    }

    public function withDeadline(\DateTime|string|null $deadline = null): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => $deadline ?? fake()->dateTimeBetween('now', '+2 months'),
        ]);
    }

    public function withoutDeadline(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => null,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => fake()->randomElement([TaskStatus::PENDING, TaskStatus::IN_PROGRESS]),
            'deadline' => fake()->dateTimeBetween('-2 weeks', '-1 day'),
            'completed_at' => null,
        ]);
    }
}
