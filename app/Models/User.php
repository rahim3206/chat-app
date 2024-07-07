<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    
    public function group_chat()
    {
        return $this->hasMany(GroupChat::class);
    }
    public function group_member()
    {
        return $this->hasMany(GroupMember::class);
    }
    
    public function sentFriendRequests() {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function receivedFriendRequests() {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function notificationsender() {
        return $this->hasMany(Notification::class, 'sender_id');
    }
    public function group_last_seen() {
        return $this->hasMany(GroupMessageSeen::class, 'sender_id');
    }
}

