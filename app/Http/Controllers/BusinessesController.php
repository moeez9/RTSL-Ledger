<?php

namespace App\Http\Controllers;

use App\Models\businesses;
use Illuminate\Http\Request;

class BusinessesController extends Controller
{
    // GET /api/businesses
    public function index()
    {
        return businesses::with('category')->get();
    }

    // POST /api/businesses
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:30|unique:businesses,business_name',
            'cate_id' => 'required|exists:categories,id',
        ]);

        $business = businesses::create($request->only('business_name', 'cate_id'));

        return response()->json($business, 201);
    }

    // GET /api/businesses/{id}
    public function show(businesses $business)
    {
        return $business->load('category');
    }

    // PUT/PATCH /api/businesses/{id}
    public function update(Request $request, businesses $business)
    {
        $request->validate([
            'business_name' => 'required|string|max:30|unique:businesses,business_name,' . $business->id,
            'cate_id' => 'required|exists:categories,id',
        ]);

        $business->update($request->only('business_name', 'cate_id'));

        return response()->json($business, 200);
    }

    // DELETE /api/businesses/{id}
    public function destroy(businesses $business)
    {
        $business->delete();

        return response()->json(['message' => 'Business deleted successfully'], 200);
    }

    // Optional: restore soft-deleted business
    public function restore($id)
    {
        $business = businesses::withTrashed()->find($id);

        if (!$business || !$business->trashed()) {
            return response()->json(['message' => 'Business not deleted or not found'], 404);
        }

        $business->restore();

        return response()->json(['message' => 'Business restored', 'data' => $business], 200);
    }
}
