<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\fileUploadedEvent;
use Illuminate\Support\Facades\DB;
use App\Models\History_Log;


class fileUploadedListenr
{
    
    public function __construct()
    {
        //
    }

    public function handle(fileUploadedEvent $fileUploadedEvent)
    {
        $this->uploadFile($fileUploadedEvent->file);
    }

    public function uploadFile($file){
        try{
            DB::beginTransaction();
                $history_log = new History_Log();
                $history_log->file_id = $file->id;
                $history_log->user_id = $file->user_id;
                $history_log->uploaded_date = $file->created_at;
                $history_log->save();
            DB::commit();

        }catch (\Exception $exception) {
            DB::rollback();
        }
    }
}
