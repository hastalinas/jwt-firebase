<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use phpDocumentor\Reflection\PseudoTypes\False_;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $role = $request->header('role');
        if(!$role){
            return response()->json([
                'status' => False,
                'message' => 'Anda tidak memiliki hak'
            ]);
        }
        if (in_array($role, $roles)) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'Tidak Memiliki Akses!'
        ], 400);
    }
}
