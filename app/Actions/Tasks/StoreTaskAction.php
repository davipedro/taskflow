<?php

namespace App\Actions\Tasks;

use App\Events\TaskCreated;
use App\Models\Task;
use App\Repositories\TaskRepository;

class StoreTaskAction
{
    public function __construct(
        protected TaskRepository $taskRepository
    ) {}

    public function handle(int $id, array $data): Task
    {
        $task = $this->taskRepository->create($id, $data);

        $task->load('user', 'project');
        TaskCreated::dispatch($task);

        return $task;
    }
}
