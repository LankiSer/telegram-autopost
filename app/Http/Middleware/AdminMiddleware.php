<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, является ли пользователь администратором
        if ($request->user() && $request->user()->email === 'admin@admin.com') {
            return $next($request);
        }

        // Если не админ, перенаправляем на дашборд
        return redirect()->route('dashboard');
    }
} 