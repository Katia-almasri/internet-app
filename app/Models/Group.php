<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Group extends Authenticatable 
{
    protected $table = 'groups';
    protected $primaryKey='id';
    protected $fillable = [
         'name'
    ];


    ############################# Begin usedRepoFunctione ######################

            public function getAll()
            {
                return static::all();
            }
        
        
            public function findGroup($id)
            {
                return static::find($id);
            }
        
        
            public function deleteGroup($id)
            {
                return static::find($id)->delete();
            }
        
            public function create(Request $request){
                
            }
        
    ############################# End usedRepoFunctione ########################
    

    ######################### Begin Relations #################################
    
    public function files(){
        return $this->hasMany('App\Models\File', 'group_id', 'id');
    }

    public function users(){
        return $this->belongsToMany('App\User', 'group_user', 'group_id', 'user_id')->withPivot('is_owner')->withTimestamps();
    }
    ######################### End Relations #################################
    ######################### Begin Local scopes ############################
    public function scopegetGroupsUsers($query, $group_id){
        return $query->find($group_id)->users();
    }

    public function scopeDisplayGroupsUserBelongTo($query, $user_id){
        return $query->whereHas('users', function($q) use($user_id){
                        $q->where('user_id', $user_id);
                    });
    }
    ######################### End Local scopes ##############################





}
