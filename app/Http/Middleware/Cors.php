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

class Cors
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
        return $next($request)
        // ->header('Access-Control-Allow-Origin', '*')
        // ->header('Access-Control-Allow-Methods', "PUT,POST,GET,DELETE,OPTIONS")
        ->header('Allow', 'POST')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', '*')
        // ->header('Access-Control-Allow-Headers', '*')
        // ->header('Access-Control-Allow-Credentials', 'true')
        // ->header('Access-Control-Max-Age', '86400')
        ->header('Access-Control-Allow-Headers', 'Accept, Content-Type, Authorization, X-Requested-With')
        ;

        
    }
}
