<?php

namespace App\Repositories;

use App\DTOs\TaskFilterDTO;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

class TaskRepository
{
    public function __construct(
        private Task $model
    ) {}

    public function findAllByUser(int $id, TaskFilterDTO $filters): Builder
    {
        $query = $this->model->where('user_id', $id);

        if ($filters->status) {
            $query->where('status', $filters->status);
        }

        if ($filters->priority) {
            $query->where('priority', $filters->priority);
        }

        return $query->orderBy($filters->sortBy, $filters->sortOrder);
    }

    public function create(int $id, array $data): Task
    {
        return $this->model->create([
            ...$data,
            'user_id' => $id,
        ]);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh();
    }

    public function updateStatus(Task $task, $status): Task
    {
        $data = ['status' => $status];

        if ($status->value === 'COMPLETED') {
            $data['completed_at'] = now();
        }

        $task->update($data);

        return $task->fresh();
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function findByUser(int $userId, int $taskId): ?Task
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('id', $taskId)
            ->first();
    }

    public function getStatsByUser(int $userId): array
    {
        return $this->model
            ->where('user_id', $userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getRecentByUser(int $userId, int $limit = 5): Builder
    {
        return $this->model
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit);
    }
}
