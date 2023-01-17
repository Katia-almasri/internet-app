<?php

namespace App\Exceptions;
use Illuminate\Support\Facades\Log;

use Exception;

class CantUpdateFileException extends Exception
{
    public function report(){
        Log::debug("cant`t delete the file");
    }
}
