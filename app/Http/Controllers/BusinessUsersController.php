<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\businesses;
use App\Models\role_users;
use App\Models\business_users;

class BusinessUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business_users = business_users::with([
            'user:id,full_name,email',
            'business:id,business_name',
            'role:id,role'
        ])->get();

        //Group by user + business combination
        $result = $business_users->groupBy(function ($bu) {
            return $bu->user->id . '-' . $bu->business->id;
        })->map(function ($group) {
            $first = $group->first();
            return [
                'users' => $first->user->full_name,
                'email' => $first->user->email,
                'business' => $first->business->business_name,
                'roles' => $group->pluck('role.role'),//array of roles
            ];
        });

        // You can return or use $result as needed
        return response()->json($result, 200);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
     try {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bus_name_id' => 'required|exists:businesses,id',
            // 'role_id' => 'required|exists:role_users,id',
        ]);

        // Fetch seller + buyer roles
        $roles = role_users::whereIn('role', ['seller', 'buyer'])->get()->unique('role');
        $createdRoles = [];

        foreach ($roles as $role) {
            // Check if record already exists
            $exists = business_users::where('user_id', $request->user_id)
                ->where('bus_name_id', $request->bus_name_id)
                ->where('role_id', $role->id)
                ->exists();

            if (!$exists) {
                $bu = business_users::create([
                    'user_id' => $request->user_id,
                    'bus_name_id' => $request->bus_name_id,
                    'role_id' => $role->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Load related models
                $bu->load([
                    'user:id,full_name,email',
                    'business:id,business_name',
                    'role:id,role'
                ]);

                $createdRoles[] = $bu->role->role;
            }
        }

        if (empty($createdRoles)) {
            return response()->json([
                'message' => 'All roles are already assigned to the user for this business.'
            ], 409);
        }

        $user = User::find($request->user_id);
        $business = businesses::find($request->bus_name_id);

        return response()->json([
            'message' => 'Business user roles assigned successfully.',
            'user' => $user->full_name,
            'email' => $user->email,
            'business' => $business->business_name,
            'assigned_roles' => $createdRoles,
        ], 201);
    }

    // Catch validation or DB-related errors
    catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'error' => 'Validation failed.',
            'details' => $e->errors(),
        ], 422);
    }
    catch (\Illuminate\Database\QueryException $e) {
        return response()->json([
            'error' => 'Database error.',
            'message' => $e->getMessage(),
        ], 500);
    }
    catch (\Exception $e) {
        return response()->json([
            'error' => 'Unexpected error.',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(), // optional, remove in production
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(business_users $business_users)
    {
        return response()->json($business_users, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(business_users $business_users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, business_users $business_users)
    {
         return response()->json([
            'message'=>'Updating business roles is not allowed once assigned.'
        ],403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(business_users $business_users)
    {
       return response()->json([
            'message'=>'Deleting business roles is not allowed once assigned.'
        ],403);
    }
}
