<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OffreEmploi;
use App\Services\EmbeddingService;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    // Cosine similarity
    private function cosineSimilarity(array $a, array $b): float {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $val) {
            $dot += $val * $b[$i];
            $normA += $val ** 2;
            $normB += $b[$i] ** 2;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    // Génère le texte complet d'un candidat avec pondération des skills
    private function candidateText(User $user, int $skillWeight = 3): string
    {
        $user->load([
            'particulier',
            'experiences:id,user_id,titre_poste,description_taches',
            'formations:id,user_id,diplome,domaine_etude',
        ]);

        $skillNames = \DB::table('user_skills')
            ->join('skills', 'skills.id', '=', 'user_skills.skill_id')
            ->where('user_skills.user_id', $user->id)
            ->pluck('skills.nom')
            ->all();

        // Répéter les skills pour augmenter leur poids
        $weightedSkills = implode(' ', array_fill(0, $skillWeight, implode(' ', $skillNames)));

        $c = $user->particulier;

        return trim(implode(' ', [
            optional($c)->titre_professionnel,
            optional($c)->resume_profil,
            collect($user->experiences)->map(fn($e) => trim($e->titre_poste.' '.$e->description_taches))->implode(' '),
            collect($user->formations)->map(fn($f) => trim($f->diplome.' '.$f->domaine_etude))->implode(' '),
            $weightedSkills,
            trim((optional($c)->ville ?? '').' '.(optional($c)->pays ?? '')),
        ]));
    }

    // Génère le texte complet d'une offre avec pondération des skills
    private function jobText(OffreEmploi $offre, int $skillWeight = 3): string
    {
        $offreSkillNames = \DB::table('offre_skills')
            ->join('skills', 'skills.id', '=', 'offre_skills.skill_id')
            ->where('offre_skills.offre_id', $offre->id)
            ->pluck('skills.nom')
            ->all();

        $weightedSkills = implode(' ', array_fill(0, $skillWeight, implode(' ', $offreSkillNames)));

        return trim(implode(' ', [
            $offre->titre_poste,
            $offre->description_poste,
            $offre->exigences,
            $offre->responsabilites,
            $weightedSkills,
            trim(($offre->ville ?? '').' '.($offre->pays ?? '')),
        ]));
    }

    // Candidat → Offres
    public function candidateMatches($candidateId)
    {
        $user = User::whereHas('particulier')->findOrFail($candidateId);
        $candidateText = $this->candidateText($user, 5); // skills ×5
        $candidateEmbedding = EmbeddingService::getEmbedding($candidateText);

        $offers = OffreEmploi::all();
        $results = [];

        foreach ($offers as $offre) {
            $jobEmbedding = EmbeddingService::getEmbedding($this->jobText($offre, 5)); // skills ×5
            $score = $this->cosineSimilarity($candidateEmbedding, $jobEmbedding) * 100;
            $results[] = [
                'offre_id' => $offre->id,
                'titre' => $offre->titre_poste,
                'score' => round($score, 2),
            ];
        }

        // Trier par score décroissant
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return response()->json($results);
    }

    // Recruteur → Candidats
    public function jobMatches($offreId)
    {
        $offre = OffreEmploi::findOrFail($offreId);
        $jobEmbedding = EmbeddingService::getEmbedding($this->jobText($offre, 5)); // skills ×5

        $candidates = User::whereHas('particulier')->get();
        $results = [];

        foreach ($candidates as $candidate) {
            $candidateEmbedding = EmbeddingService::getEmbedding($this->candidateText($candidate, 5)); // skills ×5
            $score = $this->cosineSimilarity($candidateEmbedding, $jobEmbedding) * 100;
            $results[] = [
                'candidate_id' => $candidate->id,
                'name' => $candidate->name ?? $candidate->email,
                'score' => round($score, 2),
            ];
        }

        // Trier par score décroissant
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return response()->json($results);
    }
}
