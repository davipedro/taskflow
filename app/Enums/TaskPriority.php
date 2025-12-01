<?php

namespace App\Enums;

enum TaskPriority: string
{
    case LOW = 'LOW';
    case MEDIUM = 'MEDIUM';
    case HIGH = 'HIGH';

    /**
     * Get all possible values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label in Portuguese
     */
    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Baixa',
            self::MEDIUM => 'Média',
            self::HIGH => 'Alta',
        };
    }

    /**
     * Get color for UI
     */
    public function color(): string
    {
        return match ($this) {
            self::LOW => 'green',
            self::MEDIUM => 'yellow',
            self::HIGH => 'red',
        };
    }
}
