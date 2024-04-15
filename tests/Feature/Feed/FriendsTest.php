<?php

use App\Models\Friend;
use App\Models\User;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNull;

// ------------------------------ Get user friends ------------------------------------
test('get user friends', function () {
    $user = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

test('get user friends with no friends', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('feed/friends');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get user friends with pending friends', function () {
    $user = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'pending'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends');
    $response->assertStatus(200);
    $response->assertJsonCount(2);
});

test('test get user friends with blocked friends', function () {
    $user = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'blocked'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends');
    $response->assertStatus(200);
    $response->assertJsonCount(2);
});

test('test get user friends with declined friends', function () {
    $user = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'declined'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends');
    $response->assertStatus(200);
    $response->assertJsonCount(2);
});

test('test get user friends with mixed friends', function () {
    $user = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'accepted'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'pending'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'blocked'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

// ------------------------------ Get user friends by id ------------------------------
test('get user friends by id', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'accepter_id' => $friend->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/' . $friend->id);
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

test('get user friends by id with no friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $response = $this->actingAs($user)->getJson('feed/friends/' . $friend->id);
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get user friends by id with pending friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'accepter_id' => $friend->id,
        'status' => 'pending'
    ]);
    Friend::factory(2)->create([
        'accepter_id' => $friend->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/' . $friend->id);
    $response->assertStatus(200);
    $response->assertJsonCount(2);
});

test('test get user friends by id with blocked friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'accepter_id' => $friend->id,
        'status' => 'blocked'
    ]);
    Friend::factory(2)->create([
        'accepter_id' => $friend->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/' . $friend->id);
    $response->assertStatus(200);
    $response->assertJsonCount(2);
});

test('test get user friends by id with declined friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'accepter_id' => $friend->id,
        'status' => 'declined'
    ]);
    Friend::factory(2)->create([
        'accepter_id' => $friend->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/' . $friend->id);
    $response->assertStatus(200);
    $response->assertJsonCount(2);
});

test('test get user friends by id with mixed friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friends = Friend::factory(3)->create([
        'accepter_id' => $friend->id,
        'status' => 'accepted'
    ]);
    Friend::factory(2)->create([
        'accepter_id' => $friend->id,
        'status' => 'pending'
    ]);
    Friend::factory(2)->create([
        'accepter_id' => $friend->id,
        'status' => 'blocked'
    ]);
    Friend::factory(2)->create([
        'accepter_id' => $friend->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/' . $friend->id);
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

test('test get user friends by id with invalid friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('feed/friends/100');
    $response->assertStatus(422);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['user_id']);
});

test('test get user friends with string as friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('feed/friends/string');
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['user_id']);
});

// ------------------------------ Add friend ------------------------------------------
test('add friend', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $response = $this->actingAs($user)->postJson('feed/friends/' . $friend->id);
    $response->assertStatus(200);
    $response->assertJson(['requester_id' => $user->id, 'accepter_id' => $friend->id, 'status' => 'pending']);
});

test('add friend with invalid friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('feed/friends/100');
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['friend_id']);
});

test('add friend with string as friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('feed/friends/string');
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['friend_id']);
});

test('add friend with user as friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('feed/friends/' . $user->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'You cannot send a friend request to yourself']);
});

test('add friend with friend as user id', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($friend)->postJson('feed/friends/' . $user->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'User is already a friend']);
});

test('add friend with friend request already sent', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->postJson('feed/friends/' . $friend->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Friend request already sent']);
});

test('add friend with friend request already received', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->postJson('feed/friends/' . $friend->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Already received friend request from requested user']);
});

test('add friend with user is already a friend', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->postJson('feed/friends/' . $friend->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'User is already a friend']);
});

test('add friend with requested user when blockd', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->postJson('feed/friends/' . $friend->id);
    $response->assertStatus(200);
    $response->assertJson(['requester_id' => $user->id, 'accepter_id' => $friend->id, 'status' => 'pending']);
});

test('add friend with requested user when you are blocked', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->postJson('feed/friends/' . $friend->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Requested user has blocked you']);
});

test('add friend with requested user when friend request is declined', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->postJson('feed/friends/' . $friend->id);
    $response->assertStatus(200);
    $response->assertJson(['requester_id' => $user->id, 'accepter_id' => $friend->id, 'status' => 'pending']);
});

test('add friend with requested user when already declined', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->postJson('feed/friends/' . $friend->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Friend request already declined']);
});

test('add friend with requested user id as a string', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('feed/friends/string');
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['friend_id']);
});

// ------------------------------ Remove friend ---------------------------------------
test('remove friend', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->deleteJson('feed/friends/' . $friend->id);
    assertNull(Friend::searchFriend($user->id, $friend->id));
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Friend removed']);
});

test('remove friend with invalid friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->deleteJson('feed/friends/100');
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['friend_id']);
});

test('remove friend with string as friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->deleteJson('feed/friends/string');
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['friend_id']);
});

test('remove friend with friend not found', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $response = $this->actingAs($user)->deleteJson('feed/friends/' . $friend->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Friendship does not exist']);
});

test('remove friend who is blocked', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->deleteJson('feed/friends/' . $friend->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Friendship does not exist']);
});

test('remove friend who is pending', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->deleteJson('feed/friends/' . $friend->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Friendship does not exist']);
});

test('remove friend who is declined', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->deleteJson('feed/friends/' . $friend->id);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Friendship does not exist']);
});

// ------------------------------ Update friendship -----------------------------------
test('update friendship', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friendship = Friend::factory()->create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Cannot update user has not accepted friend request']);
});

