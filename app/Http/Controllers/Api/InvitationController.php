<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Models\Invitation;
use App\Traits\ApiResponseHandler;
use Illuminate\Http\JsonResponse;

class InvitationController extends Controller
{
    use ApiResponseHandler;

    public function index(): JsonResponse
    {
        return $this->handleApiNoTransaction(fn() => Invitation::all());
    }

    public function show(Invitation $invitation): JsonResponse
    {
        return $this->handleApiNoTransaction(fn() => $invitation);
    }

    public function store(StoreInvitationRequest $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            return Invitation::create($request->validated());
        }, 201);
    }

    public function update(UpdateInvitationRequest $request, Invitation $invitation): JsonResponse
    {
        return $this->handleApi(function () use ($request, $invitation) {
            $invitation->update($request->validated());
            return $invitation;
        });
    }

    public function destroy(Invitation $invitation): JsonResponse
    {
        return $this->handleApi(function () use ($invitation) {
            $invitation->delete();
            return null;
        }, 204);
    }
}
