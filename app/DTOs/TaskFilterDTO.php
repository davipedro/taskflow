<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class TaskFilterDTO
{
    public function __construct(
        public ?string $status = null,
        public ?string $priority = null,
        public string $sortBy = 'created_at',
        public string $sortOrder = 'desc'
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            status: $request->query('status'),
            priority: $request->query('priority'),
            sortBy: $request->query('sort_by', 'created_at'),
            sortOrder: $request->query('sort_order', 'desc')
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'priority' => $this->priority,
            'sort_by' => $this->sortBy,
            'sort_order' => $this->sortOrder,
        ];
    }
}
