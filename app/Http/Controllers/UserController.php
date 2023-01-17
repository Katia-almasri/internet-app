<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRequest;


use App\Repositories\User\userRepository;
use App\Repositories\File\FileRepository;
use App\Repositories\Group\GroupRepository;


use App\Traits\GeneralTrait;
use App\User;
use App\Models\Group;
use App\Models\File;
use App\Models\History_Log;


use App\Http\serviceContainer\validation;
use App\Http\serviceContainer\filesManager;

use App\Services\fileService;
use App\Services\groupService;
use App\Services\userService;

use App\Exceptions\CantMakeFileException;
use App\Exceptions\CantMakeGroupException;
use App\Exceptions\CantDeleteFileException;
use App\Exceptions\CantDeleteUserException;
use App\Exceptions\CantDeleteGroupException;
use App\Exceptions\CantDownloadFileException;
use App\Exceptions\CantUpdateFileException;
use App\Exceptions\CantFreeFileException;
use App\Exceptions\CantBlockFileException;
use App\Exceptions\CantCreateUserException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;


use Validator;
use Response;
use Auth;
use Storage;
use Carbon\Carbon;

            /******** controllers shuld only call the business logic inside services ********/

class UserController extends Controller
{
    use GeneralTrait;

    protected validation $validation;
    protected filesManager $filesManager;

    protected $fileService;
    protected $groupService;
    protected $userService;


    public function __construct(validation $validation, filesManager $filesManager)
    {
         $this->validation = $validation;
         $this->filesManager = $filesManager;

         $this->fileService = new fileService(new FileRepository(new File));
         $this->groupService = new groupService(new GroupRepository(new Group));
         $this->userService = new userService(new userRepository(new User));
    }

