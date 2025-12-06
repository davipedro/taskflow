<?php

namespace App\Actions\Projects;

use App\Models\Project;
use App\Repositories\ProjectRepository;

class StoreProjectAction
{
    public function __construct(
        protected ProjectRepository $projectRepository
    ) {}

    public function handle(int $id, array $data): Project
    {
        return $this->projectRepository->create($id, $data);
    }
}
