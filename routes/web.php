<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CareerMatchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('landing');
});
Route::get('terms', function () {
    return view('terms');
})->name('terms');
Route::get('privacy', function () {
    return view('privacy');
})->name('privacy');

Route::middleware(['auth','verified'])->group(function() {
    Route::get(
        '/user/queue/status',
        [UserController::class, 'queue_status']
    )->name('user.queue.status');

    Route::get(
        '/user/applications',
        [UserController::class, 'application_stats']
    )->name('user.applications');

    Route::get(
        '/applications',
        [ApplicationController::class, 'index']
    )->name('applications');

    Route::get(
        '/applications/ai-answer/{id}',
        [ApplicationController::class, 'aiAnswer']
    )->whereNumber('id')->name('applications.ai-answer');

    Route::get(
        '/applications/{applicationId}/ai-answer',
        [ApplicationController::class, 'aiAnswerByApplication']
    )->whereNumber('applicationId')->name('applications.ai-answer.by-app');

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    Route::get('/career-match', [CareerMatchController::class, 'index'])->name('career-match');
    Route::post('/career-match/weights', [CareerMatchController::class, 'saveWeights'])->name('career-match.weights');
    Route::post('/career-match/answer', [CareerMatchController::class, 'answer'])->name('career-match.answer');
    Route::post('/career-match/score', [CareerMatchController::class, 'score'])->name('career-match.score');
    Route::get('/career-match/history', [CareerMatchController::class, 'historyIndex'])->name('career-match.history');
    Route::delete('/career-match/history/{id}', [CareerMatchController::class, 'historyDestroy'])->whereNumber('id')->name('career-match.history.destroy');

    Route::middleware('subscription')->group(function () {
        Route::prefix('/apply')->group(function () {
            Route::get(
                '/',
                [ApplyController::class, 'index']
            )->name('apply');

            Route::post(
                '/save',
                [ApplyController::class, 'save']
            )->name('apply.save');

            Route::post(
                '/stop',
                [ApplyController::class, 'stop']
            )->name('apply.stop');

            Route::post(
                '/resume',
                [ApplyController::class, 'resume']
            )->name('apply.resume');
        });
    });


    Route::prefix('settings')->group(function() {
        Route::get(
            '/',
            [SettingsController::class, 'settings']
        )->name('settings');

        Route::put(
            '/profile/update',
            [SettingsController::class, 'upsert_user']
        )->name('settings.profile.update');

        Route::delete(
            '/profile',
            [SettingsController::class, 'destroy_user']
        )->name('settings.profile.destroy');

        Route::post(
            '/toggle-automation',
            [SettingsController::class, 'toggle_automation']
        )->name('settings.toggle-automation');

        Route::post('/ai-provider', [SettingsController::class, 'saveAiProvider'])->name('settings.ai-provider.save');
        Route::post('/ai-provider/test', [SettingsController::class, 'testAiProvider'])->name('settings.ai-provider.test');
        Route::post('/ai-profile', [SettingsController::class, 'saveAiProfile'])->name('settings.ai-profile.save');
    });

    Route::prefix('products')->group(function() {
       Route::get(
           '/compass-link',
           [ProductController::class, 'compass_link']
       )->name('products.compass-link');
    });


    Route::prefix('payment')->group(function() {
        Route::post(
            '/subscribe',
            [PaymentController::class, 'subscribe']
        )->name('payment.subscribe');
    });


    Route::prefix('billing')->group(function() {
        Route::get(
            '/',
            [BillingController::class, 'index']
        )->name('billing');

        Route::get(
            '/subscription',
            [BillingController::class, 'subscription']
        )->name('billing.subscription');

        Route::get(
            '/invoices',
            [BillingController::class, 'invoices']
        )->name('billing.invoices');

        Route::get(
            '/invoices/{invoice}/pay',
            [PaymentController::class, 'pay']
        )->name('billing.pay');

        Route::get(
            '/invoices/{invoice}',
            [BillingController::class, 'show']
        )->name('billing.invoice.show');

        Route::get(
            '/payments',
            [BillingController::class, 'payments']
        )->name('billing.payments');

        Route::get(
            '/packages',
            [BillingController::class, 'packages']
        )->name('billing.packages');

        Route::post(
            '/subscription/autorenew',
            [BillingController::class, 'toggleAutoRenew']
        )->name('billing.subscription.autorenew');

        Route::post(
            '/subscription/cancel',
            [BillingController::class, 'cancelSubscription']
        )->name('billing.subscription.cancel');
    });
    Route::prefix('connection')->group(function() {
        Route::get(
            '/connect/jobstreet', fn() => view(
                'connection.jobstreet')
        )->name('connection.jobstreet');

        Route::get('/connect/glints', fn() => view(
            'connection.glints')
        )->name('connection.glints');

        Route::post(
            '/{provider}/save-token',
            [ConnectionController::class, 'save_token']
        )->name('connection.save-token');

        Route::get(
            '/{provider}/connect',
            [ConnectionController::class, 'connect']
        )->name('connection.connect');

        Route::delete(
            '/{provider}/disconnect',
            [ConnectionController::class, 'disconnect']
        )->name('api.connection.disconnect');

        Route::post(
            '/{provider}/save-config',
            [ConnectionController::class, 'save_config']
        )->name('connection.save-config');
    });

});

require __DIR__.'/auth.php';
require __DIR__.'/webhook.php';
