<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'role' => $this->rolerole_actif,
            'actif' => (bool)$this->is_active,
            'last_login' => $this->last_login,
            'email_verified_at' => $this->email_verified_at,
        ];
        if ($status = $this->isCandidat() || $this->isRecruteur()) {
            $user['profilCompleted'] = $status;
        }
        if ($this->rolerole_actif === 'Professionnel') {
            $user['professionnel'] = new ProfessionnelResource($this->professionnel);
        } else {
            $user['particulier'] = new ParticulierResource($this->particulier);
        }
        // if (isset($this->professionnel)) {
        //     $user['professionnel'] = new ProfessionnelResource($this->professionnel);
        // }
        // if (isset($this->particulier)) {
        //     $user['particulier'] = new ParticulierResource($this->particulier);
        // }
        if (isset($this->skills)) {
            $user['skills'] = UserSkillResource::collection($this->skills);
        }
        if (isset($this->experiences)) {
            $user['experiences'] = ExperienceResource::collection($this->experiences);
        }

        return $user;
    }
}