test('update friendship with invalid friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->putJson('feed/friends/100', ['status' => 'accepted']);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['friend_id']);
});

test('update friendship with string as friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->putJson('feed/friends/string', ['status' => 'accepted']);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['friend_id']);
});

test('update friendship with friend not found', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Friendship does not exist']);
});

test('update friendship with friend is blocked', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(200);
    $response->assertJson(['requester_id' => $friend->id, 'accepter_id' => $user->id, 'status' => 'accepted']);
});

test('update friendship with friend is declined', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(200);
    $response->assertJson(['requester_id' => $friend->id, 'accepter_id' => $user->id, 'status' => 'accepted']);
});

test('update friendship with friend is already a friend', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(200);
    $response->assertJson(['requester_id' => $user->id, 'accepter_id' => $friend->id, 'status' => 'accepted']);
});

test('update friendship with friend request already sent', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Cannot update user has not accepted friend request']);
});

test('update friendship with friend request already received', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(200);
    $response->assertJson(['requester_id' => $friend->id, 'accepter_id' => $user->id, 'status' => 'accepted']);
});

test('update friendship with friend request already received and declined', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(200);
    $response->assertJson(['requester_id' => $friend->id, 'accepter_id' => $user->id, 'status' => 'accepted']);
});

test('update friendship with friend request already received and blocked', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $friend->id,
        'accepter_id' => $user->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(200);
    $response->assertJson(['requester_id' => $friend->id, 'accepter_id' => $user->id, 'status' => 'accepted']);
});

test('update friendship with friend request already received and blocked by you', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    Friend::factory()->create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'accepted']);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Cannot update user is blocked by requested user']);
});

test('update friendship with self as friend id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->putJson('feed/friends/' . $user->id, ['status' => 'accepted']);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'You cannot update friendship with self']);
});

test('update friendship with invalid status', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $response = $this->actingAs($user)->putJson('feed/friends/' . $friend->id, ['status' => 'invalid']);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['status']);
});

test('update friendship with friend id as string', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->putJson('feed/friends/string', ['status' => 'accepted']);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['friend_id']);
});

// ------------------------------ Get friend requests ---------------------------------
test('get friend requests', function () {
    $user = User::factory()->create();
    $friendRequests = Friend::factory(3)->create([
        'accepter_id' => $user->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/requests');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

test('get friend requests with no friend requests', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('feed/friends/requests');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get friend requests with friend requests declined', function () {
    $user = User::factory()->create();
    $friendRequests = Friend::factory(3)->create([
        'accepter_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/requests');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get friend requests with friend requests blocked', function () {
    $user = User::factory()->create();
    $friendRequests = Friend::factory(3)->create([
        'accepter_id' => $user->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/requests');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get friend requests with friend requests accepted', function () {
    $user = User::factory()->create();
    $friendRequests = Friend::factory(3)->create([
        'accepter_id' => $user->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/requests');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get friend requests with friend requests mixed', function () {
    $user = User::factory()->create();
    $friendRequests = Friend::factory(3)->create([
        'accepter_id' => $user->id,
        'status' => 'pending'
    ]);
    Friend::factory(2)->create([
        'accepter_id' => $user->id,
        'status' => 'declined'
    ]);
    Friend::factory(2)->create([
        'accepter_id' => $user->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/requests');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});


// ------------------------------ Get blocked friends ---------------------------------
test('get blocked friends', function () {
    $user = User::factory()->create();
    $blockedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/blocked');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

test('get blocked friends with no blocked friends', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('feed/friends/blocked');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get blocked friends with blocked friends declined', function () {
    $user = User::factory()->create();
    $blockedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/blocked');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get blocked friends with blocked friends pending', function () {
    $user = User::factory()->create();
    $blockedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/blocked');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get blocked friends with blocked friends accepted', function () {
    $user = User::factory()->create();
    $blockedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/blocked');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get blocked friends with blocked friends mixed', function () {
    $user = User::factory()->create();
    $blockedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'blocked'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'declined'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/blocked');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

// ------------------------------ Get declined friends --------------------------------
test('get declined friends', function () {
    $user = User::factory()->create();
    $declinedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/declined');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

test('get declined friends with no declined friends', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('feed/friends/declined');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get declined friends with declined friends blocked', function () {
    $user = User::factory()->create();
    $declinedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/declined');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get declined friends with declined friends pending', function () {
    $user = User::factory()->create();
    $declinedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/declined');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get declined friends with declined friends accepted', function () {
    $user = User::factory()->create();
    $declinedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/declined');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get declined friends with declined friends mixed', function () {
    $user = User::factory()->create();
    $declinedFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'declined'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'blocked'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/declined');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

// ------------------------------ Get pending friends ----------------------------------
test('get pending friends', function () {
    $user = User::factory()->create();
    $pendingFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'pending'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/pending');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

test('get pending friends with no pending friends', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('feed/friends/pending');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get pending friends with pending friends blocked', function () {
    $user = User::factory()->create();
    $pendingFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'blocked'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/pending');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get pending friends with pending friends declined', function () {
    $user = User::factory()->create();
    $pendingFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/pending');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get pending friends with pending friends accepted', function () {
    $user = User::factory()->create();
    $pendingFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'accepted'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/pending');
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get pending friends with pending friends mixed', function () {
    $user = User::factory()->create();
    $pendingFriends = Friend::factory(3)->create([
        'requester_id' => $user->id,
        'status' => 'pending'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'blocked'
    ]);
    Friend::factory(2)->create([
        'requester_id' => $user->id,
        'status' => 'declined'
    ]);
    $response = $this->actingAs($user)->getJson('feed/friends/pending');
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});
