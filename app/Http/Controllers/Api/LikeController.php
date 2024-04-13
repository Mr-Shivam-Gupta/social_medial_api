<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;

class LikeController extends Controller
{
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

        return response()->json(['message' => 'Post liked successfully', 'like' => $like], 201);
    }

    public function destroy($id)
    {
        $like = Like::where('post_id', $id)->where('user_id', auth()->id())->first();
        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Like removed successfully']);
        } else {
            return response()->json(['message' => 'Like not found'], 404);
        }
    }
}
