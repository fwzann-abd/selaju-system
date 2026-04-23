<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Resolve the current user's role dynamically.
     *
     * - User model (admin dashboard) → resolve from userGroup relationship
     * - Account model (API) → resolve from teacher/student relationships
     *
     * @return string|null The resolved role, or null if no role can be determined.
     */
    private function resolveRole(mixed $user): ?string
    {
        // User model — admin dashboard users with userGroup
        if ($user instanceof \App\Models\User) {
            $group = $user->userGroup;

            return $group?->name ? strtolower($group->name) : null;
        }

        // Account model — API users (teacher, student, or plain account)
        if ($user instanceof \App\Models\Account) {
            if ($user->teacher()->exists()) {
                return 'teacher';
            }

            if ($user->student()->exists()) {
                return 'student';
            }

            return null;
        }

        return null;
    }

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $currentRole = $this->resolveRole($user);

        if (! $currentRole || ! in_array($currentRole, $roles)) {
            return response()->json(['message' => 'Forbidden - You do not have the required role.'], 403);
        }

        return $next($request);
    }
}
