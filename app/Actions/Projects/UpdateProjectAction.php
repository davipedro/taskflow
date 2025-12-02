<?php

namespace App\Actions\Projects;

use App\Models\Project;
use App\Repositories\ProjectRepository;

class UpdateProjectAction
{
    public function __construct(
        protected ProjectRepository $projectRepository
    ) {
    }

    public function handle(Project $project, array $data): Project
    {
        return $this->projectRepository->update($project, $data);
    }
}
