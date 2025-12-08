<?php

namespace App\Actions\Tasks;

use App\Models\Project;
use App\Utils\PaginationHelper;
use Illuminate\Pagination\LengthAwarePaginator;

class GetProjectTasksAction
{
    public function handle(Project $project, int $requestedPage): LengthAwarePaginator
    {
        $project->loadCount('tasks');

        $perPage = 5;
        $page = PaginationHelper::clampPage(
            $requestedPage,
            $project->tasks()->count(),
            $perPage
        );

        return $project->tasks()->paginate($perPage, ['*'], 'page', $page);
    }
}
