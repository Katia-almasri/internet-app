<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\GeneralTrait;
use App\Http\Controllers\Auth\LoginController;
use Validator;
use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use Auth;

class AdminController extends Controller
{
    public function index(){
        return 'katia';
    }

}
