<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{




    public function index()
    {
        // Get the IDs of users that the authenticated user is following
        $followingIds = auth()->user()->following()->pluck('profiles.user_id')->toArray();
    
        // Include the authenticated user's own ID in the list of following IDs
        $followingIds[] = auth()->id();
    
        // Retrieve posts based on privacy settings of the users being followed
        $posts = Post::whereIn('user_id', $followingIds)
            ->where(function ($query) use ($followingIds) {
                $query->whereHas('user.profile', function ($subquery) {
                    $subquery->where('privacy', 'public');
                })
                ->orWhereIn('user_id', $followingIds); // Include own posts
            })
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json(['posts' => $posts]);
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);

        // Check if the post owner is public or is being followed by the authenticated user
        if ($post->user->isPublic() || auth()->user()->isFollowing($post->user)) {
            return response()->json(['post' => $post]);
        } else {
            return response()->json(['error' => 'Unauthorized to view this post.'], 403);
        }
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
