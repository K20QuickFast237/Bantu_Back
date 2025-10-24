<?php

namespace App\Http\Enums;

enum OffreStatus: string
{
    case Available = 'Active';
    case PAUSED = 'En Pause';
    case CLOSED = 'Fermee';
    case ARCHIVED = 'Archivee';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}