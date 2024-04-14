<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    private function likeCount($postId)
    {
        return Like::where('post_id', $postId)->count();
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

                $alreadyLiked = Like::where('user_id', auth()->id())
                ->where('post_id', $validatedData['post_id'])
                ->first();

                if ($alreadyLiked) {
                return response()->json(['message' => 'Post already liked'], 400);
                }
                
        $like = Like::create([
            'user_id' => auth()->id(),
            'post_id' => $validatedData['post_id'],
        ]);

        $likeCount = $this->likeCount($validatedData['post_id']);

        return response()->json(['message' => 'Post liked successfully', 'like' => $like, 'like_count' => $likeCount], 201);
    }

    public function destroy($id)
    {
        $like = Like::where('post_id', $id)->where('user_id', auth()->id())->first();
        if ($like) {
            $like->delete();
            $likeCount = $this->likeCount($id);
            return response()->json(['message' => 'Like removed successfully', 'like_count' => $likeCount]);
        } else {
            return response()->json(['message' => 'Like not found'], 404);
        }
    }
    
}
