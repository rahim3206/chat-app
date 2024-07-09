<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    public function group_members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function messages()
    {
        return $this->hasMany(GroupChat::class);
    }
}
