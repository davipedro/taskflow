<?php

namespace App\Actions\Tasks;

use App\DTOs\TaskFilterDTO;
use App\Repositories\TaskRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class GetUserTasksAction
{
    public function __construct(
        protected TaskRepository $taskRepository
    ) {}

    public function handle(int $userId, int $perPage, TaskFilterDTO $filters): LengthAwarePaginator
    {
        return $this->taskRepository
            ->findAllByUser($userId, $filters)
            ->paginate($perPage)
            ->withQueryString();
    }
}
