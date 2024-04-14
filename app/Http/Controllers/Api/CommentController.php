<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    private function commentCount($postId)
    {
        return Comment::where('post_id', $postId)->count();
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id',
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $validatedData['post_id'],
            'parent_id' => $validatedData['parent_id'],
            'content' => $validatedData['content'],
        ]);

        $commentCount = $this->commentCount($validatedData['post_id']);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment, 'comment_count' => $commentCount], 201);
    }

    public function reply(Request $request, $id)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);

        $parentComment = Comment::findOrFail($id);

        $reply = Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $parentComment->post_id,
            'parent_id' => $parentComment->id,
            'content' => $validatedData['content'],
        ]);

        $commentCount = $this->commentCount($parentComment->post_id);

        return response()->json(['message' => 'Reply added successfully', 'reply' => $reply, 'comment_count' => $commentCount], 201);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $postId = $comment->post_id;
        $comment->delete();

        $commentCount = $this->commentCount($postId);

        return response()->json(['message' => 'Comment deleted successfully', 'comment_count' => $commentCount]);
    }
}
