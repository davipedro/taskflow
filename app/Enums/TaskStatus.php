<?php

namespace App\Enums;

enum TaskStatus: string
{
    case PENDING = 'PENDING';
    case IN_PROGRESS = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';

    /**
     * Get all possible values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Validate if a given status is valid
     */
    public function isValid(): bool
    {
        return in_array($this, self::cases());
    }

    /**
     * Get label in Portuguese
     */
    public function label(): string
    {
        if(!$this->isValid()) {
            throw new \InvalidArgumentException('Status inválido');
        }

        return match ($this) {
            self::PENDING => 'Pendente',
            self::IN_PROGRESS => 'Em Progresso',
            self::COMPLETED => 'Concluída',
            default => throw new \InvalidArgumentException('Status desconhecido'),
        };
    }

    /**
     * Get next status in the workflow
     */
    public function next(): ?self
    {
        if(!$this->isValid()) {
            throw new \InvalidArgumentException('Status inválido');
        }

        return match ($this) {
            self::PENDING => self::IN_PROGRESS,
            self::IN_PROGRESS => self::COMPLETED,
            self::COMPLETED => null
        };
    }

    /**
     * Check if status can transition to next
     */
    public function canTransition(): bool
    {
        return $this->next() !== null;
    }
}
