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

        $like = Like::create([
            'user_id' => auth()->id(),
            'post_id' => $validatedData['post_id'],
        ]);

        return response()->json(['message' => 'Post liked successfully', 'like' => $like], 201);
    }

    public function destroy($id)
    {
        $like = Like::findOrFail($id);
        $like->delete();

        return response()->json(['message' => 'Like removed successfully']);
    }
}
