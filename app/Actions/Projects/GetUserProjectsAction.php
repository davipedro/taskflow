<?php

namespace App\Actions\Projects;

use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Builder;

class GetUserProjectsAction
{
    public function __construct(
        protected ProjectRepository $projectRepository
    ) {}

    public function handle(int $userId): Builder
    {
        return $this->projectRepository->findAllByUser($userId);
    }
}
