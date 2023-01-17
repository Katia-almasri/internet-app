<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogDataBase extends Model
{
    protected $table = 'logs';
    protected $primaryKey='id';
    protected $fillable = [
         'URI', 'METHOD', 'REQUEST_BODY', 'RESPONSE'
    ];
}
