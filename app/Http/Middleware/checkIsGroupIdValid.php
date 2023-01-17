<?php

namespace App\Http\Middleware;
use App\Traits\GeneralTrait;

use App\Models\Group;

use Closure;

class checkIsGroupIdValid
{
   use GeneralTrait;
    public function handle($request, Closure $next)
    {
        $group_id = $request->group_id;
        $group = Group::find($group_id);
        if(!$group)
            return  $this -> returnError('404', 'group id is not valid');
        return $next($request);
    }
}
