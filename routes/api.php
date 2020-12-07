<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HabitationController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\OutputController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\AbonnementPonctuelController;
use App\Http\Controllers\AbonnementUserController;
use App\Http\Controllers\MontantController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});

/**
 * users
 */
Route::get('/users', [UserController::class, 'index']);

Route::get('/users/{id}',  [UserController::class, 'show']);

Route::put('/users/{id}',  [UserController::class, 'update']);

Route::delete('/users/{id}',  [UserController::class, 'destroy']);

/**
 * habitations
 */
Route::get('/habitations', [HabitationController::class, 'index']);

Route::post('/habitations',  [HabitationController::class, 'store']);

Route::get('/habitations/{id}',  [HabitationController::class, 'show']);

Route::put('/habitations/{id}',  [HabitationController::class, 'update']);

Route::delete('/habitations/{id}',  [HabitationController::class, 'destroy']);

/**
 * categories
 */
Route::get('/categories', [CategorieController::class, 'index']);

Route::post('/categories',  [CategorieController::class, 'store']);

Route::get('/categories/{id}',  [CategorieController::class, 'show']);

Route::put('/categories/{id}',  [CategorieController::class, 'update']);

Route::delete('/categories/{id}',  [CategorieController::class, 'destroy']);

/**
 * outputs
 */
Route::get('/outputs', [OutputController::class, 'index']);

Route::post('/outputs',  [OutputController::class, 'store']);

Route::get('/outputs/{id}',  [OutputController::class, 'show']);

Route::put('/outputs/{id}',  [OutputController::class, 'update']);

Route::delete('/outputs/{id}',  [OutputController::class, 'destroy']);

/**
 * questions
 */
Route::get('/questions', [QuestionController::class, 'index']);

Route::get('/questionsprop', [QuestionController::class, 'proposition']);

Route::post('/questions',  [QuestionController::class, 'store']);

Route::get('/questions/{id}',  [QuestionController::class, 'show']);

Route::put('/questions/{id}',  [QuestionController::class, 'update']);

Route::delete('/questionsprop/{id}', [QuestionController::class, 'destroyprop']);

Route::delete('/questions/{id}',  [QuestionController::class, 'destroy']);

/**
 * propositions
 */
Route::get('/propositions', [PropositionController::class, 'index']);

Route::post('/propositions',  [PropositionController::class, 'store']);

Route::get('/propositions/{id}',  [PropositionController::class, 'show']);

Route::put('/propositions/{id}',  [PropositionController::class, 'update']);

Route::delete('/propositions/{id}',  [PropositionController::class, 'destroy']);

/**
 * abonnements
 */
Route::get('/abonnements', [AbonnementController::class, 'index']);

Route::post('/abonnements',  [AbonnementController::class, 'store']);

Route::get('/abonnements/{id}',  [AbonnementController::class, 'show']);

Route::put('/abonnements/{id}',  [AbonnementController::class, 'update']);

Route::delete('/abonnements/{id}',  [AbonnementController::class, 'destroy']);

/**
 * abonnements_ponctuel
 */
Route::get('/abonnement_ponctuels', [AbonnementPonctuelController::class, 'index']);

Route::post('/abonnement_ponctuels',  [AbonnementPonctuelController::class, 'store']);

Route::get('/abonnement_ponctuels/{id}',  [AbonnementPonctuelController::class, 'show']);

Route::put('/abonnement_ponctuels/{id}',  [AbonnementPonctuelController::class, 'update']);

Route::delete('/abonnement_ponctuels/{id}',  [AbonnementPonctuelController::class, 'destroy']);

/**
 * abonnements_user
 */
Route::get('/abonnement_users', [AbonnementUserController::class, 'index']);

Route::post('/abonnement_users',  [AbonnementUserController::class, 'store']);

Route::get('/abonnement_users/{id}',  [AbonnementUserController::class, 'show']);

Route::put('/abonnement_users/{id}',  [AbonnementUserController::class, 'update']);

Route::delete('/abonnement_users/{id}',  [AbonnementUserController::class, 'destroy']);

/**
 * montant
 */
Route::get('/montants', [MontantController::class, 'index']);

Route::post('/montants',  [MontantController::class, 'store']);

Route::get('/montants/{id}',  [MontantController::class, 'show']);

Route::put('/montants/{id}',  [MontantController::class, 'update']);

Route::delete('/montants/{id}',  [MontantController::class, 'destroy']);

/**
 * type
 */
Route::get('/types', [TypeController::class, 'index']);

Route::post('/types',  [TypeController::class, 'store']);

Route::get('/types/{id}',  [TypeController::class, 'show']);

Route::put('/types/{id}',  [TypeController::class, 'update']);

Route::delete('/types/{id}',  [TypeController::class, 'destroy']);

/**
* attribution
*/
Route::get('/attributions', [AttributionController::class, 'index']);

Route::post('/attributions',  [AttributionController::class, 'store']);

Route::get('/attributions/{id}',  [AttributionController::class, 'show']);

Route::put('/attributions/{id}',  [AttributionController::class, 'update']);

Route::delete('/attributions/{id}',  [AttributionController::class, 'destroy']);
