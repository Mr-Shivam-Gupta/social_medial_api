<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Follower;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{

    public function index()
    {
        
    $user_id = auth()->id();
    $posts = [];
    // Retrieve posts from users with private profiles followed by the current user
    $private_profiles = Profile::where('privacy', 'private')->get();
    foreach ($private_profiles as $private_profile) {
        $follower = Follower::where('user_id', $user_id)->first();
        if ($follower) {
            $private_posts = Post::where('user_id', $follower->follower_id)
                ->orderByDesc('id')
                ->select('content', 'media')
                ->get()
                ->toArray();
            $posts = array_merge($posts, $private_posts);
        }
    }
    // Retrieve posts from users with public profiles
    $public_profiles = Profile::where('privacy', 'public')->get();
    foreach ($public_profiles as $public_profile) {
        $public_posts = Post::where('user_id', $public_profile->user_id)
            ->orderByDesc('id')
            ->select('id','content', 'media')
            ->get()
            ->toArray();
        $posts = array_merge($posts, $public_posts);
    }
    return response()->json(['posts' => $posts]);
    }

    public function show($id)
    {
        // Retrieve the target user's profile
        $profile = Profile::where('user_id', $id)->first();
        // Check if the profile exists
        if (!$profile) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the profile is private or public
        if ($profile->privacy === 'private') {
            // Check if the requesting user follows the target user
            $user = Auth::user();
            $follower = Follower::where('user_id', $user->id)
                ->where('follower_id', $id)
                ->first();
            // If the requesting user doesn't follow the target user, return 403 Forbidden
            if (!$follower) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }else{
                $posts = Post::where('user_id', $id)->select('id','content', 'media')->get();
            }
        }else{
            $posts = Post::where('user_id', $id)->select('id','content', 'media')->get();
        }
        return response()->json(['posts' => $posts]);
    }


    public function store(Request $request)
    {


        $validatedData = $request->validate([
            'content' => 'required|string',
            'media.*' => 'nullable|file|mimes:jpeg,png,mp4|max:2048', // Adjust max file size as needed
        ]);

        // Handle file upload if present
        $user_id = auth()->id();
        $mediaPaths = [];

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $imageName = $this->uploadImage($file);
                    $mediaPaths[] =  $imageName;
                } else {
                    // Handle invalid file
                    return response()->json(['message' => 'Invalid file'], 400);
                }
            }
        }

        // Convert $mediaPaths array to JSON
        $mediaJson = json_encode($mediaPaths);

        // Create the post
        $post = Post::create([
            'user_id' => $user_id,
            'content' => $validatedData['content'],
            'media' => $mediaJson,
        ]);

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    private function uploadImage($file)
    {
        // Generate a unique name for the image
        $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        // Move the image to the specified directory
        $file->move(public_path('media_images'), $imageName);
        return $imageName;
    }



    // public function store(Request $request)
    // {
    //     dd($request);
    //     $validatedData = $request->validate([
    //         'content' => 'required|string',
    //         'media' => 'nullable|file|mimes:jpeg,png,mp4|max:2048', // Adjust max file size as needed
    //     ]);

    //     // Handle file upload if present
    //     if ($request->hasFile('media')) {
    //         $file = $request->file('media');
    //         if ($file->isValid()) {
    //             $imageName = $this->uploadImage($file);
    //         } else {
    //             // Handle invalid file
    //             return response()->json(['message' => 'Invalid file'], 400);
    //         }
    //     } else {
    //         // Handle missing file
    //         return response()->json(['message' => 'No file uploaded'], 400);
    //     }

    //     // Continue with creating the post
    //     $user_id = auth()->id();
    //     $post = Post::create([
    //         'user_id' => $user_id,
    //         'content' => $validatedData['content'],
    //         'media' =>   $imageName,
    //     ]);

    //     return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    // }

    // private function uploadImage($file)
    // {
    //     // Generate a unique name for the image
    //     $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    //     // Move the image to the specified directory
    //     $file->move(public_path('media_images'), $imageName);
    //     return $imageName;
    // }



    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        if ($post->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized to update this post.'], 403);
        }
        $validatedData = $request->validate([
            'content' => 'required|string',
            'media.*' => 'nullable|file|mimes:jpeg,png,mp4|max:2048', // Adjust max file size as needed
        ]);
        $post->content = $validatedData['content'];

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $imageName = $this->uploadImage($file);
                    // Append the new image name to the existing media array
                    $postMedia = $post->media ?? [];
                    $postMedia[] = $imageName;
                    $post->media = $postMedia;
                } else {
                    // Handle invalid file
                    return response()->json(['message' => 'Invalid file'], 400);
                }
            }
        }
        // Save the updated post
        $post->save();
        return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized to delete this post.'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
