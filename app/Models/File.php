<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

class File extends Authenticatable 
{
    protected $table = 'files';
    protected $primaryKey='id';
    protected $fillable = [
        'group_id', 'user_id', 'name', 'path', 'status', 'blockedBy' //id
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

   
    public function getJWTCustomClaims()
    {
        return [];
    } 


        ############################# Begin usedRepoFunctione ######################

        public function getAll()
        {
            return static::all();
        }
    
    
        public function findFile($id)
        {
            return static::find($id);
        }
    
    
        public function deleteFile($id)
        {
            return static::find($id)->delete();
        }
    
        public function create(Request $request){
            
        }
    
    ############################# End usedRepoFunctione ########################
    

    ############################## Begin Relations #############################
    public function users(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function blockedByUsers(){
        return $this->belongsTo('App\User', 'blocked_by', 'id');
    }

    public function groups(){
        return $this->belongsTo('App\Models\Group', 'group_id', 'id');
    }

    public function history_log(){
        return $this->hasMany('App\Models\History_Log', 'file_id', 'id');
    }

    ############################## End Relations ##############################

    ############################## Begin Local Scope ##########################
    public function scopegetFilesBelongToGroup($query, $group_id){
        return $query->where('group_id', $group_id);
    }

    public function scopeIsFileFree($query, $group_id){
        return $query->where('group_id', $group_id)
                    ->where('status', 0);
    }

    public function scopeGetBlockedFile($query, $group_id){
        return $query->where('group_id', $group_id)
        ->where('status', 1);
    }

    public function scopeCheckFilesFree($query, $fileList){
        return $query->whereIn('id', $fileList)->where('status', 0);
    }

    public function scopeGetAllUploadedFileInGroup($query, $user_id){
        return $query->with('groups')->where('user_id', $user_id);
    }
    ############################## End Local Scope ##############################

}
