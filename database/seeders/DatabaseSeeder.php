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
        $demoUser = User::factory()->withoutTwoFactor()->create([
            'name' => 'Demo User',
            'email' => 'demo@taskflow.com',
            'password' => Hash::make('password'),
        ]);

        $project1 = Project::factory()->forUser($demoUser)->create([
            'name' => 'Website Redesign',
            'description' => 'Complete redesign of the company website with modern UI/UX',
            'color' => '#3B82F6',
        ]);

        $project2 = Project::factory()->forUser($demoUser)->create([
            'name' => 'Mobile App Development',
            'description' => 'Develop a mobile application for iOS and Android platforms',
            'color' => '#10B981',
        ]);

        $project3 = Project::factory()->forUser($demoUser)->create([
            'name' => 'API Integration',
            'description' => null,
            'color' => '#F59E0B',
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Design homepage mockup',
            'description' => 'Create high-fidelity mockups for the new homepage layout',
            'status' => TaskStatus::COMPLETED,
            'priority' => TaskPriority::HIGH,
            'deadline' => now()->subDays(5),
            'completed_at' => now()->subDays(3),
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Implement responsive navigation',
            'description' => 'Build a mobile-friendly navigation menu with hamburger icon',
            'status' => TaskStatus::IN_PROGRESS,
            'priority' => TaskPriority::HIGH,
            'deadline' => now()->addDays(3),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Update content for About Us page',
            'description' => null,
            'status' => TaskStatus::PENDING,
            'priority' => TaskPriority::LOW,
            'deadline' => now()->addDays(14),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Test cross-browser compatibility',
            'description' => 'Ensure the website works correctly on all major browsers',
            'status' => TaskStatus::PENDING,
            'priority' => TaskPriority::MEDIUM,
            'deadline' => now()->addDays(10),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Set up SEO best practices',
            'description' => 'Implement meta tags, alt attributes, and sitemap.xml',
            'status' => TaskStatus::IN_PROGRESS,
            'priority' => TaskPriority::HIGH,
            'deadline' => now()->addDays(4),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Conduct user testing sessions',
            'description' => 'Organize sessions to gather feedback on the new design',
            'status' => TaskStatus::PENDING,
            'priority' => TaskPriority::MEDIUM,
            'deadline' => now()->addDays(12),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Migrate content from old site',
            'description' => 'Transfer all blog posts and pages to the new website',
            'status' => TaskStatus::COMPLETED,
            'priority' => TaskPriority::HIGH,
            'deadline' => now()->subDays(2),
            'completed_at' => now()->subDay(),
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Set up Google Analytics',
            'description' => 'Integrate Google Analytics to track website traffic and user behavior',
            'status' => TaskStatus::IN_PROGRESS,
            'priority' => TaskPriority::MEDIUM,
            'deadline' => now()->addDays(6),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Create blog post templates',
            'description' => null,
            'status' => TaskStatus::PENDING,
            'priority' => TaskPriority::LOW,
            'deadline' => now()->addDays(20),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Set up email newsletter signup',
            'description' => 'Integrate Mailchimp signup form on the website',
            'status' => TaskStatus::COMPLETED,
            'priority' => TaskPriority::MEDIUM,
            'deadline' => now()->subDays(7),
            'completed_at' => now()->subDays(5),
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->create([
            'title' => 'Optimize images for web',
            'description' => 'Compress and convert images to WebP format for better performance',
            'status' => TaskStatus::PENDING,
            'priority' => TaskPriority::MEDIUM,
            'deadline' => now()->addDays(7),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project2)->create([
            'title' => 'Setup React Native project',
            'description' => 'Initialize new React Native project with necessary dependencies',
            'status' => TaskStatus::COMPLETED,
            'priority' => TaskPriority::HIGH,
            'deadline' => now()->subDays(10),
            'completed_at' => now()->subDays(8),
        ]);

        Task::factory()->forUser($demoUser)->forProject($project2)->create([
            'title' => 'Implement user authentication',
            'description' => 'Add login and registration screens with JWT authentication',
            'status' => TaskStatus::IN_PROGRESS,
            'priority' => TaskPriority::HIGH,
            'deadline' => now()->addDays(5),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project2)->create([
            'title' => 'Create settings page',
            'description' => null,
            'status' => TaskStatus::PENDING,
            'priority' => TaskPriority::LOW,
            'deadline' => now()->addDays(15),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project3)->create([
            'title' => 'Document API endpoints',
            'description' => 'Create comprehensive documentation for all REST API endpoints',
            'status' => TaskStatus::PENDING,
            'priority' => TaskPriority::MEDIUM,
            'deadline' => null,
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project3)->create([
            'title' => 'Write integration tests',
            'description' => 'Add automated tests for third-party API integrations',
            'status' => TaskStatus::PENDING,
            'priority' => TaskPriority::MEDIUM,
            'deadline' => now()->addDays(10),
            'completed_at' => null,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project1)->overdue()->create([
            'title' => 'Fix mobile menu bug',
            'description' => 'Menu not closing properly on mobile devices',
            'priority' => TaskPriority::HIGH,
        ]);

        Task::factory()->forUser($demoUser)->forProject($project2)->create([
            'title' => 'Add push notifications',
            'description' => 'Implement Firebase Cloud Messaging for push notifications',
            'status' => TaskStatus::PENDING,
            'priority' => TaskPriority::LOW,
            'deadline' => now()->addDays(20),
            'completed_at' => null,
        ]);

        $this->command->info('✅ Demo user created:');
        $this->command->info('   Email: demo@taskflow.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('✅ Created 3 projects and 10 tasks');
    }
}
