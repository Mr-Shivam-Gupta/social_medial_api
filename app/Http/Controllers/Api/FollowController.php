<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use App\Models\Follower;
use App\Models\RequestFollower;

class FollowController extends Controller
{
    public function acceptRequest($id){
        $request = RequestFollower::where('sender_id', $id)->where('receiver_id', auth()->id())->first();
      // Check if the request exists
    if (!$request) {
        return response()->json(['message' => 'Request not found ']);
    }

    // Create the follower relationship
    auth()->user()->followers()->attach($request->sender_id);

    // Delete the request
    $request->delete();          

    }

    public function followUser($id)
    {
        $userToFollow = Profile::findOrFail($id);
        $alreadyFollowed = Follower::where('follower_id', $userToFollow->id)
                                    ->where('user_id', auth()->id())
                                    ->exists();
    
        if ($alreadyFollowed) {
            return response()->json(['message' => 'User is already being followed']);
        }
    
        if ($userToFollow->privacy === 'private') {
            // User has a private profile, create a request to follow
            RequestFollower::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $userToFollow->id,
            ]);
            return response()->json(['message' => 'Follow request sent successfully']);
        } else {
            // User has a public profile, directly establish the follower relationship
            auth()->user()->following()->attach($userToFollow);
    
            return response()->json(['message' => 'User followed successfully']);
        }
    }


    
    public function unfollowUser($id)
    {
        $userToUnfollow = Profile::findOrFail($id);

        auth()->user()->following()->detach($userToUnfollow);

        return response()->json(['message' => 'User unfollowed successfully']);
    }
    
    public function followers($id)
    {
        $profile = Profile::findOrFail($id);

        $followers = Follower::where('user_id', $profile->user_id)->get();
        $followersCount = $followers->count();
    
        return response()->json(['followers_count' => $followersCount, 'followers' => $followers]);
    
    }

    public function userFollowing()
    {
        $following = Follower::where('follower_id', auth()->user()->id)->get();
    
        if ($following->isEmpty()) {
            return response()->json(['message' => 'You are not following any users.']);
        }
    
        $followingCount = $following->count();
    
        return response()->json(['following_count' => $followingCount, 'following' => $following]);
    }
    
}
