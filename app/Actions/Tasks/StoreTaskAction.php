<?php

namespace App\Actions\Tasks;

use App\Models\Task;
use App\Repositories\TaskRepository;

class StoreTaskAction
{
    public function __construct(
        protected TaskRepository $taskRepository
    ) {}

    public function handle(int $id, array $data): Task
    {
        return $this->taskRepository->create($id, $data);
    }
}
