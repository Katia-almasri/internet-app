<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\fileCheckedInEvent;
use Illuminate\Support\Facades\DB;
use App\Models\History_Log;


class fileCheckedInListener
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
    public function handle(fileCheckedInEvent $fileCheckedInEvent)
    {
        $this->checkInFile($fileCheckedInEvent->file);
    }

    public function checkInFile($file){
         try{
            DB::beginTransaction();
                $history_log = new History_Log();
                $history_log->file_id = $file->id;
                $history_log->user_id = $file->blocked_by;
                $history_log->checked_in_date = $file->updated_at;
                $history_log->save();
            DB::commit();

        }catch (\Exception $exception) {
            DB::rollback();
        }
    }
}
