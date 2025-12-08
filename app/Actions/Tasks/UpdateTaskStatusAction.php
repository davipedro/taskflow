<?php

namespace App\Actions\Tasks;

use App\Enums\TaskStatus;
use App\Exceptions\StatusTransitionNotAllowedException;
use App\Models\Task;
use App\Repositories\TaskRepository;

class UpdateTaskStatusAction
{
    public function __construct(
        protected TaskRepository $taskRepository
    ) {}

    /**
     * @throws StatusTransitionNotAllowedException
     */
    public function handle(Task $task, string $nextStatus): Task
    {
        $nextStatus = TaskStatus::from($nextStatus);

        if (! $task->canTransitionStatus($nextStatus)) {
            throw new StatusTransitionNotAllowedException;
        }

        return $this->taskRepository->updateStatus($task, $nextStatus);
    }
}
