<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Group;
use App\Models\File;
use App\Models\History_Log;
use Carbon\Carbon;
use App\User;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function displayGroups(Request $request){

        $groups = $request->user()->groups()->get();
        return response()->json(['groups'=>$groups]);
    }

   

    public function displayGroupDetailsByAdmin(Request $request, $group_id){

        $groupInfo = Group::find($group_id);
        $files = File::getFilesBelongToGroup($group_id)->with('users')->get();
        $groupUsers = Group::getGroupsUsers($group_id)->get();
        return response()->json(['groupInfo'=>$groupInfo, 'groupUsers'=>$groupUsers, 'files'=>$files]);
    }

    
public function displayUsers(Request $request){

    $users = User::get();
    return response()->json($users);
}

public function displayAllFilesByAdmin(Request $request){

    $files = File::with(['users', 'groups', 'blockedByUsers']) ->get();
    return response()->json($files);
}

public function displayAllGroupsByAdmin(Request $request){

    $groups = Group::with(['users'=> function ($q){
        $q->where('is_owner', '=', 1);
    }])->get();
    return response()->json($groups);
}

public function exportHistoryReportOfFile(Request $request, $group_id, $file_id){

    $file = History_Log::with('users', 'files')
                        ->where('file_id', $file_id)
                        ->orderByDesc('history_log.created_at')
                        ->get();
                        
    return response()->json($file);
    
}

 
function test(){
    //  $group = Group::find(55);
    // $filesBelongToGroup = File::getFilesBelongToGroup(55)->with('groups')->get();
    //   //Cache::put('group_'.$group->name, $filesBelongToGroup, now()->addDay());
    // //   Cache::add('group_public_1', File::getFilesBelongToGroup(1)->with('groups')->get(), now()->addDay());
    // //   Cache::forget('group_public_1');
    Cache::flush();
    // $result = null;
    // if(Cache::has('g')){

    //     $result = Cache::get('group_'.$group->name);
    // }

    // $files = cache('files', function (){
    //     return File::getFilesBelongToGroup(1)->with('groups')->get();
    // });
     
    // return response()->json($files);
}

public function index(){
    return view('home');
}
    
}
