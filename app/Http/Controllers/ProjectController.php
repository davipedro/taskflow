<?php

namespace App\Http\Controllers;

use App\Actions\Projects\DeleteProjectAction;
use App\Actions\Projects\GetUserProjectsAction;
use App\Actions\Projects\StoreProjectAction;
use App\Actions\Projects\UpdateProjectAction;
use App\Actions\Tasks\GetProjectTasksAction;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function __construct(

    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(GetUserProjectsAction $action)
    {
        $projects = $action->handle(auth()->id());

        return Inertia::render('Projects/Index', [
            'projects' => ProjectResource::collection($projects),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Project::class);

        return Inertia::render('Projects/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request, StoreProjectAction $action)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validated();

        $project = $action->handle(auth()->id(), $validated);

        return back()->with('success', 'Projeto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Request $request, GetProjectTasksAction $action)
    {
        $this->authorize('view', $project);
        $requestedPage = (int) $request->query('page', 1);

        $projectTasks = $action->handle($project, $requestedPage);

        return Inertia::render('Projects/Show', [
            'project' => new ProjectResource($project),
            'tasks' => Inertia::defer(fn () => TaskResource::collection($projectTasks)),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return Inertia::render('Projects/Edit', [
            'project' => new ProjectResource($project),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project, UpdateProjectAction $action)
    {
        $this->authorize('update', $project);

        $validated = $request->validated();

        $action->handle($project, $validated);

        return back()->with('success', 'Projeto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, DeleteProjectAction $action)
    {
        $this->authorize('delete', $project);

        $action->handle($project);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Projeto deletado com sucesso!');
    }
}
