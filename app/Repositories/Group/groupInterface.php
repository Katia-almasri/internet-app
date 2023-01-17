<?php

namespace App\Repositories\Group;
use Illuminate\Http\Request;
use App\Models\Group;


interface groupInterface {


    public function getAll();


    public function find($id);


    public function delete($id);

   // public function create(Request $request, $group_id, $fileName, $filePath);

    
}