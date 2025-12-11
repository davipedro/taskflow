<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Jobs\SendTaskNotificationJob;

class SendTaskCreatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        SendTaskNotificationJob::dispatch($event->task);
    }
}
