<?php

namespace App\Http\Middleware;

use App\Traits\GeneralTrait;
use Closure;

class checkGroupId
{
    use GeneralTrait;

    public function handle($request, Closure $next)
    {
        
        $user = $request->user();
        $group_id = $request->group_id;
        $group = $user->groups()->wherePivot('group_id', $group_id)->get();
        
        if($group->isEmpty())
            return  $this -> returnError('404', 'Unauthorized user to this group');
        return $next($request);
            
    }
}
