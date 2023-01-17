<?php

namespace App\Http\Middleware;

use App\Traits\GeneralTrait;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use JWTAuth;

class checkGuards {
    use GeneralTrait;

    public function handle($request, Closure $next, $guard = null)
    {
        if($guard != null){

             
            //  DB::beginTransaction();
            //     $log = [
            //         'URL'=>$request->getUri(),
            //         // 'METHOD'=>$request->getMethod(),
            //         // 'BODY'=>$request->all(),
            //         // 'URL'=>$response->getContent()
            //     ];
            //     Log::info('app.requests', ['log' => $log]);
            //  DB::commit();
            //  DB::rollback();
            
            
            auth()->shouldUse($guard); 
            $token = $request->header('auth-token');
            $request->headers->set('auth-token', (string) $token, true);
            $request->headers->set('Authorization', 'Bearer '.$token, true);
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException $e) {
                return  $this -> returnError('401','Unauthenticated user ');
            } catch (JWTException $e) {

                return  $this -> returnError('', 'token_invalid'.$e->getMessage());
            }

        }
        return $next($request);
    }

    // public function terminate($request, $response)
    // {

    //     Log::info('URL: '.$request->fullUrl());
    //     Log::info('Method: '.$request->getMethod());
    //     Log::info('IP Address: '.$request->getClientIp());
    //     Log::info('Status Code: '.$response->getStatusCode());
    // }

   
}
