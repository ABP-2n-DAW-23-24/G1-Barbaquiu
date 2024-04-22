<?php

use App\Models\User;

describe('Friend requests', function() {
    test('User can send a friend request', function() {
        $user_1 = User::factory()->create();
        $user_2 = User::factory()->create();

        $user_1->sendFriendRequest($user_2);

        $users = User::all();

        expect($user_1->sentFriendRequests()->count())->toBe(1);
    });

    test('User recieves a friend request', function() {
        $user_1 = User::factory()->create();
        $user_2 = User::factory()->create();

        $user_1->sendFriendRequest($user_2);
        expect($user_2->recievedFriendRequests()->count())->toBe(1);
    });

    test('User can accept a friend request', function() {
        $user_1 = User::factory()->create();
        $user_2 = User::factory()->create();

        $user_1->sendFriendRequest($user_2);
        $user_2->acceptFriendRequest($user_1);

        expect($user_1->friends()->count())->toBe(1);
        expect($user_2->friends()->count())->toBe(1);
    });

    test('A user cannot send a friend request to themselves', function() {
        $user = User::factory()->create();
        $user->sendFriendRequest($user);

        expect($user->sentFriendRequests()->count())->toBe(0);
    });

    test('A user cannot accept a friend request that does not exist', function() {
        $user_1 = User::factory()->create();
        $user_2 = User::factory()->create();

        $user_1->acceptFriendRequest($user_2);

        expect($user_1->friends()->count())->toBe(0);
        expect($user_2->friends()->count())->toBe(0);
    });

    test('A user cannot accept a friend request that they did not recieve', function() {
        $user_1 = User::factory()->create();
        $user_2 = User::factory()->create();
        $user_3 = User::factory()->create();

        $user_1->sendFriendRequest($user_2);
        $user_3->acceptFriendRequest($user_1);

        expect($user_1->friends()->count())->toBe(0);
        expect($user_2->friends()->count())->toBe(0);
        expect($user_3->friends()->count())->toBe(0);
    });

    test('A user cannot send a friend request to the same user twice', function() {
        $user_1 = User::factory()->create();
        $user_2 = User::factory()->create();

        $user_1->sendFriendRequest($user_2);
        $user_1->sendFriendRequest($user_2);

        expect($user_1->sentFriendRequests()->count())->toBe(1);
        expect($user_2->recievedFriendRequests()->count())->toBe(1);
    });

    test('When a friendship is created, the database store the time that happened', function() {
        $user_1 = User::factory()->create();
        $user_2 = User::factory()->create();

        $user_1->sendFriendRequest($user_2);
        $user_2->acceptFriendRequest($user_1);

        $friendship = $user_1->friends()->first();

        expect($friendship->created_at)->not()->toBeNull();
    });
});