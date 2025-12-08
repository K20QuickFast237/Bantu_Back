<?php

use Illuminate\Support\Facades\Route;

// AUTH CONTROLLERS
use App\Http\Controllers\Auth\AuthController;

// PRODUITS
use App\Http\Controllers\Produit\ProduitController;
use App\Http\Controllers\Produit\CategorieProduitController;

// ACHETEUR
use App\Http\Controllers\Acheteur\PanierController;
use App\Http\Controllers\Acheteur\FavoriController;
use App\Http\Controllers\Acheteur\CommandeController;
use App\Http\Controllers\Paiement\ModePaiementController;
use App\Http\Controllers\Livraison\OptionLivraisonController;
// PAIEMENT
use App\Http\Controllers\Paiement\PaiementController;

// ATTRIBUTS
use App\Http\Controllers\Produit\AttributController;
use App\Http\Controllers\Produit\AttributValeurController;

// Route::prefix('v1')->group(function () {

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('register-acheteur', [AuthController::class, 'registerAcheteur']);
    Route::post('register-vendeur', [AuthController::class, 'registerVendeur']);
    Route::post('login', [AuthController::class, 'login']);
});


/*
|--------------------------------------------------------------------------
| PRODUITS PUBLICS
|--------------------------------------------------------------------------
*/
Route::get('produits', [ProduitController::class, 'index']);
Route::get('produits/search', [ProduitController::class, 'search']);
Route::get('produits/categorie/{categorie_id}', [ProduitController::class, 'prodcat']);
Route::get('produits/{id}', [ProduitController::class, 'show']);

Route::get('categories', [CategorieProduitController::class, 'index']);
Route::get('categories/{id}', [CategorieProduitController::class, 'show']);


/*
|--------------------------------------------------------------------------
| ROUTES ACHETEUR (protégées)
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth:api', 'role:acheteur'])->group(function () {
Route::middleware(['auth:api'])->group(function () {

    // PANIER
    Route::prefix('panier')->group(function () {
        Route::get('/', [PanierController::class, 'index']);
        Route::post('/', [PanierController::class, 'store']);
        // Route::put('/{id}', [PanierController::class, 'update']);
        Route::delete('/', [PanierController::class, 'flushPanier']);
        Route::delete('/{id}', [PanierController::class, 'destroy']);
    });

    // FAVORIS
    Route::prefix('favoris')->group(function () {
        Route::get('/', [FavoriController::class, 'index']);
        Route::post('/', [FavoriController::class, 'store']);
        Route::delete('/{id}', [FavoriController::class, 'destroy']);
    });

    // COMMANDES
    Route::prefix('commandes')->group(function () {
        Route::get('/', [CommandeController::class, 'index']);
        Route::get('/all', [CommandeController::class, 'listAll']);
        Route::get('/{id}', [CommandeController::class, 'show']);
        Route::post('/', [CommandeController::class, 'store']);
    });

    // PAIEMENTS
    Route::prefix('paiements')->group(function () {
        Route::get('/', [PaiementController::class, 'index']);
        Route::post('initiate', [PaiementController::class, 'initiate']);
    });
});


/*
|--------------------------------------------------------------------------
| ROUTES VENDEUR (protégées)
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth:api', 'role:vendeur'])->group(function () {
Route::middleware('auth:api')->group(function () {

    Route::prefix('vendeur')->group(function () {

        // PRODUITS VENDEUR
        Route::prefix('produits')->group(function () {
            Route::post('/', [ProduitController::class, 'store']);
            Route::post('/{id}/media', [ProduitController::class, 'addProduitMedia']);
            Route::post('/{id}/attributs', [ProduitController::class, 'addProduitAttributValeur']);
            Route::get('/', [ProduitController::class, 'vendeurProduits']);
            Route::put('/{id}', [ProduitController::class, 'update']);
            Route::delete('/{id}', [ProduitController::class, 'destroy']);
            Route::delete('/{produit_id}/media/{media_id}', [ProduitController::class, 'deleteProduitMedia']);
            Route::delete('/{produit_id}/attributs/{attribut_valeur_id}', [ProduitController::class, 'deleteProduitAttributValeur']);
        });

        // OPTIONS DE LIVRAISON (CRUD optionnel)
        Route::post('option-livraisons', [OptionLivraisonController::class, 'addVendeurOption']);
        Route::get('option-livraisons', [OptionLivraisonController::class, 'mesOptions']);
        Route::get('{id}/option-livraisons', [OptionLivraisonController::class, 'mesOptions']);
        Route::delete('option-livraisons/{id}', [OptionLivraisonController::class, 'delVendeurOption']);

        // MODE DE PAIEMENT (CRUD optionnel)
        Route::post('mode-paiements', [ModePaiementController::class, 'addVendeurMode']);
        Route::get('mode-paiements', [ModePaiementController::class, 'mesModes']);
        Route::get('{id}/mode-paiements', [ModePaiementController::class, 'mesModes']);
        Route::delete('mode-paiements/{id}', [ModePaiementController::class, 'delVendeurMode']);
    });
});
Route::middleware('auth:api')->apiResource('attributs', AttributController::class)->only([
    'index','store','show','update','destroy'
]);
Route::middleware('auth:api')->apiResource('attribut-valeurs', AttributValeurController::class)->only([
    'index','store','show','update','destroy'
]);
Route::middleware('auth:api')->apiResource('categorie-produits', CategorieProduitController::class)->only([
    'index','store','show','update','destroy'
]);
Route::middleware('auth:api')->apiResource('mode-paiements', ModePaiementController::class)->only([
    'index','store','update','destroy'
]);
Route::middleware('auth:api')->apiResource('option-livraisons', OptionLivraisonController::class)->only([
    'index','store','update','destroy'
]);
// });
