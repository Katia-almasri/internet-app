<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\LogDataBase;

class loggingMiddleware
{
   
    public function handle($request, Closure $next)
    {
        $flag = false;
        $response = $next($request);

        try{
            DB::beginTransaction();
                $log = new LogDataBase();
                $log->URI = $request->getUri();
                $log->METHOD = $request->getMethod();
                $log->REQUEST_BODY = json_encode($request->all());
                $log->RESPONSE = json_encode($response->getData());
                $log->save();
                 
            DB::commit();
            return $response;


        }catch (\Exception $exception) {
            DB::rollback();
            return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);    
    }    

    }

   
}
