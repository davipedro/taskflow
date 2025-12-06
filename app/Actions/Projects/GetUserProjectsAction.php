<?php

namespace App\Actions\Projects;

use App\Repositories\ProjectRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class GetUserProjectsAction
{
    public function __construct(
        protected ProjectRepository $projectRepository
    ) {}

    public function handle(int $userId, int $perPage = 6): LengthAwarePaginator
    {
        return $this->projectRepository
            ->findAllByUser($userId)
            ->paginate($perPage);
    }
}
