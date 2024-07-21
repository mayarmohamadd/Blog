<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Get all categories
    public function index() {
        return response()->json(Category::all());
    }

    //Create categories
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    //Get one category
    public function show(Category $category) {
        return response()->json($category);
    }

    //update specific one
    public function update(Request $request, Category $category) {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);
        $category->update($validated);
        return response()->json($category);
    }

    //Delete
    public function destroy(Category $category) {
        $category->delete();
        return response()->json(null, 204);
    }
}

