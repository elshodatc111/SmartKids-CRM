<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin{
    public function handle(Request $request, Closure $next): Response{
        if ($request->user()?->type !== 'admin') {
            return response()->json([
                'message' => 'Sizda ushbu amal uchun ruxsat yo‘q'
            ], 403);
        }
        return $next($request);
    }
}
