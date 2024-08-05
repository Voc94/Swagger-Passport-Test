<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens,HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function messages(){
        return $this->hasOne(Message::class);
    }
    public function invitations(){
        return $this->hasMany(Invitation::class);
    }
    public function friends(){
        return $this->belongsToMany(User::class, 'user_friends', 'user_id', 'friend_id');
    }
    public function roles(){
        return $this->belongsToMany(Role::class,'conversations_roles_users');
    }
    public function conversations(){
        return $this->belongsToMany(Conversation::class,'conversations_roles_users');
    }

}
