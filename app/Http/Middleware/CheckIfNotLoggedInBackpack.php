<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfNotLoggedInBackpack
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->wantsJson() || $request->ajax()) {
            if (!backpack_user()) {
               return response('Unauthorized.', Response::HTTP_UNAUTHORIZED);
            }
        }

        if (!backpack_user()) {
            return redirect()->guest(backpack_url('login'));
        }

        return $next($request);
    }
}
