<?php

namespace App\Http\Controllers;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function getAllMessages(Request $request){
        try {
            $user = $request->user('api');
            if($user) {
                $messages = DB::table('messages')->join('users', 'users.id', 'messages.senderId')->select('messages.id','users.name', 'title')->where('receiverId', '=', $user->id)->get();
                return response()->json($messages);
            }
            else {
                return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
            }
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        }
    }

    public function getMessage(Request $request, $messageId){
        try {
            $user = $request->user('api');
            if($user) {
                $message = DB::table('messages')->join('users', 'users.id', 'messages.senderId')->select('messages.id','users.name', 'title', 'text')->where('messages.id', '=', $messageId)->where('receiverId', '=', $user->id)->first();
                return response()->json($message);
            }
            else {
                return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
            }
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        }
    }

    public function sendMessage(Request $request, $userName) {
        try {
            $user = $request->user();
                if($user) {
                    $request->validate([
                        'title' => 'required|string',
                        'text' => 'required|string',
                    ]);
                    $userId = User::where('name', '=', $userName)->first('id');
                    $message = new Messages();
                    $message->title = $request->input('title');
                    $message->text = $request->input('text');
                    $message->receiverId = $userId->id;
                    $message->senderId = $user->id;
                    $message->save();
                    return response()->json($message,201);
                }
                else {
                    return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
                }
            }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        }
    }

    public function removeMessage(Request $request, $messageId) {
        try {
            $user = $request->user();
                if($user) {
                    $message = Messages::where('id', '=', $messageId)->where('receiverId', '=', $user->id);
                    if(!$message->count()){
                        return response()->json(['message'=> 'El mensaje guardado no existe'],404);
                    }
                    $message->delete();
                    return response()->json(['message'=> 'El mensaje ha sido borrado', 'statusCode' => 200],200);
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

