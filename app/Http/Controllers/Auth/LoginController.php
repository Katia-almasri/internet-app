<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\AdminRequest;
use App\Http\serviceContainer\validation;
use App\Http\Requests\UserRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\GeneralTrait;
use Validator;
use Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    use GeneralTrait;

    protected $valid;

    protected $redirectTo = RouteServiceProvider::HOME;
    public function __construct(validation $valid)
    {
        $this->valid = $valid;
        $this->middleware('guest')->except(['logout']);
    }

    public function login(Request $request){
        try{

            $rules = [
                "name" => "required|max:255",
                "password" => "required|min:3|max:51"

            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($validator, $code);
            }

            // //login
                 
             $credentials = $request->only(['name', 'password']);
             $token = auth()->guard('user-api')->attempt($credentials);
            if (!$token)
                return $this->returnError('E001', 'error in inputs');
            $user = Auth::guard('user-api')->user();
            $user->api_token = $token;
           
            return $this->returnData('user', $user);

                 
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }  
    }

     public function logout(Request $request)
     {
     $token = $request -> header('auth-token');
     if($token){
         try {
                 JWTAuth::setToken($token)->invalidate(); //logout
             }
             catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                 return  $this -> returnError('','some thing went wrongs');
             }
             return $this->returnSuccessMessage('Logged out successfully');
         }
         else
         {
             $this -> returnError('','some thing went wrongs');
         }
    }
}