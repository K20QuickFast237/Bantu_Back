<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
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
use App\Http\Controllers\Api\OffreCategorieController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\DeliveryMethodController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\Api\PosteRechercheController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Laravel\Socialite\Facades\Socialite;

// Broadcast::routes(['middleware' => ['auth:api']]);
Broadcast::routes(['middleware' => ['auth:api']]); //->middleware('auth:api');

Route::middleware('auth:api')->prefix('user')->group(function () {
    // Route::get('/{user}', fn(User $user) => response()->json(new UserResource($user))); //->middleware('verified'); // Verified to ensure the user's email is verified
    Route::get('/{user}', [AuthController::class, 'user']);
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
Route::get('/offres/categories-populaires', [OffreEmploiController::class, 'categoriesPopulaires']);
Route::get('/offres/niveaux', [OffreEmploiController::class, 'listNiveauExperience']);

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

//Routes pour gérer les CVs
Route::middleware('auth:api')->group(function () {
    Route::get('/cvs', [CvController::class, 'index']);
    Route::post('/cvs', [CvController::class, 'store']);
    Route::put('/cvs/{cv}', [CvController::class, 'update']);
    Route::delete('/cvs/{cv}', [CvController::class, 'destroy']);
});

// Routes pour gérer les categories (des offres)
Route::prefix('offres-categories')->group(function () {
    Route::get('/', [OffreCategorieController::class, 'index']);
    Route::get('/{id}', [OffreCategorieController::class, 'show']);
    Route::post('/', [OffreCategorieController::class, 'store']);
    Route::put('/{id}', [OffreCategorieController::class, 'update']);
    Route::delete('/{id}', [OffreCategorieController::class, 'destroy']);
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
Route::get('login', fn() => response()->json(['message' => 'Unauthanticated.'], 401))->name('login');
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



    Route::middleware('auth:api')->group(function () {
        Route::post('/profile/particulier', [ParticulierProfileController::class, 'store']);
        Route::put('/profile/particulier', [ParticulierProfileController::class, 'update']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/profile/professionnel', [ProfessionnelProfileController::class, 'store']);
        Route::put('/profile/professionnel/{professionnel}', [ProfessionnelProfileController::class, 'update']);
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
            
        // Candidat : voir toutes ses candidatures
        Route::get('candidatures/me', [CandidatureController::class, 'myCandidatures'])
            ->middleware('can:isCandidat');

        //voir les détails d'une candidature
        Route::get('candidatures/{candidature}', [CandidatureController::class, 'show']);

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

Route::prefix('shops')->group(function() {

    // ===============================
    // CÔTÉ VENDEUR
    // ===============================
    Route::middleware(['auth:api','can:isVendeur'])->group(function() {
        Route::post('/', [ShopController::class,'store']);           // créer boutique
        Route::put('/{shop}', [ShopController::class,'update']);     // modifier boutique
        Route::get('/my', [ShopController::class,'myShops']);        // voir ses boutiques
        Route::patch('/{shop}/toggle', [ShopController::class,'toggleStatus']); // activer/désactiver
    });

    // ===============================
    // CÔTÉ ADMIN
    // ===============================
    Route::middleware(['auth:api','can:isAdmin'])->group(function() {
        Route::get('/', [ShopController::class,'index']);            // lister toutes
        Route::patch('/{shop}/approve', [ShopController::class,'approve']);   // approuver
        Route::patch('/{shop}/reject', [ShopController::class,'reject']);     // refuser
        Route::patch('/{shop}/suspend', [ShopController::class,'suspend']);   // suspendre
        Route::delete('/{shop}', [ShopController::class,'destroy']);          // supprimer
    });

    // ===============================
    // CÔTÉ CLIENT
    // ===============================
    Route::get('/approved', [ShopController::class,'approvedShops']); // toutes approuvées
});

Route::prefix('categories')->group(function () {

    // ===============================
    // CÔTÉ ADMIN
    // ===============================
    Route::middleware(['auth:api','can:isAdmin'])->group(function() {
        Route::get('/', [CategoryController::class,'index']);          // Lister toutes
        Route::post('/', [CategoryController::class,'store']);         // Créer une catégorie
        Route::put('/{category}', [CategoryController::class,'update']);  // Modifier
        Route::delete('/{category}', [CategoryController::class,'destroy']); // Supprimer
    });

    // ===============================
    // CÔTÉ CLIENT / VENDEUR
    // ===============================
    Route::get('/publicCat', [CategoryController::class,'publicIndex']); // Liste publique
});

Route::prefix('tags')->group(function () {

    // ===============================
    // CÔTÉ ADMIN
    // ===============================
    Route::middleware(['auth:api','can:isAdmin'])->group(function() {
        Route::get('/', [TagController::class,'index']);         // lister tous les tags
        Route::post('/', [TagController::class,'store']);        // créer un tag
        Route::put('/{tag}', [TagController::class,'update']);   // modifier un tag
        Route::delete('/{tag}', [TagController::class,'destroy']); // supprimer un tag
    });

    // ===============================
    // CÔTÉ CLIENT / VENDEUR
    // ===============================
    Route::get('/publicTag', [TagController::class,'publicIndex']); // liste publique des tags
});

Route::prefix('products')->group(function() {

    // Côté vendeur
    Route::middleware(['auth:api','can:isVendeur'])->group(function() {
        Route::post('/', [ProductController::class,'store']);           // créer produit
        Route::put('/{product}', [ProductController::class,'update']);  // modifier
        Route::get('/my/{shop_id}', [ProductController::class,'myProducts']);     // mes produits
        Route::delete('/{product}', [ProductController::class,'destroy']);            // supprimer
    });

    // Côté admin
    Route::middleware(['auth:api','can:isAdmin'])->group(function() {
        Route::patch('/{product}/toggle', [ProductController::class,'toggleStatus']); // activer/désactiver
    });

    // Côté client
    Route::get('/shop/{shop_id}', [ProductController::class,'shopProducts']); // produits d'une boutique

    // Filtrer les produits par catégorie et/ou tags
    Route::get('/filter', [ProductController::class, 'filterProducts']);

});


Route::middleware(['auth:api'])->prefix('shops/{shopId}')->group(function () {

    // Gestion des modes de livraison pour une boutique
    Route::get('delivery-methods', [DeliveryMethodController::class, 'index']);
    Route::post('delivery-methods', [DeliveryMethodController::class, 'store']);
    Route::delete('delivery-methods/{deliveryMethodId}', [DeliveryMethodController::class, 'destroy']);

});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('poste-recherche', PosteRechercheController::class)->only(['index', 'store', 'update', 'destroy']);
});
