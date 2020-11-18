<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {        
        // Check if user is logged in
        $token = $request->header('Authorization');
        $jwtAth = new \JWTAuth();
        $checkToken = $jwtAth->checkToken($token);
        
        if($checkToken){
            $data = $next($request);
            
            return $data;
        }else{
            $data = [
                    'status'    => 'error',
                    'code'    => 404,
                    'message'   => 'User is not logged'
                ];
            
            return response()->json($data, $data['code']);
        }
        
    }
}
