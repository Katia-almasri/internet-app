<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CantDeleteGroupException extends Exception
{
    public function report(){
        Log::debug("cant`t delete the group");
    }
}
