<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friends extends Model
{
    use HasFactory;

    public function parentUser() {
        return $this->belongsToMany(User::class, 'friends', 'parentId', 'childId')->withTimestamps();
    }

    public function childUser() {
        return $this->belongsToMany(User::class, 'friends', 'childId', 'parentId')->withTimestamps();
    }
}
