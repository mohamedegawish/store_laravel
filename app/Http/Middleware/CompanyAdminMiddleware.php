<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'company_admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Company Admin access only.'
                ], 403);
            }
            abort(403, 'غير مصرح لك بالدخول، هذه الصفحة مخصصة لمديري الشركات فقط.');
        }

        return $next($request);
    }
}
