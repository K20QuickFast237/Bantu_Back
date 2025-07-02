<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreProfessionnelRequest;
use App\Models\Professionnel;
use Illuminate\Support\Facades\Auth;

class ProfessionnelProfileController extends Controller
{
    public function store(StoreProfessionnelRequest $request)
    {
        $user = Auth::guard('api')->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $professionnel = Professionnel::create($data);

        return response()->json([
            'message' => 'Profil professionnel complété avec succès',
            'data' => $professionnel,
        ], 201);
    }
}