    public function register(Request $request)
    { 
                
        try{
            $validator =$this->validation->checkValidationUserRegisterInfo($request);
             if($validator['error']!=null)
                return  response()->json(["error"=>$validator['error'], "errNum"=>$validator['errNum']]);

            $registerUserInDB = $this->userService->registerUser($request);
            if($registerUserInDB['status']==false)
                throw new CantCreateUserException($registerUserInDB['message']);

            return (new LoginController(new validation))->login($request); 

        }catch (\Exception $exception) {
            return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
        }

}


public function createGroup(Request $request){

        try {

            $validator =$this->validation->checkValidationGroupValidation($request);
            if($validator['error']!=null)
               return  response()->json(["error"=>$validator['error'], "errNum"=>$validator['errNum']]);

            $this->userService->userHasRole('owner', $request->user());
            $createGroupInDB = $this->groupService->createGroupInDB($request);
            if($createGroupInDB['status']==false)
                throw new CantMakeGroupException($createGroupInDB['message']);

            $addGroupToFileSystem = $this->filesManager->addGroupToFileSystem($request->name, 'storage/groups');
            if(!$addGroupToFileSystem)
                    throw new CantMakeGroupException($addGroupToFileSystem['message']);

            return  response()->json(["status"=>true, "message"=>"group created successfully"], 200);

    }catch (\Exception $exception) {
        return  response()->json(["status"=>false, "message"=>$exception->getMessage()], 400);
    }
    
}

public function addFile(Request $request, $group_id){
    
    try{
        $validator =$this->validation->checkValidationFileValidation($request);
        if($validator['error']!=null)
           return  response()->json(["error"=>$validator['error'], "errNum"=>$validator['errNum']]);
        
           
           $uploadedFile = $this->fileService->uploadFileToFileSystem($group_id, $request->file);
           if($uploadedFile['status']==false)
               throw new CantMakeFileException($uploadedFile['message']);

           $addedFile = $this->fileService->addFileToDBFile($uploadedFile['fileName'],
                                                            $uploadedFile['filePath'],
                                                            $group_id, $request->user()->id);
                
       return response()->json(["status"=>true, "message"=>$addedFile['file']]);
       
   }catch (\Exception $exception) {
    return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
}
    
}

public function DeleteFile(Request $request, $group_id, $file_id){
    
    $file = $this->fileService->findFile($file_id);
    $group = $this->groupService->findGroup($group_id);
   
     try{

        if(!$this->fileService->canDeleteThisFile($group_id, $file_id, $request->user()->id))
            throw new CantDeleteFileException("you dont have role to delete this file");

            if($file->status==0){
                $fileDeleted = $this->fileService->deleteFileFromFileSystem($group->name, $file->name);
                $this->fileService->deleteFileFromDataBase($file_id);    
                return response()->json(["status"=>true, "message"=>$fileDeleted['message']]);
           }
           return response()->json(["status"=>false, "message"=>"this file is blocked"]);
     }
     catch (\Exception $exception) {
         return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
     }

}


public function addUser(Request $request, $group_id){

    try{

        if($this->groupService->isGroupPublic($group_id))
             throw new CantDeleteUserException("you dont have role to add user to public group");

        $addedUser = $this->groupService->addUserTpGroup($request->user_id, $group_id, $request);
        if($addedUser['status']==true)
            return  response()->json(["status"=>true, "message"=>$addedUser['message']]);
             
    }catch (\Exception $exception) {
        return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
    }

}

public function addUserForm(Request $request, $group_id){

    /* display all users dont belong to this group to show
    / them in a form to select one to add him to this group*/

    $users = User::GetAllUserNotBelongToGroup($group_id, $request->user()->id)->get();                         
    return response()->json($users);
}

public function deleteUser(Request $request, $group_id, $user_id){

    try{
        if($this->groupService->isGroupPublic($group_id))
            throw new CantDeleteUserException("you dont have role to delete user from public group");

        $isAddedUserBelongToGroup = User::GetAllGroupsUserBelongTo($user_id, $group_id)->get();

        if($isAddedUserBelongToGroup->isEmpty())
            throw new CantDeleteUserException("user does not belong to this group");

        $IsFilesUserFreeInTheGroup = $this->groupService->IsFilesUserFreeInTheGroup($group_id, $user_id);
        if($IsFilesUserFreeInTheGroup['status']==false)
            throw new CantDeleteUserException($IsFilesUserFreeInTheGroup['message']);

        $user = User::GetAllGroupsUserBelongTo($user_id, $group_id)->detach($group_id);
        return response()->json(["status"=>true, "message"=>"user deleted successfully"]);

    }catch (\Exception $exception) {
        return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
    }

           
}

public function deleteGroup(Request $request, $group_id){

    try{
        if($this->groupService->isGroupPublic($group_id))
            throw new CantDeleteGroupException("you dont have role to delete public group");

        $isFilesFreeInTheGroup = $this->groupService->isFilesFreeInTheGroup($group_id);
        if($isFilesFreeInTheGroup['status']==false)
            throw new CantDeleteGroupException($isFilesFreeInTheGroup['message']);

         //1. delete group from storage
        $deleteGroupFromFileSystem = $this->filesManager->deleteGroupFromFileSystem($group_id);
        if($deleteGroupFromFileSystem['status']==false)
            throw new CantDeleteGroupException($deleteGroupFromFileSystem['message']);
       
        //2. delete group from database
        $group = Group::find($group_id)->delete();
        return response()->json(["status"=>true, "message"=>"group deleted successfully"]);
    }catch (\Exception $exception) {
        return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
    }
    
}

public function readFile(Request $request, $group_id, $file_id){

    $file = $this->fileService->findFile($file_id);
    if($file->status== '0')
        return response()->json(["status"=>true, "file"=>$file, "mode"=> "r"]);

    return response()->json(["status"=>false, "message"=>"this file is blocked you cant read it"]);
    
}

public function checkIn(Request $request, $group_id, $file_id){

    $file = $this->fileService->findFile($file_id);
        try{
            if($file->status==0){
                $blockedFile = $this->fileService->blockFile($file, $request->user()->id, $request);
                if($blockedFile['status']==false)
                    throw new CantBlockFileException($blockFile['message']);  
    
                return response()->json(["status"=>true, "message"=>"file ".$file->name." checked in successfully"]);

            }
            else if($file->status==1 && $file->blocked_by==$request->user()->id)
                return response()->json(["status"=>true, "message"=>" you have actually blocked file ".$file->name]);

            else if($file->status==1 && $file->blocked_by != $request->user()->id)
                return response()->json(["status"=>true, "message"=>"this file is actually blocked by someone else"]);

        }catch (\Exception $exception) {

            return  response()->json(["status"=>false, "message"=>"cant block this file"]);    
        } 
       
}

public function editFile(Request $request, $group_id, $file_id){

    $file = $this->fileService->findFile($file_id);
    $group = $this->groupService->findGroup($group_id);

    try{
        if($file->status==1)
        {
            $downloaddFile = $this->fileService->downloadFile($group->name, $file);
            if($downloaddFile['status']==false)
                throw new CantDownloadFileException($downloaddFile['message']);

                return response()->json(["status"=>true, "file"=>$file]);        
                //return response()->download($downloaddFile['fileFromStorage'], $file->name, $downloaddFile['headers']);

        }
        return response()->json(["status"=>false, "message"=>"this file is not bloacked"]);

    }catch (\Exception $exception) {
        return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
    }
}

public function updateFile(Request $request, $group_id, $file_id){

    
    try{

        $validator =$this->validation->checkValidationFileValidation($request);
        if($validator['error']!=null)
           return  response()->json(["error"=>$validator['error'], "errNum"=>$validator['errNum']]);

        $group = $this->groupService->findGroup($group_id);
        $file = $this->fileService->findFile($file_id);

        if($file->status==1 && $file->blocked_by == $request->user()->id){
            
            $fileDeleted = $this->fileService->deleteFileFromFileSystem($group->name, $file->name);
            if($fileDeleted['status']==false)
                throw new CantDeleteFileException($fileDeleted['message']);
            
            $uploadedFile = $this->fileService->uploadFileToFileSystem($group_id, $request->file, $request);
            if($uploadedFile['status']==false)
                throw new CantMakeFileException($uploadedFile['message']);
            
            $replaceFile = $this->fileService->replaceFile($file, $request->user()->id, $request->file, $request);
            if($replaceFile['status']==false)
                throw new CantUpdateFileException($replaceFile['message']);

            return response()->json(["status"=>true, "message"=>"file is updated successfully"]);
        }

        return response()->json(["status"=>false, "message"=>"this file is free or blocked by someone else"]);

        }catch (\Exception $exception) {
        return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
    }

}

public function checkOut(Request $request, $group_id, $file_id){
    try{

        $file = $this->fileService->findFile($file_id);

        if($file->status==1 && $file->blocked_by == $request->user()->id){
            $freeBlockedFile = $this->fileService->freeBlockedFile($file, $request->user()->id, $request);
            if($freeBlockedFile['status']==false)
                throw new CantFreeFileException($freeBlockedFile['message']);

        return  response()->json(["status"=>true, "message"=>"file checked out successfully"]); 

        }
        return  response()->json(["status"=>false, "message"=>"this file is free or blocked by someone else"]);

    }catch (\Exception $exception) {
        return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
    }
}

public function bulkCheckIn(Request $request, $group_id){
    try{

        $fileList = $request['files'];
        $files = File::CheckFilesFree($fileList)->get();

        if(!$files->isEmpty()){
            $checkedInFiles = $this->fileService->bulkCheckIn($files, $request->user()->id, $request);
            if($checkedInFiles['status']==false)
                throw new  CantBlockFileException($checkedInFiles['message']);

            return  response()->json(["status"=>true, "message"=>$checkedInFiles['message']]); 
        }
        return  response()->json(["status"=>false, "message"=>"cant checkin all files"]); 

    }catch (\Exception $exception) {
        return  response()->json(["status"=>false, "message"=>$exception->getMessage()]);
    }
     
}

public function displayGroupsIBelongTo(Request $request){
    $user_id = $request->user()->id;

    $groups = Group::DisplayGroupsUserBelongTo($request->user()->id)->get();
    return  response()->json($groups);
}
public function displayMyUploadedFiles(Request $request){

    $files = File::GetAllUploadedFileInGroup($request->user()->id)->get();
    return  response()->json($files);
}

public function displayFilesBelongToGroup(Request $request, $group_id){

    // if(!Cache::has($group_id)){
    //     Cache::put($group_id, File::getFilesBelongToGroup($group_id)->with('users')->get(), now()->addDay());
    // }
    // $cachedFiles = Cache::get($group_id);
    // return response()->json($cachedFiles);

    $cachedFile = Cache::remember($group_id, 10, function() use($group_id){
        return File::getFilesBelongToGroup($group_id)->with('users')->get();
      });
      return response()->json($cachedFile);
}


public function displayGroupDetails(Request $request, $group_id){

    $groupInfo = $this->groupService->findGroup($group_id);
    $files = File::getFilesBelongToGroup($group_id)->with('users')->get();
    $groupUsers = Group::getGroupsUsers($group_id)->get();
    
    return response()->json(['groupInfo'=>$groupInfo, 'groupUsers'=>$groupUsers, 'files'=>$files]);
}
    
}
