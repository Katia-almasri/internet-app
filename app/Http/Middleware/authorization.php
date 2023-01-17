<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Traits\GeneralTrait;

class authorization
{
    
    use GeneralTrait;

    public function handle($request, Closure $next, $guard = null)
    {   if($guard!=null)
            if($guard=='user-api'){
                $user = Auth::guard('user-api')->user();
                if($user->hasRole('owner'))
                    return $next($request);
                
            }
        return  $this -> returnError('401','unauthorized user');
    }
    
}
