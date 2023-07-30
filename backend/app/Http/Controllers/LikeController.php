<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LikeController extends Controller
{
    public function liked($postId)
    {
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $existingLike = Like::where('recipe_id', $postId)->first();

            if($existingLike){
                $likeData = json_decode($existingLike->like_data, true);
                if(in_array($user->id, $likeData)){
                    $updatedLikeData = array_diff($likeData, [$user->id]);
                    $existingLike->update([
                        'like_data' => json_encode($updatedLikeData)
                    ]);
                    return response()->json(['message' => 'you unliked it.']);
                }
                else{
                    $likeData[] = $user->id;
                    $existingLike->update([
                        'like_data' => json_encode($likeData)
                    ]);
                }
            }
            else{
                $input = [
                    'recipe_id' => $postId,
                    'like_data' => json_encode([
                        $user->id
                    ])
                ];
    
                $item = Like::create($input);
            }
            return response()->json(['message' => 'Like added successfully']);
        // return response()->json(['isLiked' => true, 'likes' => $post->likes]);

        } catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }

    public function isLikedOrNot($postId){
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $existingLike = Like::where('recipe_id', $postId)->first();

            if($existingLike){
                $likeData = json_decode($existingLike->like_data, true);
                if(in_array($user->id, $likeData)){
                    return response()->json(['isLiked' => true]);
                }
                else{
                    return response()->json(['isLiked' => false]);
                }
            }
            else{
                return response()->json(['isLiked' => false]);
            }

        }catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }
}

