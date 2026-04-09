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

        // Determine user's role
        $currentRole = 'super_admin';
        
        if (method_exists($user, 'teacher') && $user->teacher()->exists()) {
            $currentRole = 'teacher';
        } elseif (method_exists($user, 'student') && $user->student()->exists()) {
            $currentRole = 'student';
        }

        // Allow super_admin or teacher to access LMS master data endpoints
        if (in_array('super_admin', $roles)) {
            if ($currentRole === 'super_admin' || $currentRole === 'teacher') {
                return $next($request);
            }
        } elseif (in_array($currentRole, $roles)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden - You do not have the required role.'], 403);
    }
}
