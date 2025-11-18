<?php

namespace App\Http\Controllers;

use App\Models\businesses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BusinessesController extends Controller
{
    use HasFactory;
    /**
     * Display a listing of all businesses with category name.
     */
    public function index()
    {
        $businesses = businesses::with('categories')->get();
        return response()->json($businesses, 200);
    }

    /**
     * Store a new business.
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:30',
            'cate_id' => 'required|exists:categories,id',
        ]);

        $businesses = businesses::create($request->only('business_name', 'cate_id'));

        return response()->json($businesses, 201);
    }

    /**
     * Display a specific business by ID.
     */
    public function show(businesses $businesses)
    {
        $businesses->load('categories');
        return response()->json($businesses, 200);
    }

    /**
     * Update an existing business.
     */
    public function update(Request $request, businesses $businesses)
    {
        $request->validate([
            'business_name' => 'required|string|max:30|' . $businesses->id,
            'cate_id' => 'required|exists:categories,id',
        ]);

        $businesses->update($request->only('business_name', 'cate_id'));
        return response()->json($businesses, 200);
    }

    /**
     * Prevent deletion of businesses.
     */
    public function destroy(businesses $businesses)
    {
        return response()->json([
            'message' => 'Businesses cannot be deleted once created.'
        ], 403);
    }
}
