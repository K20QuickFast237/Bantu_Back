<?php

namespace App\Http\Enums;

enum TypeContrat: string
{
    case CDI = 'cdi';
    case CDD = 'cdd';
    case INTERIM = 'interim';
    case STAGE = 'stage';
    case ALTERNANCE = 'alternance';
    case FREELANCE = 'freelance';
    case AUTRE = 'autre';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
