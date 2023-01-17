<?php
 namespace App\Http\serviceContainer;
 use Validator;
 use Auth;
 use App\User;
 use App\Models\Group;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\File;
 use App\Traits\GeneralTrait;

class filesManager {

    public function addGroupToFileSystem($name, $path){
        return File::makeDirectory($path.'/'.$name, 0777, true, true);
    }

    public function deleteGroupFromFileSystem($group_id){   
        $group = Group::find($group_id);
        $path = storage_path().'/app/public/groups/'.$group->name;
        File::deleteDirectory($path);
            return ["status"=>true,  "message"=>"group deleted succssfully from fileSystem"];
        
        return ["status"=>false,  "message"=>"error in delete group from fileSystem"];
    }
}