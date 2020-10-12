<?php

namespace App\Http\Middleware;

use App\Helpers\JwtAuth;
use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth();
        $loggedUser = $jwtAuth->checkToken($token,true);
        if ($loggedUser) {
            $request->attributes->add(['loggedUser' => $loggedUser]);
            return $next($request);
        }else{
            //echo "sorry brou"; exit(); //Test
            return response()->json(array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Token not validated',
            ), 400);
        }
    }
}
