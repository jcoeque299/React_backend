<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friends;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FriendsController extends Controller
{
    public function getFriends(Request $request) {
        try {
            $user = $request->user('api');
            if($user) {
                $friends = DB::table('friends')->join('users as parentUser', 'parentUser.id', 'friends.parentId')->join('users as childUser', 'childUser.id', 'friends.childId')->select('parentUser.name as parentName', 'childUser.name as childName', 'accepted')->where('parentId', '=', $user->id)->orWhere('childId', '=', $user->id)->get();
                if(!$friends) {
                    return response()->json([]);
                }
                return response()->json($friends);
            }
            else {
                return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
            }
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        }
    }

    public function sendFriendRequest(Request $request, $userName) {
        try {
            $user = $request->user('api');
            if($user) {
                $friendId = User::where('name', '=', $userName)->first('id');
                $friendRequest = new Friends();
                $friendRequest->parentId = $user->id;
                $friendRequest->childId = $friendId->id;
                $friendRequest->save();
                return response()->json($friendRequest,201);
            }
            else {
                return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
            }
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        }
    }

    public function acceptRequest(Request $request, $userName) {
        try {
            $user = $request->user('api');
            if($user) {
                $userId = User::where('name', '=', $userName)->first('id');
                DB::table('friends')->where('childId', '=', $user->id)->where('parentId', '=', $userId->id)->update(['accepted' => true]);
                return response()->json(['message' => 'Solicitud de amistad aceptada', 'statusCode' => 200],200);
            }
            else {
                return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
            }
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        }
    }

    public function removeFriend(Request $request, $userName) {
        try {
            $user = $request->user('api');
            if($user) {
                $userId = User::where('name', '=', $userName)->first('id');
                DB::table('friends')->where('childId', '=', $user->id)->where('parentId', '=', $userId->id)->delete();
                return response()->json(['message' => 'Amistad borrada con exito', 'statusCode' => 200],200);
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