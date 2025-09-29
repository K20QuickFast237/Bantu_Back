<?php

namespace App\Http\Enums;

enum RoleValues: string
{
    case ADMIN = 'Admin';
    case CANDIDAT = 'Particulier'; //"Cercheur d'emploi";
    case RECRUTEUR = 'Professionnel'; //'Recruteur';
    case FREELANCEUR = 'Freelanceur';
    case VENDEUR = 'Vendeur';
}


// case PARTICULIER = 'Particulier';
// case PROFESSIONNEL = 'Professionnel';