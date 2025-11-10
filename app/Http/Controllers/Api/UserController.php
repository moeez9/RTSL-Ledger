<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // CREATE
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'f_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'email' => 'required|email|unique:users',
            'password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->max(255)
            ],
            'gender' => 'required|in:male,female,other',
            'phone_no' => 'required|string|max:20',
            'profile_pic' => 'nullable|string',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'f_name' => $request->f_name,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'phone_no' => $request->phone_no,
            'profile_pic' => $request->profile_pic,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    // READ ALL
    public function index()
    {
        return response()->json([
            'data' => User::all()
        ], 200);
    }

    // READ SINGLE
    public function show($id)
    {
    //     return response()->json([
    // 'message'=>'Success message here',
    // ]);
        $user = User::findOrFail($id);
        return response()->json([
            'data' => $user
        ], 200);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'sometimes|required|string|max:255',
            'f_name' => 'sometimes|required|string|max:255',
            'date_of_birth' => 'sometimes|required|date',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => ['nullable', Password::min(8)  // Changed to nullable
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->max(255)
            ],
            'gender' => 'sometimes|required|in:male,female,other',
            'phone_no' => 'sometimes|required|string|max:20',
            'profile_pic' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    // DELETE
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}
