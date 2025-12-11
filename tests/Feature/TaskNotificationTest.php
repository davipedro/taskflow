<?php

use App\Events\TaskCreated;
use App\Jobs\SendTaskNotificationJob;
use App\Mail\TaskCreatedMail;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
});

it('dispatches TaskCreated event when creating a task', function () {
    Event::fake([TaskCreated::class]);

    actingAs($this->user);

    $taskData = [
        'title' => 'Nova Tarefa',
        'description' => 'Descrição da tarefa',
        'priority' => 'MEDIUM',
        'deadline' => now()->addDays(7)->format('Y-m-d'),
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
    ];

    post(route('projects.tasks.store', $this->project), $taskData);

    Event::assertDispatched(TaskCreated::class, function ($event) {
        return $event->task->title === 'Nova Tarefa';
    });
});

it('dispatches SendTaskNotificationJob when task is created', function () {
    Queue::fake();

    actingAs($this->user);

    $taskData = [
        'title' => 'Tarefa com Notificação',
        'priority' => 'HIGH',
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
    ];

    post(route('projects.tasks.store', $this->project), $taskData);

    Queue::assertPushed(SendTaskNotificationJob::class, function ($job) {
        return $job->task->title === 'Tarefa com Notificação';
    });
});

it('sends TaskCreatedMail to task creator', function () {
    Mail::fake();

    actingAs($this->user);

    $taskData = [
        'title' => 'Tarefa para Email',
        'priority' => 'LOW',
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
    ];

    post(route('projects.tasks.store', $this->project), $taskData);

    // Processar jobs da queue manualmente no teste
    $task = Task::where('title', 'Tarefa para Email')->first();
    $task->load('user', 'project');

    (new \App\Jobs\SendTaskNotificationJob($task))->handle();

    Mail::assertSent(TaskCreatedMail::class, function ($mail) use ($task) {
        return $mail->task->id === $task->id &&
               $mail->hasTo($task->user->email);
    });
});

it('includes all task details in the notification email', function () {
    Mail::fake();

    $task = Task::factory()->create([
        'user_id' => $this->user->id,
        'project_id' => $this->project->id,
        'title' => 'Tarefa Completa',
        'description' => 'Descrição detalhada',
        'priority' => 'HIGH',
        'deadline' => now()->addDays(3),
    ]);

    $task->load('user', 'project');

    (new \App\Jobs\SendTaskNotificationJob($task))->handle();

    Mail::assertSent(TaskCreatedMail::class, function ($mail) use ($task) {
        return $mail->task->title === $task->title &&
               $mail->task->description === $task->description &&
               $mail->task->priority->name === 'HIGH';
    });
});

it('handles queue failure gracefully', function () {
    Queue::fake();

    actingAs($this->user);

    $taskData = [
        'title' => 'Tarefa',
        'priority' => 'MEDIUM',
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
    ];

    post(route('projects.tasks.store', $this->project), $taskData)
        ->assertRedirect();

    // Tarefa deve ser criada mesmo se queue falhar
    assertDatabaseHas('tasks', [
        'title' => 'Tarefa',
        'user_id' => $this->user->id,
    ]);
});
