<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;


use Illuminate\Http\Request;

class User extends Authenticatable implements JWTSubject
{

    use LaratrustUserTrait;

    protected $table = 'users';
    protected $primaryKey='id';
   
    

    protected $fillable = [
        'name', 'email', 'password', 'is_admin'
    ];

   
    protected $hidden = [
        'password', 'remember_token',
    ];

   
    protected $casts = [
        'email_verified_at' => 'datetime',
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


    public function findUser($id)
    {
        return static::find($id);
    }


    public function deleteUser($id)
    {
        return static::find($id)->delete();
    }

    public function create(Request $request){

    }

    ############################# End usedRepoFunctione ########################


    ############################# Begin Relations ################################
    public function files(){
        return $this->hasMany('App\Models\File', 'user_id', 'id');
    }

    public function history_log(){
        return $this->hasMany('App\Models\History_Log', 'user_id', 'id');
    }

    public  function groups(){
        return $this->belongsToMany('App\Models\Group', 'group_user', 'user_id', 'group_id')->withPivot('is_owner', 'date_of_joined')->withTimestamps();
    }
    ############################# End Relations ################################


    ############################# Begin Local scopes ###########################
    
    public function scopeIsUserOwnerOfGroup($query, $group_id){
        return $query->wherePivot('group_id', '=', $group_id)
        ->wherePivot('is_owner', '=', 1);
    }

    public function scopeIsAddedUserBelongsToGroup($query, $group_id){
        return $query->groups()->wherePivot('group_id', '=', $group_id);
    }

    public function scopeGetAllUserNotBelongToGroup($query, $group_id, $user_id){
        return $query->where('id', '!=', $user_id)

                ->whereHas('groups', function($q) use($group_id){
                    $q->where('group_id', '!=', $group_id);
                })
                ->orWhereDoesntHave('groups');
                
    }

    public function scopeGetAllGroupsUserBelongTo($query, $user_id, $group_id){
        return $query->find($user_id)
                    ->groups()
                    ->wherePivot('group_id', '=', $group_id);
}
    ############################# End Local scopes #############################
    

}
