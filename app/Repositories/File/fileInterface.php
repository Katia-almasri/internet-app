<?php

namespace App\Repositories\File;
use Illuminate\Http\Request;
use App\Models\File;


interface fileInterface {


    public function getAll();


    public function find($id);


    public function delete($id);

    public function create(Request $request, $group_id, $fileName, $filePath);

    
}