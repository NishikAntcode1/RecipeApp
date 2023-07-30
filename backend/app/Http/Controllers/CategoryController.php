<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Recipe;

class CategoryController extends Controller
{
    //
    public function createCategory(Request $request)
    {
        $categories = Category::where('parent_id', null)->orderby('name', 'asc')->get();
        if($request->method()=='GET')
        {
            return view('create-category', compact('categories'));
        }
        if($request->method()=='POST')
        {
            $validator = $request->validate([
                'name'      => 'required',
                'slug'      => 'required|unique:categories',
                'parent_id' => 'nullable|numeric'
            ]);

            Category::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'parent_id' =>$request->parent_id
            ]);

            return redirect()->back()->with('success', 'Category has been created successfully.');
        }
    }

    public function getAllCategories()
    {
        $categories = Category::all()->pluck('name','id')->toArray();
        return response()->json($categories);
    }

    public function getRelatedPosts($postId)
    {
        // Get the category ID of the current post
        $recipe = Recipe::find($postId);
        $categoryId = $recipe->category_id;

        // Get related posts under the same category
        $relatedPosts = Recipe::where('category_id', $categoryId)
                            ->where('id', '!=', $postId) // Exclude the current post itself
                            ->get();

        return response()->json($relatedPosts);
        // You can also pass the related posts to a view and display them as needed.
    }
}
