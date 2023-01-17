<?php

namespace App\Repositories\User;
use Illuminate\Http\Request;
use App\User;


interface UserInterface {


    public function getAll();


    public function find($id);


    public function delete($id);

    public function create(Request $request);
}