<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Demo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')){
            $notify[] = ['warning', 'You can not change anything over this demo'];
            $notify[] = ['info', 'This version is for demonstration purposes only and few actions are blocked'];
            return back()->withNotify($notify);
        }
        return $next($request);
    }
}
