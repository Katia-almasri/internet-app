<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CantMakeFileException extends Exception
{
    public function report(){
        Log::debug("cant`t upload a new file");
    }

}
