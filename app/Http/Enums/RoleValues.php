<?php

namespace App\Http\Enums;

enum RoleValues: string
{
    case ADMIN = 'Admin';
    case PARTICULIER = 'Particulier';
    case PROFESSIONNEL = 'Professionnel';
}