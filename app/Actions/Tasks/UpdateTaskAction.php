<?php

namespace App\Actions\Tasks;

use App\Models\Task;
use App\Repositories\TaskRepository;

class UpdateTaskAction
{
    public function __construct(
        protected TaskRepository $taskRepository
    ) {}

    public function handle(Task $task, array $data): Task
    {
        return $this->taskRepository->update($task, $data);
    }
}
