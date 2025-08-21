<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreParticulierRequest;
use App\Models\Particulier;
use Illuminate\Support\Facades\Auth;

class ParticulierProfileController extends Controller
{
    public function store(StoreParticulierRequest $request)
    {
        $user = Auth::guard('api')->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;

        if ($request->hasFile('image_profil')) {
            $data['image_profil'] = $request->file('image_profil')->store('images/profils', 'public');
        }

        $particulier = Particulier::create($data);

        return response()->json([
            'message' => 'Profil particulier complété avec succès',
            'data' => $particulier,
        ], 201);
    }

    
}
