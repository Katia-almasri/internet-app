<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware'=>'logging-middleware'], function(){
    Route::group(['middleware'=>'guest', 'namespace'=>'Auth'], function(){
        Route::post('login', 'LoginController@login');    
        
    });

    Route::group(['middleware'=>'check-guards', 'namespace'=>'Auth'], function(){
        Route::post('logout', 'LoginController@logout');
    });

});


