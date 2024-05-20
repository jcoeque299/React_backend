<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ratings;

class RatingsController extends Controller
{
    public function getRatings($bookId) {
        $ratings = Ratings::where('bookId', '=', $bookId)->avg('score');
        return response()->json(['score' => $ratings]);
    }

    public function sendRating(Request $request, $bookId) { //Catch del error que se causa cuando un usuario intenta poner mas de un rating en un libro
        try {
            $user = $request->user('api');
            if($user) {
                $request->validate([
                    'score' => 'required|integer',
                ]);
                $alreadyRated = Ratings::where('bookId', '=', $bookId)->where('userId', '=', $user->id);
                if (!$alreadyRated->count()) {
                    $rating = new Ratings();
                    $rating->score = $request->input('score');
                    $rating->bookId = $bookId;
                    $rating->userId = $user->id;
                    $rating->save();
                    return response()->json($rating,201);
                }
                $alreadyRated->update(['score' => $request->input('score')]);
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
