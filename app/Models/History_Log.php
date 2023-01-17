<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History_Log extends Model
{
    protected $table = 'history_log';
    protected $primaryKey='id';
    protected $fillable = [
         'file_id', 'user_id', 'uploaded_date', 'checked_in_date', 'updated_date', 'checked_out_date'
    ];

    ###################### Begin Relations ###########################
    public function files(){
        return $this->belongsTo('App\Models\File', 'file_id', 'id');
    }

    public function users(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    ###################### End Relations #############################



}
