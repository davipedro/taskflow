<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@taskflow.com',
            'password' => Hash::make('password'),
        ]);

        $projectsData = [
            ['name' => 'Website Redesign', 'description' => 'Complete redesign of the company website with modern UI/UX', 'color' => '#3B82F6'],
            ['name' => 'Mobile App Development', 'description' => 'Develop a mobile application for iOS and Android platforms', 'color' => '#10B981'],
            ['name' => 'API Integration', 'description' => 'Integrate third-party APIs and services', 'color' => '#F59E0B'],
            ['name' => 'Database Migration', 'description' => 'Migrate legacy database to new architecture', 'color' => '#EF4444'],
            ['name' => 'Marketing Campaign', 'description' => 'Q1 2025 digital marketing campaign', 'color' => '#8B5CF6'],
            ['name' => 'E-commerce Platform', 'description' => 'Build online store with payment integration', 'color' => '#EC4899'],
            ['name' => 'Customer Portal', 'description' => 'Self-service portal for customer support', 'color' => '#06B6D4'],
            ['name' => 'Analytics Dashboard', 'description' => 'Real-time analytics and reporting dashboard', 'color' => '#14B8A6'],
            ['name' => 'CI/CD Pipeline', 'description' => 'Automated deployment and testing pipeline', 'color' => '#F97316'],
            ['name' => 'Documentation Site', 'description' => 'Technical documentation and API reference', 'color' => '#6366F1'],
            ['name' => 'Performance Optimization', 'description' => 'Improve application speed and efficiency', 'color' => '#84CC16'],
            ['name' => 'Security Audit', 'description' => 'Comprehensive security review and fixes', 'color' => '#DC2626'],
            ['name' => 'Cloud Migration', 'description' => 'Migrate infrastructure to AWS/Azure', 'color' => '#0EA5E9'],
            ['name' => 'Internal Tools', 'description' => 'Build admin and management tools', 'color' => '#A855F7'],
            ['name' => 'Product Launch', 'description' => 'Prepare for Q2 product launch', 'color' => '#22C55E'],
        ];

        $projects = collect($projectsData)->map(function ($data) use ($demoUser) {
            return Project::factory()->forUser($demoUser)->create($data);
        });

        $tasksPerProject = [8, 7, 6, 5, 5, 5, 5, 4, 4, 4, 4, 4, 3, 3, 3];

        foreach ($projects as $index => $project) {
            $taskCount = $tasksPerProject[$index];

            for ($i = 0; $i < $taskCount; $i++) {
                $statuses = [TaskStatus::PENDING, TaskStatus::IN_PROGRESS, TaskStatus::COMPLETED];
                $priorities = [TaskPriority::LOW, TaskPriority::MEDIUM, TaskPriority::HIGH];

                $status = fake()->randomElement($statuses);
                $priority = fake()->randomElement($priorities);

                $isOverdue = fake()->boolean(15);
                $isCompleted = $status === TaskStatus::COMPLETED;

                if ($isOverdue && ! $isCompleted) {
                    $deadline = now()->subDays(fake()->numberBetween(1, 10));
                } elseif ($isCompleted) {
                    $deadline = now()->subDays(fake()->numberBetween(1, 30));
                } else {
                    $deadline = fake()->boolean(70) ? now()->addDays(fake()->numberBetween(1, 30)) : null;
                }

                Task::factory()->forUser($demoUser)->forProject($project)->create([
                    'status' => $status,
                    'priority' => $priority,
                    'deadline' => $deadline,
                    'completed_at' => $isCompleted ? now()->subDays(fake()->numberBetween(1, 20)) : null,
                ]);
            }
        }

        $this->command->info('✅ Demo user created:');
        $this->command->info('   Email: demo@taskflow.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info("✅ Created {$projects->count()} projects and 75 tasks");
    }
}
