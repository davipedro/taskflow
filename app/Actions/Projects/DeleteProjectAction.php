<?php

namespace App\Actions\Projects;

use App\Models\Project;
use App\Repositories\ProjectRepository;

class DeleteProjectAction
{
    public function __construct(
        protected ProjectRepository $projectRepository
    ) {}

    public function handle(Project $project): bool
    {
        return $this->projectRepository->delete($project);
    }
}
