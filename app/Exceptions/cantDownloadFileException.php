<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class cantDownloadFileException extends Exception
{
    public function report(){
        Log::debug("cant`t download file");
    }
}
