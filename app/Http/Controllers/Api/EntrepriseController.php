<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfessionnelResource;
use Illuminate\Http\Request;
use App\Models\Professionnel;
use Illuminate\Http\Response;

class EntrepriseController extends Controller
{
    // Entreprises avec au moins une offre active
    
    public function entreprisesAvecOffresEnCours()
    {
        $entreprises = ProfessionnelResource::collection(
            Professionnel::whereHas('offres', function ($query) {
                $query->where('statut', 'active');
            })->with(['offres' => function ($query) {
                $query->where('statut', 'active');
            }])->get()
        );

        return response()->json([
            'count' => $entreprises->count(),
            'data' => $entreprises,
        ], Response::HTTP_OK);
    }

    // Entreprises avec au moins une offre (quel que soit le statut)

    public function entreprisesAvecOffres()
    {
        $entreprises = Professionnel::has('offres')->with('offres')->get();

        return response()->json([
            'count' => $entreprises->count(),
            'data' => $entreprises,
        ], Response::HTTP_OK);
    }
}
