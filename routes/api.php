<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ParticulierProfileController;
use App\Http\Controllers\Api\ProfessionnelProfileController;
use App\Http\Controllers\Api\FormationController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\OffreEmploiController;
use App\Http\Controllers\Api\CandidatureController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MatchingController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\MetadataController;
use App\Http\Controllers\Api\EntrepriseController;
use App\Http\Controllers\Api\FavoriController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

Route::middleware('auth:api')->prefix('user')->group(function () {
    Route::get('/{user}', fn(User $user) => response()->json(new UserResource($user)))->middleware('verified'); // Verified to ensure the user's email is verified
    Route::get('/{user}/skills', [UserController::class, 'getUserSkills']);
    Route::get('/{user}/roles', [UserController::class, 'getUserRoles']);
    Route::post('/{user}/skill', [UserController::class, 'setUserSkill']);
    Route::post('/{user}/role', [UserController::class, 'setUserRole']);
    Route::delete('/{user}/skill/{skill}', [UserController::class, 'deleteUserSkill']);
    Route::delete('/{user}/role/{role}', [UserController::class, 'deleteUserRole']);
});

//Routes: gestion de la newsletter
Route::prefix('newsletter')->group(function () {
    Route::post('/subscribe', [NewsletterController::class, 'subscribe']);
    Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe']);
    Route::get('/subscribers', [NewsletterController::class, 'index']);
});

//Route pour les types de contrat
Route::get('/types-contrat', [MetadataController::class, 'typesContrat']);

//Route pour la liste des entreprises ayant des offres en cours ou ayant au moins une offfre
Route::prefix('entreprises')->group(function () {
    Route::get('/avec-offres-en-cours', [EntrepriseController::class, 'entreprisesAvecOffresEnCours']);
    Route::get('/avec-offres', [EntrepriseController::class, 'entreprisesAvecOffres']);
});

//Route pour les favoris
Route::middleware('auth:api')->prefix('favoris')->group(function () {
    Route::post('/ajouter', [FavoriController::class, 'add']);
    Route::post('/retirer', [FavoriController::class, 'remove']);
    Route::get('/', [FavoriController::class, 'list']);
});

// Routes pour gérer les categories (des offres)
Route::prefix('categories')->group(function () {
    Route::get('/', [CategorieController::class, 'index']);
    Route::get('/{id}', [CategorieController::class, 'show']);
    Route::post('/', [CategorieController::class, 'store']);
    Route::put('/{id}', [CategorieController::class, 'update']);
    Route::delete('/{id}', [CategorieController::class, 'destroy']);
});

// Routes matching pour les listings de candidats et d'offres
Route::get('matching/candidate/{candidateId}', [MatchingController::class, 'candidateMatches']);
Route::get('matching/job/{offreId}', [MatchingController::class, 'jobMatches']);

// Routes pour les conversations
Route::prefix('conversations')->middleware('auth:api')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ConversationController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\Api\ConversationController::class, 'store']);
    Route::get('/{id}', [\App\Http\Controllers\Api\ConversationController::class, 'show']);
});


// Routes  pour les messages
Route::prefix('messages')->middleware('auth:api')->group(function () {
    Route::post('/{conversationId}', [\App\Http\Controllers\Api\MessageController::class, 'store']);
    Route::put('/{message}', [\App\Http\Controllers\Api\MessageController::class, 'update']);
    Route::delete('/{message}', [\App\Http\Controllers\Api\MessageController::class, 'destroy']);
});


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api')->middleware('verified'); // Verified to ensure the user's email is verified

// Route to show the email verification notice
Route::get('/email/verify', function () {
    return response()->json(['message' => 'Check your mail to verify your account.']);
})->name('verification.notice');

// Route to verify the email (email verification handler)
Route::post('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerify'])->middleware('signed')->name('verification.verify');

