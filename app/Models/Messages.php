<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;

    public function senderUser() {
        return $this->belongsToMany(User::class, 'messages', 'senderId', 'receiverId')->withTimestamps();
    }

    public function receiverUser() {
        return $this->belongsToMany(User::class, 'messages', 'receiverId', 'senderId')->withTimestamps();
    }
}
