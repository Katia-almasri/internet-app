<?php
 namespace App\Http\serviceContainer;
 use Validator;
 use Auth;
 use Illuminate\Http\Request;
 use App\Traits\GeneralTrait;

class validation {
    use GeneralTrait;

    public function checkValidationUserRegisterInfo(Request $request){

        $rules = [
            "email" => "required|email|max:255|unique:users,email|unique:admins,email",
            "password" => "required|min:3|max:51",
            "name" => "required|min:3|max:51|unique:users,name|unique:admins,name",

       ];
        $validator = Validator::make($request->all(),$rules);

       if ($validator->fails())  
            return (["status"=>false, 'error'=>$validator->errors(), "errNum"=>401]); 

        return (["status"=>true, "error"=>"", "message"=>"validation true",  "errNum"=>""]); 

    }

    public function  checkValidationGroupValidation(Request $request){

        $validator = Validator::make($request->all(), 
        [ 
            "name" => "required|unique:groups,name"
        ]);  
        if ($validator->fails()) 
            return (["status"=>false, 'error'=>$validator->errors(), "errNum"=>401]);

        return (["status"=>true, "error"=>"", "message"=>"validation true",  "errNum"=>""]); 
        
    }

    public function checkValidationFileValidation(Request $request){

        $validator = Validator::make($request->all(), 
        [ 
            "file" => "required"
        ]);  
        if ($validator->fails()) 
                return (["status"=>false, 'error'=>$validator->errors(), "errNum"=>401]); 

        return (["status"=>true, "error"=>"", "message"=>"validation true",  "errNum"=>""]); 
    }

    public function checkLogin($credentials){

        $adminToken = auth()->guard('admin-api')->attempt($credentials);
        if($adminToken){
            $admin = Auth::guard('admin-api')->user();
            $admin->api_token = $adminToken;
            return $this->returnData('admin', $admin);
        }

        else if (!$adminToken){
            $userToken = auth()->guard('user-api')->attempt($credentials);
            if($userToken){
                $user = Auth::guard('user-api')->user();
                $user->api_token = $userToken;
                return $this->returnData('user', $user);
            }
        else 
            return  $this -> returnError('','invalid inputs');  
        }
    }

    
}
