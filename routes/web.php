<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Spatie\Permission\Models\Role;
use App\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/create-role', function () {

    //Role::create(['name' => 'admin']);
    //Role::create(['name' => 'member']);
    $userIds = User::all()->pluck('id');
    $role = 'admin';
    foreach ($userIds as $id){
        $user = User::find($id);
        dd($user);
        $user->assignRole($role);
        $role = 'member';
    }

    dd('Yes');
});
