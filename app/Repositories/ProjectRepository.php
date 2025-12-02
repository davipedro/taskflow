<?php

namespace App\Repositories;

use App\Contract\ProjectRepositoryInterface;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function __construct(
        private Project $model
    ) {
    }

    public function findAllByUser(int $id): Collection
    {
        return $this->model
            ->where('user_id', $id)
            ->with('tasks')
            ->latest()
            ->get();
    }

    public function create(int $id, array $data): Project
    {
        return $this->model->create([
            ...$data,
            'user_id' => $id,
        ]);
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project->fresh();
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }

    public function findByUser(int $userId, int $projectId): ?Project
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('id', $projectId)
            ->first();
    }
}
