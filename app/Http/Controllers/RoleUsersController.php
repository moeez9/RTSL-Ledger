<?php

namespace App\Http\Controllers;

use App\Models\role_users;
use Illuminate\Http\Request;


class RoleUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role_users = role_users::with('user')->get();
        return response()->json($role_users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:seller,buyer,admin'
        ]);

        // Check duplicate role for same user
        $exists = role_users::where('user_id', $request->user_id)
            ->where('role', $request->role)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'User already has this role.'
            ], 409);
        }

        $role_user = role_users::create([
            'user_id' => $request->user_id,
            'role' => $request->role,
        ]);

        return response()->json($role_user->load('user'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(role_users $role_users)
    {
        $role_users->load('user');
        return response()->json($role_users, 200);
    }

    /**
     * Update is disabled.
     */
    public function update(Request $request, role_users $role_users)
    {
        return response()->json([
            'message' => 'Role cannot be updated once assigned.'
        ], 403);
    }

    /**
     * Delete is disabled.
     */
    public function destroy(role_users $role_users)
    {
        return response()->json([
            'message' => 'Role cannot be deleted once assigned.'
        ], 403);
    }
}
