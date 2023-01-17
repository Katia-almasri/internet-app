<?php

namespace App\Http\Middleware;
use App\Traits\GeneralTrait;
use App\Models\File;
use Closure;

class checkFileId 
{
   use GeneralTrait;
    public function handle($request, Closure $next)
    {
        $file_id = $request->file_id;
        $file = File::where('group_id', $request->group_id)
                    ->where('id', $request->file_id)->get();
        
        if($file->isEmpty())
            return  $this -> returnError('', 'this file does not exist');
        return $next($request);
    }

    
}
