<?php

namespace App\Http\Middleware;

use App\Support\AuditLogger;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $request->user()) {
            return $response;
        }

        if ($request->isMethod('get') || $request->isMethod('head')) {
            return $response;
        }

        $route = $request->route();
        $routeName = $route?->getName() ?? 'unknown';
        $method = strtoupper($request->method());

        $entityType = null;
        $entityId = null;

        if ($route) {
            foreach ($route->parameters() as $param) {
                if ($param instanceof Model) {
                    $entityType = $param->getMorphClass();
                    $entityId = (int) $param->getKey();
                    break;
                }
            }
        }

        $action = match ($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => strtolower($method),
        };

        $payloadKeys = array_keys($request->except([
            'password',
            'password_confirmation',
            'current_password',
            'token',
        ]));

        AuditLogger::log(
            action: "admin.{$action}",
            entityType: $entityType,
            entityId: $entityId,
            description: "{$method} {$routeName}",
            meta: [
                'route' => $routeName,
                'path' => $request->path(),
                'payload_keys' => $payloadKeys,
            ]
        );

        return $response;
    }
}
