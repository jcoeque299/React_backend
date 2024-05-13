<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Comments;

class CommentsController extends Controller
{
    public function getComments($postId){
        $comments = DB::table("comments")->join("users", "users.id", "comments.userId")->select("comments.text", "users.name")->where("postId", "=", $postId)->get();
        return response()->json($comments);
    }

    public function sendComment(Request $request, $postId){
        try {
            $user = $request->user();
                if($user) {
                    $request->validate([
                        'text' => 'required|string',
                    ]);
                    $comment = new Comments();
                    $comment->text = $request->input('text');
                    $comment->postId = $postId;
                    $comment->userId = $user->id;
                    $comment->save();
                    return response()->json($comment,201);
                }
                else {
                    return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
                }
            }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        }
    }
}