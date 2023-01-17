<?php

namespace App\Repositories\File;

use App\Repositories\File\fileInterface as FileInterface;
use App\Models\File;
use Illuminate\Http\Request;

class FileRepository implements FileInterface
{
    public $file;


    function __construct(File $file) {
	    $this->file = $file;
    }


    public function getAll()
    {
        return $this->file->getAll();
    }


    public function find($id)
    {
        return $this->file->findFile($id);
    }


    public function delete($id)
    {
        return $this->file->deleteFile($id);
    }

    public function create($fileName, $filePath, $group_id, $user_id){
        
        $file = new File();
        $file->group_id = $group_id;
        $file->user_id = $user_id;
        $file->status = 0;
        $file->name = $fileName;
        $file->path = $filePath;   
        $file->save();
        
        return $file;
    }

    
}