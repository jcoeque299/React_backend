<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friends;
use Illuminate\Support\Facades\DB;

class FriendsController extends Controller
{
    public function getFriends(Request $request) {
        try {
            $user = $request->user('api');
            if($user) {
                $friendsParent = DB::table('friends')->join('users as parentUser', 'parentUser.id', 'friends.parentId')->select('parentUser.name')->where('parentId', '=', $user->id)->orWhere('childId', '=', $user->id)->first()->name;
                $friendsChild = DB::table('friends')->join('users as childUser', 'childUser.id', 'friends.childId')->select('childUser.name')->where('parentId', '=', $user->id)->orWhere('childId', '=', $user->id)->first()->name;
                $accepted = DB::table('friends')->select('accepted')->where('parentId', '=', $user->id)->orWhere('childId', '=', $user->id)->first()->accepted;
                return response()->json(['parentName' => $friendsParent, 'childName' => $friendsChild, 'accepted' => $accepted]);
            }
            else {
                return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
            }
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        }
    }

    public function sendFriendRequest(Request $request, $userId) {
        try {
            $user = $request->user('api');
            if($user) {
                $friendRequest = new Friends();
                $friendRequest->parentId = $user->id;
                $friendRequest->childId = $userId;
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

    public function acceptRequest(Request $request, $userId) {
        try {
            $user = $request->user('api');
            if($user) {
                DB::table('friends')->where('childId', '=', $user->id)->where('parentId', '=', $userId)->update(['accepted' => true]);
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

    public function removeFriend(Request $request, $userId) {
        try {
            $user = $request->user('api');
            if($user) {
                DB::table('friends')->where('childId', '=', $user->id)->where('parentId', '=', $userId)->delete();
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