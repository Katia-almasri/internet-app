<?php

namespace App\Http\Middleware;
use App\Traits\GeneralTrait;
use Closure;

class checkUserOwner
{
    use GeneralTrait;
    
    public function handle($request, Closure $next)
    {
        $userOwner = $request->user()->groups()
        ->wherePivot('group_id', '=', $request->group_id)
        ->wherePivot('is_owner', '1')
        ->get();
        if($userOwner->isEmpty())
            return  $this -> returnError('404', 'Unauthorized user you are not the owner');
        return $next($request);
    }
}
