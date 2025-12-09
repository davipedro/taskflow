<?php

namespace App\Actions\Dashboard;

use App\Repositories\TaskRepository;

class GetDashboardDataAction
{
    public function __construct(
        protected TaskRepository $taskRepository
    ) {}

    public function handle(int $userId): array
    {
        $stats = $this->taskRepository->getStatsByUser($userId);
        $recentTasks = $this->taskRepository->getRecentByUser($userId, 5)->get();

        return [
            'stats' => [
                'pending' => $stats['PENDING'] ?? 0,
                'in_progress' => $stats['IN_PROGRESS'] ?? 0,
                'completed' => $stats['COMPLETED'] ?? 0,
            ],
            'recentTasks' => $recentTasks,
        ];
    }
}
