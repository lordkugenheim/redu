<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    protected $fillable = [
        'follower_id',
        'following_id'
    ];

    public function follower(): HasOne
    {
        return $this->hasOne(User::class, 'user_id', 'follower_id');
    }

    public function following(): HasOne
    {
        return $this->hasOne(User::class, 'user_id', 'follower_id');
    }

}
