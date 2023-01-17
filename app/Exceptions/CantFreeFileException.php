<?php

namespace App\Exceptions;
use Illuminate\Support\Facades\Log;

use Exception;

class CantFreeFileException extends Exception
{
    public function report(){
        Log::debug("cant`t block the file");

        Log::debug("Action log debug test", ['my-string' => 'log me', "run"]);
    }
}
