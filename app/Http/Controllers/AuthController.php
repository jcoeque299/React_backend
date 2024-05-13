<?php

namespace App\Http\Controllers;

use App\Models\Friends;
use DateTime;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email'=> 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Formato de petición incorrecto', 'statusCode' => 400],400);
        }
        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password' => Hash::make($request->password),
        ]);
        $date = new DateTime("NOW");
        $date->modify("+1 day");
        $token = $user->createToken('authToken',["*"],$date)->plainTextToken;
        return response()->json(['message' => 'Usuario registrado', 'token' => $token, 'statusCode' => 200],200);
    }

    public function login (Request $request) {
        $credentials = $request->only('email','password');

        if(Auth::attempt($credentials)) {
            $user = Auth::user();
            $date = new DateTime("NOW");
            $date->modify("+1 day");
            $token = $user->createToken('authToken',["*"],$date)->plainTextToken;
            return response()->json(['message'=> 'Login OK', 'token' => $token, 'statusCode' => 200],200);
        }
        else{
            return response()->json(['message'=> 'Login error', 'statusCode' => 401],401);
        }
    }

    public function user(Request $request) {
        $user = $request->user();

        if($user) {
            return response()->json([
                'id' =>$user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
        else {
            return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
        }
    }

    public function seeProfile(Request $request, $userName) {
        $user = $request->user();
        if($user) {
            $profile = User::where('name', '=', $userName)->first();
            $isProfileFriended = Friends::where('accepted', '=', true)->where('parentId', '=', $profile->id)->orWhere('childId', '=', $profile->id);
            if ($isProfileFriended->count()) {
                return response()->json(['id' => $profile->id,'name' => $profile->name]);
            }
            else {
                return response()->json(['message' => 'Este usuario no esta en tu lista de amigos', 'statusCode' => 403],403);
            }
        }
        else {
            return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
        }
    }

    public function logout(Request $request) {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json(['message'=> 'Se ha cerrado sesion', 'statusCode' => 200], 200);
    }
}
