<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $followingIds = auth()->user()->following()->pluck('id')->toArray();

        $posts = Post::whereIn('user_id', $followingIds)
                     ->orWhere('user_id', auth()->id()) // Include own posts
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
            'media_type' => 'nullable|in:image,video',
            'media_path' => 'nullable|string',
        ]);

        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => $validatedData['content'],
            'media_type' => $validatedData['media_type'],
            'media_path' => $validatedData['media_path'],
        ]);

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized to update this post.'], 403);
        }

        $post->update($request->all());

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
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
