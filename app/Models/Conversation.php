<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function users(){
        return $this->belongsToMany(User::class,'conversations_roles_users');
    }
    public function roles(){
        return $this->belongsToMany(Role::class,'conversations_roles_users');
    }
    public function messages(){
        return $this->hasMany(Message::class);
    }
}
