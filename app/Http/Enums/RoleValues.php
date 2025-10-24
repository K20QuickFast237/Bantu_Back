<?php

namespace App\Http\Enums;

enum RoleValues: string
{
    case ADMIN = 'Admin';
    case CANDIDAT = 'Particulier'; //"Cercheur d'emploi";
    case RECRUTEUR = 'Professionnel'; //'Recruteur';
    case FREELANCEUR = 'Freelanceur';
    case VENDEUR = 'Vendeur';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}


// case PARTICULIER = 'Particulier';
// case PROFESSIONNEL = 'Professionnel';