<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Repositories\User\userRepository;




class userService{

    use GeneralTrait;

    public userRepository $userRepository;

    public function __construct(userRepository $userRepository){
        $this->userRepository = $userRepository;
    }
    public function registerUser(Request $request){
        try{

            DB::beginTransaction();
                    $user = $this->userRepository->create($request);
                    $user->attachRole('user');
                    $user->attachRole('owner');
                    $user->groups()->attach('1', ["is_owner"=>1]);
            DB::commit();
            return  ["status"=>true, "message"=>"user added to database successfully"];

        }catch (\Exception $exception) {
            DB::rollback();
            return  ["status"=>false, "message"=>"error in adding the user"];
        }
    }

    public function userHasRole($role, $user){
        if(!Auth::guard('user-api')->user()->hasRole($role))
            $user->attachRole($role);
    }
}