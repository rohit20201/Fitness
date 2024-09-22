<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

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


Route::post('/student-register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
	Route::prefix("admin")->group(function(){

        Route::prefix("blog")->group(function(){
            Route::post('create-update',[\App\Http\Controllers\Admin\BlogController::class,"createUpdate"]);
            Route::post('list',[\App\Http\Controllers\Admin\BlogController::class,"blogList"]);
            Route::get('edit/{blogId}',[\App\Http\Controllers\Admin\BlogController::class,"edit"]);
            Route::post('status-update',[\App\Http\Controllers\Admin\BlogController::class,"updateStatus"]);
        });

        Route::prefix("faq")->group(function(){
            Route::post('create-update',[\App\Http\Controllers\Admin\FaqController::class,"createUpdate"]);
            Route::post('list',[\App\Http\Controllers\Admin\FaqController::class,"faqList"]);
            Route::get('edit/{faqId}',[\App\Http\Controllers\Admin\FaqController::class,"edit"]);
            Route::post('status-update',[\App\Http\Controllers\Admin\FaqController::class,"updateStatus"]);
        });

        Route::prefix("banner")->group(function(){
            Route::post('create-update',[\App\Http\Controllers\Admin\HomeController::class,"createUpdateBanner"]);
            Route::post('list',[\App\Http\Controllers\Admin\HomeController::class,"bannerList"]);
            Route::get('edit/{bannerId}',[\App\Http\Controllers\Admin\HomeController::class,"editBanner"]);
            Route::post('status-update',[\App\Http\Controllers\Admin\HomeController::class,"updateBannerStatus"]);
        });

        Route::prefix("testimonial")->group(function(){
            Route::post('create-update',[\App\Http\Controllers\Admin\HomeController::class,"createUpdateTestimonial"]);
            Route::post('list',[\App\Http\Controllers\Admin\HomeController::class,"testimonialList"]);
            Route::get('edit/{testimonialId}',[\App\Http\Controllers\Admin\HomeController::class,"editTestimonial"]);
            Route::post('status-update',[\App\Http\Controllers\Admin\HomeController::class,"updateTestimonialStatus"]);
        });

        Route::post('update-web-information',[\App\Http\Controllers\Admin\HomeController::class,"updateWebInformation"]);
        Route::get('get-web-information',[\App\Http\Controllers\Admin\HomeController::class,"getWebInformation"]);
        
    });
    
    Route::get('/role-list', [AuthController::class, 'roleList']);
});

Route::post('/save-contact', [\App\Http\Controllers\HomeController::class, 'saveContact']);

