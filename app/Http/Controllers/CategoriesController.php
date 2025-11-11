<?php

namespace App\Http\Controllers;

use App\Models\categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    // GET /api/categories
    public function index()
    {
        return response()->json(categories::all(), 200);
    }

    // POST /api/categories
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:30|unique:categories,category',
        ]);

        $categories = categories::create([
            'category' => $request->category
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $categories
        ], 201);
    }

    // GET /api/categories/{id}
    public function show($id)
    {
        $categories = categories::find($id);

        if (!$categories) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($categories, 200);
    }

    // PUT/PATCH /api/categories/{id}
    public function update(Request $request, $id)
    {
        $categories = categories::find($id);

        if (!$categories) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'category' => 'required|string|max:30|unique:categories,category,' . $id,
        ]);

        $categories->update([
            'category' => $request->categories
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $categories
        ], 200);
    }

    // DELETE /api/categories/{id}
    public function destroy($id)
    {
        $categories = categories::find($id);

        if (!$categories) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $categories->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }

    // PUT /api/categories/{id}/restore
    public function restore($id)
{
    $categories = categories::onlyTrashed()->find($id);

    if (!$categories || !$categories->trashed()) {
        return response()->json(['message' => 'Category not found or not deleted'], 404);
    }

    $categories->restore();
    return response()->json(['message' => 'Category restored', 'data' => $categories], 200);
}
}
