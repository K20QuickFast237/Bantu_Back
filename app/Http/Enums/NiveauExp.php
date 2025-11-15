<?php

namespace App\Http\Enums;

enum NiveauExp: string
{
    case JUNIOR = '<1an';
    case INTERMEDIAIRE = '1-3ans';
    case SENIOR = '4-5ans';
    case EXPERT = '>5ans';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
