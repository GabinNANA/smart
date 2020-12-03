<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HabitationController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\OutputController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\PropositionController;
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

Route::post('/questions',  [QuestionController::class, 'store']);

Route::get('/questions/{id}',  [QuestionController::class, 'show']);

Route::put('/questions/{id}',  [QuestionController::class, 'update']);

Route::delete('/questions/{id}',  [QuestionController::class, 'destroy']);

/**
 * propositions
 */
Route::get('/propositions', [PropositionController::class, 'index']);

Route::post('/propositions',  [PropositionController::class, 'store']);

Route::get('/propositions/{id}',  [PropositionController::class, 'show']);

Route::put('/propositions/{id}',  [PropositionController::class, 'update']);

Route::delete('/propositions/{id}',  [PropositionController::class, 'destroy']);