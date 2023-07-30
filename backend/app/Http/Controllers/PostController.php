<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;




class PostController extends Controller
{
    public function latestPosts(){
        $recepies = Recipe::orderBy('created_at', 'desc')->get();
        return response() -> json([
            'data' => $recepies,
            'message' => 'sucessfully retrived all the recipes'
        ]);
    }

    public function createPost(Request $request) {
        try {
            // Get the authenticated user using JWT token
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $validator = Validator::make($request ->all(), [
                'dishname' => 'required|string|min:2|max:50',
                'description' => 'required|string|min:10|max:500',
                'ingredients' => 'required|string|min:10|max:1000',
                'instructions' => 'required|string|max:2000',
                'dishImage' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'categoryId'=> 'required'
            ]);
            if($validator->fails())
            {
                return response()->json($validator->errors(),400);
            }

            $dishImageName = $request->file('dishImage');
            $fileName = time().'_'.$dishImageName->getClientOriginalName();
            $dishImageName->storeAs('public/images', $fileName);

            $dish = Recipe::create([
                'dishname' => $request -> dishname,
                'description' => $request -> description,
                'ingredients' => $request -> ingredients,
                'instructions' => $request -> instructions,
                'dishimage' => $fileName,
                'user_id' => $user->id,
                'category_id' => $request->categoryId,
            ]);

            return response()->json([
                'message' => 'Successfully created !',
                'recipe' => $dish
            ], 201);
        }
        catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }

    public function userAllPosts(){
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }
        $recipes = Recipe::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return response()->json($recipes, 200);
        }
        catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }

    public function getPostById($id){
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $recipe = Recipe::find($id);

            if (!$recipe) {
                return response()->json(['message' => 'Recipe not found'], 404);
            }
            return $recipe;
        }
        catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }

    public function editPost(Request $request, $id){
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'dishname' => 'required|string|min:2|max:50',
                'description' => 'required|string|min:10|max:500',
                'ingredients' => 'required|string|min:10|max:500',
                'instructions' => 'required|string|max:2000',
                'dishImage' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'category_id'=> 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $recipe = Recipe::find($id);

            if (!$recipe) {
                return response()->json(['message' => 'Recipe not found'], 404);
            }

            $recipe->fill($request->all());

            if ($request->hasFile('dishImage')) {
                $dishImageName = $request->file('dishImage');
                $fileName = time().'_'.$dishImageName->getClientOriginalName();
                $dishImageName->storeAs('public/images', $fileName);
                $recipe->dishimage = $fileName;
            }
            $recipe->save();
            return response()->json(['message' => 'recipe updated successfully', 'recipe' => $recipe]);

        }
        catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }

    public function deletePost($id)
    {
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $recipe = Recipe::find($id);
            if (!$recipe) {
                return response()->json(['message' => 'Recipe not found'], 404);
            }
            $recipe->delete();
            return response()->json(['message' => 'Recipe deleted successfully'], 200);
        }
        catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }
}
