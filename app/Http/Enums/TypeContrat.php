<?php

namespace App\Http\Enums;

enum TypeContrat: string
{
    case CDI = 'CDI';
    case CDD = 'CDD';
    case INTERIM = 'Interim';
    case STAGE = 'Stage';
    case ALTERNANCE = 'Alternance';
    case FREELANCE = 'Freelance';
    case AUTRE = 'Autre';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
