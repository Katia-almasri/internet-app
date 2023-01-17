<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CantDeleteFileException extends Exception
{
    public function report(){
        Log::debug("cant`t delete the file");
    }
}
