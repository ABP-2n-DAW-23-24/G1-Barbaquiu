<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Friend;
use App\Models\Product;
use App\Models\Barbecue;
use App\Models\BarbecueFriendship;
use App\Models\ChatMessage;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasFactory, Notifiable, \Staudenmeir\LaravelMergedRelations\Eloquent\HasMergedRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'surnames',
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

    
    public function sentFriendRequests()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')->withPivot('accepted')->withTimestamps();
    }

    public function recievedFriendRequests()
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')->withPivot('accepted')->withTimestamps();
    }

    public function acceptedSentFriendRequests()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')->wherePivot('accepted', true);
    }

    public function acceptedRecievedFriendRequests()
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')->wherePivot('accepted', true);
    }

    public function friends()
    {
        return $this->mergedRelationWithModel(User::class, 'friends_view');
    }

    public function pendingFriendsRequests()
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')->wherePivot('accepted', false);
    }

    public function alreadySentFriendRequestTo(User $user)
    {
        return $this->sentFriendRequests()->where('friend_id', $user->id)->exists();
    }

    public function alreadyHasFriendRequestFrom(User $user)
    {
        return $this->recievedFriendRequests()->where('user_id', $user->id)->exists();
    }

    public function isTheSameAs(User $user)
    {
        return $this->id === $user->id;
    }

    public function sendFriendRequest(User $user)
    {
        if ($this->isTheSameAs($user)) {
            return;
        }

        if ($this->alreadySentFriendRequestTo($user)) {
            return;
        }

        if ($this->alreadyHasFriendRequestFrom($user)) {
            $this->acceptFriendRequest($user);
            return;
        }

        $this->sentFriendRequests()->attach($user->id, ['accepted' => false]);
    }

    public function acceptFriendRequest(User $user)
    {
        $friendship = Friendship::where('user_id', $user->id)
            ->where('friend_id', $this->id)
            ->first();

        if ($friendship) {
            $friendship->accepted = true;
            $friendship->save();
        }
    }

    public function barbecues()
    {
        return $this->hasMany(Barbecue::class);
    }

    public function sendBarbecueJoinRequest(Barbecue $barbecue)
    {
        $barbecue->recieveJoinRequest($this);
    }

    public function acceptBarbecueInvitation(Barbecue $barbecue)
    {
        $barbecue->recieveInvitationAcceptance($this);
    }

}