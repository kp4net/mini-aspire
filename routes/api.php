<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\loansController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/auth/register', [AuthController::class, 'createUser'])->name('auth.signup');;
Route::post('/auth/login', [AuthController::class, 'loginUser'])->name('auth.login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/me', function (Request $request) {
        return auth()->user();
    })->name('auth.user');

    Route::resource('loans', loansController::class);

    Route::post('approveLoan/{id}', [loansController::class, 'approveLoan'])->name('approveLoan');
    Route::post('repayment/{id}', [loansController::class, 'repayment'])->name('repayment');

    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
