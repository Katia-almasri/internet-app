<?php

namespace App\Http\Middleware;
use App\Traits\GeneralTrait;

use Closure;

class checkIsAdmin
{
   use GeneralTrait;
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if($user->is_admin == 0)
            return  $this -> returnError('404', 'you are not admin');
        return $next($request);
    }
}
