<?php
namespace App\Services;

use App\Exceptions\CantMakeGroupException;
use App\Models\Group;
use App\Models\File;
use App\Models\History_Log;
use Storage;
use Illuminate\Support\Facades\DB;
use App\Traits\GeneralTrait;

use App\Events\fileUploadedEvent;
use App\Events\fileCheckedInEvent;
use App\Events\fileUpdateEvent;
use App\Events\fileCheckedOutEvent;
use Illuminate\Support\Facades\Log;
use App\Repositories\File\FileRepository;

class fileService{

    use GeneralTrait;
    
    public FileRepository $FileRepository;

    public function __construct(FileRepository $FileRepository){
        $this->FileRepository = $FileRepository;
    }

    public function findFile($file_id){
        return $this->FileRepository->find($file_id);
    }

    public function uploadFileToFileSystem($group_id, $file){
        
        $group = Group::find($group_id);
        
        $saveFile = $this->saveFile($file, 'storage/groups/'.$group->name, $group_id);
        if($saveFile->original['status']==false)
            return ["status"=>false, "message"=>$saveFile->original['message']]; 
        return ["status"=>true, "fileName"=>$saveFile->original['fileName'], "filePath"=>$saveFile->original['filePath']]; 
  
    }

    public function addFileToDBFile($fileName, $filePath, $group_id, $user_id){
        try{

            DB::beginTransaction();
                $file = $this->FileRepository->create($fileName, $filePath, $group_id, $user_id);  
             DB::commit();
             event(new fileUploadedEvent($file));
             

             return  ["status"=>true, "message"=>"file added successfully to database ", "file"=>$file];

        }catch (\Exception $exception) {
            DB::rollback();
            return  ["status"=>false, "message"=>$exception->getMessage()];
        }
    }

    public function canDeleteThisFile($group_id, $file_id, $user_id){

        $file = $this->FileRepository->find($file_id)
                    ->where('id', $file_id)
                    ->where('user_id', $user_id)
                    ->get();
                    
        if(!$file->isEmpty())
            return true;
        return false;
    }

    public function deleteFileFromFileSystem($groupName, $fileName){
        try{

            Storage::delete('public/groups/'.$groupName.'/'.$fileName);
            return  ["status"=>true, "message"=>"file deleted successfully from the file system"];

        }catch (\Exception $exception) {
            return  ["status"=>false, "message"=>"error in deleting file from the file system"];
        }
    }

    public function deleteFileFromDataBase($file_id){
        $this->FileRepository->delete($file_id);
    }

    public function downloadFile($group_name, $file){
        try{
            
            $fileFromStorage = storage_path('app/public/groups/'.$group_name.'/'.$file->name);
            $pattern = "/./";
            $fileExtention = preg_split($pattern, $file->name);

                 $headers = [
                    'Content-Type' => 'application/'.$fileExtention[1],
                ];
               return  ["status"=>true, "headers"=>$headers, "fileFromStorage"=>$fileFromStorage];

        }catch (\Exception $exception) {
            return  ["status"=>false, "message"=>$exception->getMessage()];
        }
    }

    public function blockFile($file, $user_id, $request){
        try{
            DB::beginTransaction();
                $file->status = 1;
                $file->blocked_by = $user_id;
                $file->save();
            DB::commit();
            event(new fileCheckedInEvent($file));
            return  ["status"=>true, "message"=>"file updated successfully in database"];

        }catch (\Exception $exception) {
            DB::rollback();
            return  ["status"=>false, "message"=>$exception->getMessage()];
        }
    }

    public function replaceFile($originalFile, $user_id, $newFile, $request){
        try{
            DB::beginTransaction();
                $originalFile->name = $newFile->getClientOriginalName();
                $originalFile->save();
            DB::commit();
            event(new fileUpdateEvent($originalFile));
            return  ["status"=>true, "message"=>"file updated successfully in database"];

        }catch (\Exception $exception) {
            DB::rollback();
            return  ["status"=>false, "message"=>$exception->getMessage()];
        }
    }

    public function freeBlockedFile($file, $user_id, $request){
        try{
            DB::beginTransaction();
                $file->status = 0;
                $file->blocked_by = null;
                $file->save();
            DB::commit();
            event(new fileCheckedOutEvent($file));
            return  ["status"=>true, "message"=>"file freed successfully in database"];

        }catch (\Exception $exception) {
            DB::rollback();
            return  ["status"=>false, "message"=>$exception->getMessage()];
        }
    }
    public function bulkCheckIn($files, $user_id, $request){
        try{

            foreach ($files as $_file) {
                $blockedFile = $this->blockFile($_file, $user_id, $request);
                if($blockedFile['status']==false)
                    throw new Exception($blockedFile['message']);
            }
            return  ["status"=>true, "message"=>"all files checked-in successfully"];  
        }catch (\Exception $exception) {
            return  ["status"=>false, "message"=>$exception->getMessage()];
        }
    }
}


