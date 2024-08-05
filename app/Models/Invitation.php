<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;
    public function users(){
        return $this->belongsTo(User::class);
    }
    protected $fillable = [
        'user_id',
        'email',
        'status',
    ];
}
