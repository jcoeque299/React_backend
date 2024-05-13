<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostsController extends Controller
{
    public function getAllPosts(){
        $posts = DB::table('posts')->join('users', 'users.id', 'posts.userId')->select('posts.id','users.name', 'title')->get();
        return response()->json($posts);
    }

    public function getPost($postId) {
        $post = DB::table('posts')->join('users', 'users.id', 'posts.userId')->select('posts.id','users.name', 'title', 'text')->where('posts.id', '=', $postId)->get();
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