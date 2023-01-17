<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\GeneralTrait;
use App\User;

class checkUserId
{

    use GeneralTrait;

    public function handle($request, Closure $next)
    {
        $user_id = $request->user_id;
        $userExist = User::find($user_id);
        if(!$userExist)
            return  $this -> returnError('404', 'user is not exist');
        return $next($request);
    }
}
