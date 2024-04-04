<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use App\Models\Follower;


class ProfileController extends Controller
{

        private function uploadProfilePicture($file)
        {
            $imageName = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('profile_images'), $imageName);
            return $imageName;
        }

        public function updatePrivacy(Request $request, $id)
        {
            $profile = Profile::findOrFail($id);

            // Ensure only the authenticated user can update their own profile
            if ($profile->user_id != auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $validatedData = $request->validate([
                'privacy' => 'required|in:public,private',
            ]);

            $profile->update($validatedData);

            return response()->json(['message' => 'Privacy settings updated successfully', 'profile' => $profile]);
        }
        public function followUser($id)
        {
            $userToFollow = Profile::findOrFail($id);
            
             $alreadyFollowed = Follower::where('follower_id', $userToFollow->id)->where('user_id', auth()->id())->exists();
            if ($alreadyFollowed) {
                return response()->json(['message' => 'User is already being followed']);
            }

            auth()->user()->following()->attach($userToFollow);

            return response()->json(['message' => 'User followed successfully']);
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
        
    public function index()
    {
        return Profile::all();
    }

    public function show($id)
    {
        $profile = Profile::findOrFail($id);

        // Check if the profile is public or the authenticated user follows this profile
        if ($profile->privacy == 'public' || auth()->user()->following()->where('id', $id)->exists()) {
            return $profile;
        } else {
            return response()->json(['message' => 'Profile not accessible'], 403);
        }
    }

    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:profiles,email',
    //         'profile_picture' => 'image|mimes:jpeg,png,jpg|max:2048', 
    //     ]);
    //     $profile = new Profile([
    //         'name' => $validatedData['name'],
    //         'email' => $validatedData['email'],
    //     ]);
    //     if ($request->hasFile('profile_picture')) {
    //         $profile->profile_picture = $this->uploadProfilePicture($request->file('profile_picture'));
    //     }
    //     $profile->save();
    //     return response()->json(['message' => 'Profile created successfully', 'profile' => $profile], 201);
    
    // }
    public function update(Request $request, $id)
    {
       
        $profile = Profile::findOrFail($id);
        if ($profile->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:profiles,email,' . $profile->id,
            'profile_picture' => 'image|mimes:jpeg,png,jpg|max:2048', 
        ]);
    
        $profile->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);
        if ($request->hasFile('profile_picture')) {
            $profile->profile_picture = $this->uploadProfilePicture($request->file('profile_picture'));
        }
        $profile->save();
        return response()->json(['message' => 'Profile updated successfully', 'profile' => $profile]);
    
    }

    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        $user = User::where('id',$profile->user_id);
        // Ensure only the authenticated user can delete their own profile  
        if ($profile->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $profile->delete();
        $user->delete();
        return response()->json(['message' => 'Profile deleted successfully']);
    }
}
