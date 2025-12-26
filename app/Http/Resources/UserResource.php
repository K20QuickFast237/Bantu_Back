<?php

namespace App\Http\Resources;

use App\Http\Enums\RoleValues;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Acheteur\SimpleAcheteurResource;
use App\Http\Resources\Vendeur\SimpleVendeurResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'role' => $this->role_actif,
            'actif' => (bool)$this->is_active,
            'last_login' => $this->last_login,
            'email_verified_at' => $this->email_verified_at,
            'acheteur' => $this->acheteur ? new SimpleAcheteurResource($this->acheteur) : null,
            'vendeur' => $this->vendeur ? new SimpleVendeurResource($this->vendeur) : null,
        ];
        if ($status = $this->isCandidat() || $this->isRecruteur()) {
            $user['profilCompleted'] = $status;
        }
        // if ($this->rolerole_actif === RoleValues::RECRUTEUR) {
        //     $user['professionnel'] = new ProfessionnelResource($this->professionnel);
        // } else {
        //     $user['particulier'] = new ParticulierResource($this->particulier);
        // }
        if (isset($this->professionnel) && $this->professionnel) {
            $user['professionnel'] = new ProfessionnelResource($this->professionnel);
        }
        if (isset($this->particulier) && $this->particulier) {
            $user['particulier'] = new ParticulierResource($this->particulier);
        }
        if (isset($this->skills)) {
            $user['skills'] = UserSkillResource::collection($this->skills);
        }
        if (isset($this->experiences)) {
            $user['experiences'] = ExperienceResource::collection($this->experiences);
        }

        return $user;
    }
}
