<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        session()->put('lang', $this->getCode());
        app()->setLocale(session('lang',  $this->getCode()));
        return $next($request);
    }

    protected function getCode()
    {
        if (session()->has('lang')) {
            return session('lang');
        }
        $language = Language::where('is_default', 1)->first();
        return $language ? $language->code : 'en';
    }
}
