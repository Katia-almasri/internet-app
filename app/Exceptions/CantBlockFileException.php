<?php

namespace App\Exceptions;
use Illuminate\Support\Facades\Log;

use Exception;

class CantBlockFileException extends Exception
{
    public function report(){
        Log::debug("cant`t block the file");
    }
}
