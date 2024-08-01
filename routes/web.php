<?php

use App\Models\Conversation;
use App\Models\Invitation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/user', function (){
    $user = User::findOrFail(1);
    echo $user->name;
});

Route::get('/user/invitations', function (){
   $invitation = Invitation::whereUserId(2);
   foreach ($invitation->get() as $invitation){
       echo $invitation->email.'<br>';
   }
 //  echo $invitation->email.'<br>';
});

Route::get('/user/conversations', function (){
    $user = User::findOrFail(4);

    foreach ($user->conversations as $conversation){
        echo $conversation->name.'<br>';
    }
});
Route::get('/user/message/conversations', function (){
    $message=Message::findOrFail(1);
    $conversation=Conversation::findOrFail(1);
    if($conversation->id == $message->conversation->id){
        $user=User::whereId($message->from_user_id)->first();
        echo $user->name.'<br>';
    }
});

Route::post('/register', 'App\Http\Controllers\UserController@register');
Route::post('/login', 'App\Http\Controllers\LoginController@login');
Route::get('/user', 'App\Http\Controllers\UserController@getUserDetails')->middleware('auth:sanctum');
