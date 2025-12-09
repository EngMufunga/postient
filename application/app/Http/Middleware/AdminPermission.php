<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin->hasPermission($permission)) {
            $notify[] = ['error', 'You are not authorized to access this url'];
            return redirect()->route('admin.dashboard')->withNotify($notify);

        }
        return $next($request);
    }
}
