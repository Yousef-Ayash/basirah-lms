<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the authenticated user has at least one of the
     * required roles. It accepts multiple roles as arguments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  A list of role names (e.g., 'admin', 'editor').
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. First, check if the user is authenticated at all.
        // A guest user cannot have a role.
        if (!Auth::check()) {
            // If you want to be explicit, you can redirect them to the login page.
            // return redirect('login');

            // Or, more commonly for protected routes, abort with an "Unauthorized" error.
            abort(401, 'Unauthorized');
        }

        // 2. Get the currently authenticated user model.
        $user = Auth::user();

        // 3. Use the `hasAnyRole` method from the Spatie package.
        // This method is efficient and checks if the user has any of the roles
        // passed to the middleware. The roles are passed as an array.
        if ($user->hasAnyRole($roles)) {
            // 4. If the user has one of the required roles, allow the request
            // to proceed to the next middleware or the controller.
            return $next($request);
        }

        // 5. If the user does not have any of the required roles,
        // stop the request and return a "Forbidden" HTTP error.
        abort(403, 'FORBIDDEN: YOU DO NOT HAVE THE REQUIRED ROLE TO ACCESS THIS RESOURCE.');
    }
}
