<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $currentRole = 'super_admin';
        
        if (method_exists($user, 'teacher') && $user->teacher()->exists()) {
            $currentRole = 'teacher';
        } elseif (method_exists($user, 'student') && $user->student()->exists()) {
            $currentRole = 'student';
        }

        if (!in_array($currentRole, $roles)) {
            return response()->json(['message' => 'Forbidden - You do not have the required role.'], 403);
        }

        return $next($request);
    }
}
