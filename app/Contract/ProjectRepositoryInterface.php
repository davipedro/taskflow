<?php

namespace App\Contract;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;

interface ProjectRepositoryInterface
{
    public function findAllByUser(int $id): Builder;

    public function create(int $id, array $data): Project;

    public function update(Project $project, array $data): Project;

    public function delete(Project $project): bool;

    public function findByUser(int $userId, int $projectId): ?Project;
}
