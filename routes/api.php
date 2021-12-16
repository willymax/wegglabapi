<?php

use App\Http\Controllers\Api\V1\AnswerController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\SubjectController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UpdateUserPassword;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\PaypalPlanController;
use App\Http\Controllers\PaypalSubscriptionController;
use App\Mail\InvoicePaid;
use App\Models\PaypalSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::middleware(['guest'])->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/login', [AuthController::class, 'login']);
            Route::get('/login/{service}', [SocialLoginController::class, 'redirect']);
            Route::get('/login/{service}/callback', [SocialLoginController::class, 'callback']);
            Route::post('/social-login/google', [AuthController::class, 'googleSignIn']);
        });
    });

    Route::get('files/download/', function (Request $request) {
        return Storage::disk('public')->download($request->fileUrl);
    });


    Route::middleware('guest')->post('/sendTestMail', function () {
        Mail::to("admin@example.com")->send(new InvoicePaid());
    });

    Route::middleware('auth:sanctum')->group(
        function () {
            Route::prefix('auth')->group(
                function () {
                    Route::get('/user', function (Request $request) {
                        return $request->user();
                    });
                    Route::post('/logout', [AuthController::class, 'logout']);
                    Route::put('/password', [UpdateUserPassword::class, 'update']);
                    Route::get('/me', function (Request $request) {
                        return auth()->user();
                    });
                }
            );
            Route::prefix('billing')->group(
                function () {
                    Route::get('/billing-portal', function (Request $request) {
                        return $request->user()->redirectToBillingPortal(route('home'));
                    });
                    Route::get('/setup-intent', [UserController::class, 'getSetupIntent']);
                }
            );
            Route::prefix('subscriptions')->group(
                function () {
                    Route::post('/subscribeUser', [PaypalSubscriptionController::class, 'subscribeUser']);
                    Route::resource('paypalSubscriptions', PaypalSubscriptionController::class);
                }
            );
            Route::resource('plans', PaypalPlanController::class);
            Route::resource('answers', AnswerController::class);
            Route::resource('questions', QuestionController::class);
            Route::resource('subjects', SubjectController::class);
            Route::resource('users', UserController::class);
            Route::get('createOrGetStripeCustomer', [UserController::class, 'createOrGetStripeCustomer']);
            Route::post('updateAvatar', [UserController::class, 'updateAvatar']);
            Route::post('changePassword', [UserController::class, 'changePassword']);
            Route::get('getUserBalance', [UserController::class, 'getUserBalance']);
            //Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
        }
    );
    Route::get('/', function () {
        return 'Hello World';
    });
});

Route::middleware('auth:sanctum')->group(
    function () {
        // JsonApiRoute::server('v1')
        //     ->middleware()
        //     ->prefix('v1')
        //     ->resources(function ($server) {
        //         $server->resource('users', UserController::class);
        //         $server->resource('questions', QuestionController::class);
        //         $server->resource('answers', AnswerController::class);
        //     });
    }
);
