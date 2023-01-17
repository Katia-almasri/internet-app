<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CantMakeGroupException extends Exception
{
    public function report(){
        Log::debug("cant`t make a new group");
    }
}
