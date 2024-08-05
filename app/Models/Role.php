<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function users(){
        return $this->belongsToMany(User::class,'conversations_roles_users');
    }
    public function conversations(){
        return $this->belongsToMany(Conversation::class,'conversations_roles_users');
    }

}
