<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;
    
    protected $fillable = [
        "id",
        'user_id',
        'friend_id',
        'accepted'
    ];

    protected $table = 'friendships';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
