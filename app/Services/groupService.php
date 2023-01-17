<?php
namespace App\Services;

use App\Exceptions\CantMakeGroupException;
use App\Exceptions\CantDeleteUserException;
use App\Models\Group;
use App\Models\File;
use App\User;
use Storage;
use Illuminate\Support\Facades\DB;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Repositories\Group\GroupRepository;

class groupService{

    use GeneralTrait;
    
    public GroupRepository $GroupRepository;

    public function __construct(GroupRepository $GroupRepository){
        $this->GroupRepository = $GroupRepository;
    }

    public function findGroup($group_id){
        return $this->GroupRepository->find($group_id);
    }

    public function deleteGroupFromDataBase($group_id){
        $this->GroupRepository->delete($group_id);
    }

    public function isGroupPublic($group_id){
        if($group_id==1)
            return true;
        return false;
    }

    public function createGroupInDB($request){
        try{
            DB::beginTransaction();
            $group = $this->GroupRepository->create($request->name);
            $request->user()->groups()->attach($group->id, ['is_owner'=>1]);
            DB::commit();
            return  ["status"=>true, "message"=>"group created successfully"];

        }catch (\Exception $exception) {
            DB::rollback();
            return  ["status"=>false, "message"=>"error in adding the user"];
        }
    }

    public function addUserTpGroup($user_id, $group_id, $request) {
        try{
            DB::beginTransaction();
                $addedUser = User::find($user_id);
                $addedUser->groups()->attach($group_id, ['is_owner'=>0, 'date_of_joined'=>Carbon::now()]);
            DB::commit();
            return ["status"=>true, "message"=>"user added successfully"];
    
        }catch (\Exception $exception) {
            DB::rollback();
            return  ["status"=>false, "message"=>"error in adding the user"];
        }

    }

    public function IsFilesUserFreeInTheGroup($group_id, $user_id){
  
        $files = File::IsFileFree($group_id)
                        ->where('blocked_by', $user_id)
                        ->get();
        
        if($files->isEmpty())
            return ["status"=>true,  "message"=>"all files free"];

        return  ["status"=>false, "message"=>"there is a file blocked"];

    }

    public function isFilesFreeInTheGroup($group_id){
        $files = File::GetBlockedFile($group_id)->get();

        if(!$files->isEmpty())
            return ["status"=>false,  "message"=>"there is files blocked in this group"];
            
        return ["status"=>true,  "message"=>"all files free in this group"];
    
    }
   

}