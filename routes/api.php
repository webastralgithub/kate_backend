<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\SymptomController;
use App\Http\Controllers\SymptomTrakingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeWorkController;
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
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//   Start Authentication
    Route::post('socialLogin',[ApiController::class,'socialLogin']);
    Route::get('socialLogin',[ApiController::class,'socialLogin']);
    Route::post('login', [ApiController::class, 'authenticate']);
    Route::post('register', [ApiController::class, 'register']);
    Route::post('forgot', [ApiController::class, 'forgot']);
    Route::post('verify', [ApiController::class, 'VerifyCode']);
    Route::post('reset_password', [ApiController::class, 'ResetPassword']);
    Route::get('getcountry', [ApiController::class, 'getcountry']);
    Route::get('getstates/{countryid}', [ApiController::class, 'getstates']);
    Route::get('getcities/{stateid}', [ApiController::class, 'getcities']);

//    End Authentication

Route::group(['middleware' => ['jwt.verify']], function() {

//   Start User Api
    Route::post('logout', [ApiController::class, 'logout']);
    Route::post('change_password', [ApiController::class, 'change_password']);
    Route::get('profile', [ApiController::class, 'get_user']);
    Route::get('user_list', [ApiController::class, 'user_list']);
    Route::get('trainer', [ApiController::class, 'trainer']);
    Route::get('trainertoclient', [ApiController::class, 'trainertoclient']);
    Route::get('add_client_to_trainer/{id}', [ApiController::class, 'add_client_to_trainer']);
    Route::post('update_profile', [ApiController::class, 'update_profile']);
    Route::post('deleteAccount', [ApiController::class, 'deleteAccount']);
    Route::get('edit_profle/{id}', [ApiController::class, 'edit_profle']);

//      End User Api


//   Start Note
    Route::resource('note', NoteController::class);
//    End Note//

//   Start Goal
    Route::resource('goal', GoalController::class);
//    End Goal
    //   Start Symptom
    Route::resource('symptom', SymptomController::class);
//    End Symptom
    //   Start SymptomTracking
    Route::resource('symptom_tracking', SymptomTrakingController::class);
    Route::post('symptom_tracking_by_date/{id}', [SymptomTrakingController::class,'index']);
//    End SymptomTracking

//     Start SymptomTracking
    Route::resource('event', EventController::class);
//    End SymptomTracking

//     Start HomeWork
    Route::resource('homework', HomeWorkController::class);
    Route::post('updatehomework/{id}', [HomeWorkController::class,'update']);
//    End HomeWork

});



