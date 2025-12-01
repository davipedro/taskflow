<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'deadline',
        'completed_at',
        'project_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'deadline' => 'date',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, TaskStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, TaskPriority $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopePending($query)
    {
        return $query->where('status', TaskStatus::PENDING);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', TaskStatus::IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TaskStatus::COMPLETED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
            ->where('status', '!=', TaskStatus::COMPLETED);
    }

    public function isOverdue(): bool
    {

        return $this->deadline
            && $this->deadline->isPast()
            && $this->status !== TaskStatus::COMPLETED;
    }

    public function canTransitionStatus(): bool
    {
        return $this->status->canTransition();
    }

    public function transitionToNextStatus(): void
    {
        if ($nextStatus = $this->status->next()) {
            $this->status = $nextStatus;

            if ($nextStatus === TaskStatus::COMPLETED) {
                $this->completed_at = now();
            }
        }
    }
}
