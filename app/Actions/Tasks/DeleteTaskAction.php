<?php

namespace App\Actions\Tasks;

use App\Models\Task;
use App\Repositories\TaskRepository;

class DeleteTaskAction
{
    public function __construct(
        protected TaskRepository $taskRepository
    ) {}

    public function handle(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }
}
