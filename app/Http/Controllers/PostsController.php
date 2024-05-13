<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function getAllPosts(){
        $posts = Posts::orderBy("created_at","desc")->get();
        return response()->json($posts);
    }

    public function getPost($postId) {
        $post = Posts::where('id', '=', $postId)->get();
        return response()->json($post);
    }

    public function sendPost(Request $request) {
        try {
            $user = $request->user('api');
            if($user) {
                $request->validate([
                    'title' => 'required|string',
                    'text' => 'required|string'
                ]);
                $post = new Posts();
                $post->title = $request->input('title');
                $post->text = $request->input('text');
                $post->userId = $user->id;
                $post->save();
                return response()->json($post,201);
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