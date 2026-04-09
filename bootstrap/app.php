<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'razorpay/webhook',
            'paypal/webhook',
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin/*') || $request->routeIs('admin.*')) {
                return route('admin.login');
            }

            if ($request->routeIs('customer.*') || $request->is('customer/*')) {
                return route('customer.login');
            }

            // Storefront areas that use auth:customer must not send guests to admin login
            if ($request->routeIs(
                'frontend.account.*',
                'frontend.loyalty.index',
                'frontend.orders.index',
                'frontend.orders.refund',
                'frontend.reviews.food.store',
                'frontend.reviews.branch.store',
            )) {
                return route('customer.login');
            }

            return route('admin.login');
        });

        $middleware->alias([
            'audit.log' => \App\Http\Middleware\AuditLogMiddleware::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
