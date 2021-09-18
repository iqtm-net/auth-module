<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Namshi\JOSE\SimpleJWS;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Exception;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    
    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */ 

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {   

        $token = JWTAuth::getToken(); 

        if (!$request->header('Authorization')) { 
            return Result('The token could not be parsed from the request', 401);
        }
        
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return Result('Token is Expired', 401 );
        } catch(Exception $e) {
            return Result('Token is Invalid', 401 );
        }

        if (!user()) {
            return Result('Token is Invalid', 401);
        }

        if (!user()->confirmed) {
            return Result('Account is not active please contact our support center for more details.', 401);
        }

        return $next($request);

    }
}