// Route to resend the verification email
Route::post('/email/verification-notification', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.'], 400);
    }

    $user->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification email has been sent again. Check your mail to verify your account.']);
})->middleware('throttle:6,1')->name('verification.send');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'ResetPasswordNotif'])->middleware('guest')->name('password.email');
// Route::get('/reset-password/{token}', function (string $token) {
//     // return response()->json(['token' => $token]);
//     return view('auth.reset-password', ['token' => $token]);
// })->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'ResetPassword'])->middleware('guest')->name('password.update');

// Login with Google
Route::post('/google-login', [AuthController::class, 'googleLogin'])->middleware('guest');

// Login with LinkedIn
Route::get('/linkedin-login', [AuthController::class, 'linkedinLogin'])->middleware('guest');
Route::get('/linkedin-login-callback', [AuthController::class, 'handlelinkedinCallback']);

Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('a_products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('save', [ProductController::class, 'save']);
        Route::get('{id}/show', [ProductController::class, 'show']);
        Route::patch('{id}/update', [ProductController::class, 'update']);
        Route::delete('{id}/delete', [ProductController::class, 'delete']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/profile/particulier', [ParticulierProfileController::class, 'store']);
        Route::put('/profile/particulier', [ParticulierProfileController::class, 'update']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/profile/professionnel', [ProfessionnelProfileController::class, 'store']);
    });

    Route::middleware('auth:api')->group(function () {
        // Formations
        Route::apiResource('formations', FormationController::class)->only(['index', 'store', 'update', 'destroy']);

        // Expériences
        Route::apiResource('experiences', ExperienceController::class)->only(['index', 'store', 'update', 'destroy']);

        //Skills
        Route::apiResource('skills', SkillController::class);

        // Route::apiResource('offres', OffreEmploiController::class)->parameters([
        //     'offres' => 'offreEmploi'
        // ]);
        // Route::apiResource('candidatures', CandidatureController::class);
        Route::apiResource('invitations', InvitationController::class);
    });

    // Routes publiques
    Route::get('/offres', [OffreEmploiController::class, 'index']);
    Route::get('/offres/{offreEmploi}', [OffreEmploiController::class, 'show']);

    // Routes protégées (recruteurs connectés)
    Route::middleware('auth:api')->group(function () {
        Route::get('/mesoffres', [OffreEmploiController::class, 'mesOffres']);
        Route::post('/offres', [OffreEmploiController::class, 'store']);
        Route::put('/offres/{offreEmploi}', [OffreEmploiController::class, 'update']);
        Route::delete('/offres/{offreEmploi}', [OffreEmploiController::class, 'destroy']);
    });

    Route::middleware('auth:api')->group(function () {

        // Candidat : créer une candidature
        Route::post('candidatures', [CandidatureController::class, 'store'])
            ->middleware('can:isCandidat');

        //voir les détails d'une candidature
        Route::get('/candidatures/{candidature}', [CandidatureController::class, 'show']);

        // Candidat : voir toutes ses candidatures
        Route::get('candidatures/me', [CandidatureController::class, 'myCandidatures'])
            ->middleware('can:isCandidat');

        // Candidat : mettre à jour CV ou motivation
        Route::put('candidatures/{candidature}', [CandidatureController::class, 'update'])
            ->middleware('can:isCandidat');

        // Recruteur : voir toutes les candidatures pour ses offres
        Route::get('candidatures', [CandidatureController::class, 'index'])
            ->middleware('can:isRecruteur');

        //Recruteur : voir toutes les candidatures pour une offre precise
        Route::get('/offres/{offre}/candidatures', [CandidatureController::class, 'candidaturesByOffre'])
            ->middleware('can:isRecruteur');;

        // Recruteur : mettre à jour le statut ou la note IA
        Route::put('candidatures/{candidature}/status', [CandidatureController::class, 'updateStatus'])
            ->middleware('can:isRecruteur');

        // Recruteur : envoyer invitation entretien ou contrat
        Route::post('candidatures/{candidature}/invite', [CandidatureController::class, 'sendInvitation'])
            ->middleware('can:isRecruteur');
    });

});

