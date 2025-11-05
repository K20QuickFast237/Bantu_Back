<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Enums\RoleValues;
use App\Http\Requests\StoreCandidatureRequest;
use App\Http\Requests\UpdateCandidatureRequest;
use App\Http\Requests\UpdateCandidatureRecruteurRequest;
use App\Http\Resources\CandidatureResource;
use App\Mail\CandidatureReceived;
use App\Models\Candidature;
use App\Models\Conversation;
use App\Traits\ApiResponseHandler;
use Illuminate\Http\JsonResponse;
use App\Services\CvSnapshotService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;

class CandidatureController extends Controller
{
    use ApiResponseHandler, AuthorizesRequests;

    /**
     * Recruteur : Voir toutes les candidatures pour ses offres
     */
    public function index(): JsonResponse
    {
        return $this->handleApiNoTransaction(function () {
            $user = auth()->user();
            $professionnel = $user->professionnel;

            return Candidature::whereHas('offre', function ($q) use ($user) {
                $q->where('employeur_id', $user->professionnel->id);
            })->with(['particulier', 'offre.skills'])->get();
        });
    }

    /**
     * Recruteur : Voir toutes les candidatures d'une offre spécifique
     */
    public function candidaturesByOffre($offreId): JsonResponse
    {
        return $this->handleApiNoTransaction(function () use ($offreId) {
            $user = auth()->user();
            $professionnel = $user->professionnel;

            $candidatures = Candidature::whereHas('offre', function ($q) use ($professionnel, $offreId) {
                $q->where('employeur_id', $professionnel->id)
                ->where('id', $offreId);
            })
            ->with(['particulier', 'offre.skills'])
            ->get();

            return $candidatures;
        });
    }

    /**
     * Candidat : Voir ses candidatures
     */
    public function Candidatures(): JsonResponse
    {
        return $this->handleApiNoTransaction(function () {
            $user = auth()->user();
            $particulier = $user->particulier;

            return Candidature::where('particulier_id', $particulier->id)
                ->with(['offre.skills'])
                ->get();
        });
    }

    /**
     * Candidat : Créer une candidature
     */
    public function store(StoreCandidatureRequest $request, CvSnapshotService $cvSnapshotService): JsonResponse
    {
        return $this->handleApi(function () use ($request, $cvSnapshotService) {
            $data = $request->validated();
            $user = auth()->user();
            $particulier = $user->particulier;
            $data['particulier_id'] = $particulier->id;

            // Upload fichiers
            if ($request->hasFile('cv_file')) {
                $data['cv_url'] = $request->file('cv_file')->store('cvs', 'public');
                unset($data['cv_file']);
            }
            if ($request->hasFile('motivation_file')) {
                $data['motivation_url'] = $request->file('motivation_file')->store('motivations', 'public');
                unset($data['motivation_file']);
            }

            // Gestion des autres documents
            $autresDocs = [];
            // if ($request->hasFile('autres_documents')) {
            //     foreach ($request->file('autres_documents') as $key => $file) {
            //         $autresDocs[$key] = $file->store('autres_documents', 'public');
            //     }
            // }
            foreach ($data as $key => $doc) {
                if (str_contains($key, 'doc_titre')) {
                    $order = (int)$key[strlen($key) - 1];
                    if ($request->hasFile('doc' . $order)) {
                        $autresDocs[$data[$key]] = $request->file('doc' . $order)->store('candidatures/docs', 'public');
                    }
                    unset($data[$key]);
                }
            }

            if (!empty($autresDocs)) {
                $data['autres_documents'] = json_encode($autresDocs);
            }

            // Snapshot JSON du profil si candidature avec profil
            if ($request->boolean('cv_genere', false)) {
                $data['cv_genere'] = $cvSnapshotService->generate(auth()->user());
            }

            $candidature = Candidature::create($data);

            if ($user->role_actif !== RoleValues::RECRUTEUR && $user->role_actif !== RoleValues::CANDIDAT) {
                $user->update(['role_actif' => RoleValues::CANDIDAT]);
            }

            // Ouvrir la conversation et notifier par email
            // Vérifier si une conversation entre ces 2 utilisateurs existe déjà
            $recruteurId = $candidature->offre->employeur_id;
            $candidatId = $user->id;
            $conversation = Conversation::whereHas('participants', fn($q) => $q->where('users.id', $candidatId))
                ->whereHas('participants', fn($q) => $q->where('users.id', $recruteurId))
                ->has('participants', '=', 2) // s'assure qu'il n'y a que 2 participants
                ->first();


            if (!$conversation) {
                $conversation = Conversation::create(); // pas de title

                $conversation->participants()->attach([
                    $candidatId => ['joined_at' => now()],
                    $recruteurId => ['joined_at' => now()],
                ]);
            }
            // ajout du premier message dans la conversation.
            $message = $conversation->messages()->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $recruteurId,
                'content' => "Merci de nous avoir envoyé votre candidature pour le poste de " . $candidature->offre->titre . ". Nous l'examinerons et vous contacterons bientôt.",
            ]);
            // Émettre l'événement après l'enregistrement complet du message
            event(new \App\Events\MessageSent($message));

            // Envoyer l'email au recruteur
            $recruteurEmail = $candidature->offre->employeur->email_pro;
            Mail::to($recruteurEmail)->send(new CandidatureReceived($candidature));
            // ->cc($moreUsers)
            // ->bcc($evenMoreUsers)
            // ->queue(new CandidatureReceived($candidature));
            
            return new CandidatureResource($candidature->load(['offre.skills']));
        }, 201);
    }

    /**
     * Voir les détails d'une candidature spécifique
     * (accessible au recruteur propriétaire de l'offre ou au candidat concerné)
     */

    public function show(Candidature $candidature): JsonResponse
    {
        return $this->handleApiNoTransaction(function () use ($candidature) {
            $this->authorize('view', $candidature);

            return $candidature->load([
                'offre.skills',
                'offre.employeur',
                'particulier'
            ]);
        });
    }

    /**
     * Candidat : Modifier sa candidature
     * Seulement si statut = en_revision (policy)
     */
    public function update(UpdateCandidatureRequest $request, Candidature $candidature): JsonResponse
    {
        return $this->handleApi(function () use ($request, $candidature) {
            $this->authorize('update', $candidature);

            $data = $request->validated();

            if ($request->hasFile('cv_url')) {
                $data['cv_url'] = $request->file('cv_url')->store('cvs', 'public');
            }
            if ($request->hasFile('motivation_url')) {
                $data['motivation_url'] = $request->file('motivation_url')->store('motivations', 'public');
            }

            // Autres documents (ajoutés ou remplacés)
            if ($request->hasFile('autres_documents')) {
                $autresDocs = json_decode($candidature->autres_documents ?? '[]', true);

                foreach ($request->file('autres_documents') as $key => $file) {
                    $autresDocs[$key] = $file->store('autres_documents', 'public');
                }

                $data['autres_documents'] = json_encode($autresDocs);
            }

            $candidature->update($data);

            return $candidature->load(['offre.skills']);
        });
    }

    /**
     * Recruteur : Mettre à jour statut
     */
    public function updateStatus(UpdateCandidatureRecruteurRequest $request, Candidature $candidature): JsonResponse
    {
        return $this->handleApi(function () use ($request, $candidature) {
            $this->authorize('updateStatus', $candidature);

            $data = $request->validated();

            $candidature->update([
                'statut' => $data['statut'] ?? $candidature->statut,
            ]);

            return $candidature->load(['particulier', 'offre.skills']);
        });
    }

}
