<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'logging-middleware:user-api'], function(){
    Route::group(['middleware'=>'check-guards:user-api'], function(){
        Route::post('profile/create-group', 'UserController@createGroup');
        Route::get('profile/groups/display-groups-i-belong-to/', 'userController@displayGroupsIBelongTo');
        Route::get('profile/groups/display-files-i-uploaded/', 'userController@displayMyUploadedFiles');
        
        
        Route::group(['middleware'=>'check-group-id:user-api'], function(){

            Route::group(['middleware'=>'check-user-owner:user-api'], function(){

                Route::group(['middleware'=>'check-user-id:user-api'], function(){

                    Route::get('profile/groups/{group_id}/delete-user/{user_id}', 'userController@deleteUser');
                    Route::post('profile/groups/{group_id}/add-user', 'userController@addUser');
                });
                
                Route::group(['middleware'=>'check-file-id:user-api'], function(){
                    Route::get('profile/groups/{group_id}/delete-file/{file_id}', 'userController@DeleteFile');
                    
                    
                });
                Route::get('profile/groups/{group_id}/delete-group/', 'userController@deleteGroup');
                Route::post('profile/groups/{group_id}/add-file', 'userController@addFile')->middleware('check-max-num-file');
                
            });

            Route::group(['middleware'=>'check-file-id:user-api'], function(){
                                        /* Operations */
                Route::get('profile/groups/{group_id}/read-file/{file_id}', 'userController@readFile');
                Route::get('profile/groups/{group_id}/check-in-file/{file_id}', 'userController@checkIn');
                Route::get('profile/groups/{group_id}/edit-file/{file_id}', 'userController@editFile');
                Route::post('profile/groups/{group_id}/update-file/{file_id}', 'userController@updateFile');
                Route::get('profile/groups/{group_id}/check-out-file/{file_id}', 'userController@checkOut');
                                        /* history report */
                Route::get('profile/groups/{group_id}/export-history-report-to-file/{file_id}', 'HomeController@exportHistoryReportOfFile');
                
                
            });
                                        /* Operation */
            Route::post('profile/groups/{group_id}/bulk-check-in-file', 'userController@bulkCheckIn');

            Route::get('profile/groups/{group_id}/add-users', 'userController@addUserForm');
            Route::get('profile/groups/{group_id}/display-files-belong-to-group', 'userController@displayFilesBelongToGroup');
            Route::get('profile/groups/{group_id}/display-group-details', 'userController@displayGroupDetails');
            
        });
        
        Route::group(['middleware'=>'check-is-admin:user-api'], function(){
            
            Route::get('dashboard/groups/{group_id}/display-group-details', 'HomeController@displayGroupDetailsByAdmin')->middleware('check-is-group-id-valid');
            Route::get('dashboard/display-all-files', 'HomeController@displayAllFilesByAdmin');
            Route::get('dashboard/display-all-groups', 'HomeController@displayAllGroupsByAdmin');
            Route::get('profile/groups/{group_id}/export-history-report-to-file-by-admin/{file_id}', 'HomeController@exportHistoryReportOfFile');
            
        });
        
        
    });


    Route::group(['middleware'=>'guest:user-api'], function(){
        Route::post('register', 'UserController@register');
        
    });

});

    
