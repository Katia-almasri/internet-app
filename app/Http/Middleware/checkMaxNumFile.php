<?php

namespace App\Http\Middleware;
use App\Traits\GeneralTrait;
use App\Models\File;
use Closure;

class checkMaxNumFile
{
   
    use GeneralTrait;

    public function handle($request, Closure $next)
    {
        $user_id = $request->user()->id;
        $numberFiles = File::where('user_id', $user_id)->count();
        if($numberFiles>100)
            return  $this -> returnError('', 'maximum number if files to upload is only 100');
        return $next($request);
    }
}
