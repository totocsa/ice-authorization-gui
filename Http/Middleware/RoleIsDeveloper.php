<?php

namespace Totocsa\AuthorizationGUI\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleIsDeveloper
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasRole('Developer')) {
            //abort(403, 'Access denied');
            return redirect()->route('/');
        }

        return $next($request);
    }
}
