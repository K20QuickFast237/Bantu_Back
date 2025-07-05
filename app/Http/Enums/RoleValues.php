<?php

namespace App\Http\Enums;

enum RoleValues: string
{
    case ADMIN = 'Admin';
    case CANDIDAT = "Cercheur d'emploi";
    case RECRUTEUR = 'Recruteur';
    case FREELANCEUR = 'Freelanceur';
    case VENDEUR = 'Vendeur';
}


// case PARTICULIER = 'Particulier';
// case PROFESSIONNEL = 'Professionnel';