<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Enums\TypeContrat;

class MetadataController extends Controller
{
    public function typesContrat()
    {
        return response()->json([
            'types_contrat' => TypeContrat::values(),
        ]);
    }
}
