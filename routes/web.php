<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\userController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', [userController::class, 'index']);

Route::post('/user/create', function (Request $request) {

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => $request->pass,
    ]);

    return $user;

});

Route::post('/ptest', function () {
    return "asd";
});

