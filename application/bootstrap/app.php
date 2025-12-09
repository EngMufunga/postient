<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Configuration\Exceptions;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using:function(){
            Route::namespace('App\Http\Controllers')->group(function(){
                Route::prefix('api')
                    ->middleware(['api'])
                    ->group(base_path('routes/api.php'));
                Route::middleware(['web'])
                    ->namespace('Admin')
                    ->prefix('admin')
                    ->name('admin.')
                    ->group(base_path('routes/admin.php'));

                    Route::middleware(['web', 'maintenance'])
                    ->namespace('Gateway')
                    ->prefix('ipn')
                    ->name('ipn.')
                    ->group(base_path('routes/ipn.php'));

                Route::middleware(['web', 'maintenance'])->prefix('user')->group(base_path('routes/user.php'));
                Route::middleware(['web', 'maintenance'])->group(base_path('routes/web.php'));

            });
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('web',[
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LanguageMiddleware::class,
        ]);

         $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.api' => \App\Http\Middleware\AuthenticateApi::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'maintenance' => \App\Http\Middleware\MaintenanceMode::class,

            'admin' => \App\Http\Middleware\RedirectIfNotAdmin::class,
            'admin.guest' => \App\Http\Middleware\RedirectIfAdmin::class,
            'admin.permission' => \App\Http\Middleware\AdminPermission::class,
            'user.permission' => \App\Http\Middleware\UserPermission::class,

            'registration.status' => \App\Http\Middleware\AllowRegistration::class,
            'check.status' => \App\Http\Middleware\CheckStatus::class,
            'demo' => \App\Http\Middleware\Demo::class,
            'registration.complete' => \App\Http\Middleware\RegistrationStep::class,
            'plan.check' => \App\Http\Middleware\PlanChecker::class,
        ]);

        $middleware->validateCsrfTokens(
            except: ['user/deposit','ipn*']
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function () {
            if (request()->is('api/*')) {
                return true;
            }
        });
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 401) {
                if (request()->is('api/*')) {
                    $notify[] = 'Unauthorized request';
                    return response()->json([
                        'remark' => 'unauthenticated',
                        'status' => 'error',
                        'message' => ['error' => $notify]
                    ]);
                }
            }

            return $response;
        });
    })->create();



