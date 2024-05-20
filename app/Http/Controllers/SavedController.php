<?php

namespace App\Http\Controllers;

use App\Models\Saved;
use Illuminate\Http\Request;

class SavedController extends Controller
{
    public function getSaved(Request $request) {
        try {
            $user = $request->user('api');
            if($user) {
                $saved = Saved::where('userId', '=', $user->id)->get();
                return response()->json($saved);
            }
            else {
                return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
            }
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        } 
    }

    public function save(Request $request, $bookId) {
        try {
            $user = $request->user();
                if($user) {
                    $saved = new Saved();
                    $saved->userId = $user->id;
                    $saved->bookId = $bookId;
                    $saved->bookTitle = $request->input('bookTitle');
                    $saved->bookCover = $request->input('bookCover');
                    $saved->save();
                    return response()->json($saved,201);
                }
                else {
                    return response()->json(['message'=> 'Usuario no autenticado', 'statusCode' => 401],401);
                }
            }
        catch(\Exception $e) {
            return response()->json(['error' => 'Error en el formato de la request', 'statusCode' => 400], 400);
        }
    }

    public function delete(Request $request) {
        try {
            $user = $request->user('api');
            if($user) {
                Saved::where('userId', '=', $user->id)->delete();
                return response()->json(['message' => 'Libro borrado con exito', 'statusCode' => 200]);
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
