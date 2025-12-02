<?php

namespace App\Actions\Projects;

use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Collection;

class GetUserProjectsAction
{
    public function __construct(
        protected ProjectRepository $projectRepository
    ) {
    }

    public function handle(int $userId): Collection
    {
        return $this->projectRepository->findAllByUser($userId);
    }
}
