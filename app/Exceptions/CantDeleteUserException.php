<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CantDeleteUserException extends Exception
{
    public function report(){
        Log::debug("cant`t delete user");
    }
}
