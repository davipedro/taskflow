<?php

namespace App\Http\Controllers;

use App\Actions\Tasks\DeleteTaskAction;
use App\Actions\Tasks\GetUserTasksAction;
use App\Actions\Tasks\StoreTaskAction;
use App\Actions\Tasks\UpdateTaskAction;
use App\Actions\Tasks\UpdateTaskPriorityAction;
use App\Actions\Tasks\UpdateTaskStatusAction;
use App\DTOs\TaskFilterDTO;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskPriorityRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetUserTasksAction $action)
    {
        $filters = TaskFilterDTO::fromRequest($request);

        $tasks = $action->handle(auth()->id(), 6, $filters);

        return Inertia::render('Tasks/Index', [
            'tasks' => TaskResource::collection($tasks),
            'filters' => $filters->toArray(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createByProject(Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        return Inertia::render('Tasks/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, Project $project, StoreTaskAction $action)
    {
        $this->authorize('create', [Task::class, $project]);

        $validated = $request->validated();

        $task = $action->handle(auth()->id(), $validated);

        return redirect()
            ->route('tasks.index', $task)
            ->with('success', 'Tarefa criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return Inertia::render('Tasks/Show', [
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        return Inertia::render('Tasks/Edit', [
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task, UpdateTaskAction $action)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        $action->handle($task, $validated);

        return back()->with('success', 'Tarefa atualizada com sucesso!');
    }

    /**
     * Update task status.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task, UpdateTaskStatusAction $action)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        $action->handle($task, $validated);

        return back()->with('success', 'Status atualizado com sucesso!');
    }

    /**
     * Update task priority.
     */
    public function updatePriority(UpdateTaskPriorityRequest $request, Task $task, UpdateTaskPriorityAction $action)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        $action->handle($task, $validated);

        return back()->with('success', 'Prioridade atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task, DeleteTaskAction $action)
    {
        $this->authorize('delete', $task);

        $action->handle($task);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarefa deletada com sucesso!');
    }
}
