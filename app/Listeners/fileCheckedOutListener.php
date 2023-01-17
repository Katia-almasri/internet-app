<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\fileCheckedOutEvent;

use App\Models\History_Log;
use Illuminate\Support\Facades\DB;


class fileCheckedOutListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(fileCheckedOutEvent $fileCheckedOutEvent)
    {
        $this->fileCheckOut($fileCheckedOutEvent->file);
    }

    public function fileCheckOut($file){
        try{
            DB::beginTransaction();
                $history_log = new History_Log();
                $history_log->file_id = $file->id;
                $history_log->user_id = $file->user_id;
                $history_log->checked_out_date = $file->updated_at;
                $history_log->save();
            DB::commit();

        }catch (\Exception $exception) {
            DB::rollback();
        }
    }
}
