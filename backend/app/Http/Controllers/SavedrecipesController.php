<?php

namespace App\Http\Controllers;

use App\Models\Savedrecipe;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class SavedrecipesController extends Controller
{
    //
    public function saveRecipe($postId){
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $savedRecipes = Savedrecipe::where('user_id', $user->id)->first();

            if($savedRecipes){
                $savedData = json_decode($savedRecipes->saved_recipes_data, true);
                if(in_array($postId, $savedData)){
                   
                    return response()->json(['message' => 'Already saved']);
                }
                else{
                    $postData[] = intval($postId);
                    $savedRecipes->update([
                        'saved_recipes_data' => json_encode($postData)
                    ]);
                }
            }
            else{
                $input = [
                    'user_id' => $user->id,
                    'saved_recipes_data' => json_encode([
                        intval($postId)
                    ])
                ];
    
                $item = Savedrecipe::create($input);
            }
            return response()->json(['message' => 'Recipe added successfully']);
            
        } catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }

    public function isSavedOrNot($postId){
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $savedRecipes = Savedrecipe::where('user_id', $user->id)->first();

            if($savedRecipes){
                $savedData = json_decode($savedRecipes->saved_recipes_data, true);
                if(in_array($postId, $savedData)){
                    return response()->json(['isSaved' => true]);
                }
                else{
                    return response()->json(['isSaved' => false]);
                }
            }
            else{
                return response()->json(['isSaved' => false]);
            }

        }catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }
}
