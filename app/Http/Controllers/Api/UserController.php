<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

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

    $validatedData = $request->validate([
        'full_name' => 'sometimes|required|string|max:255',
        'date_of_birth' => 'sometimes|required|date',
        'password' => [
            'nullable',
            Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->max(255)
        ],
        'phone_no' => 'sometimes|required|string|max:20',
        'profile_pic' => 'nullable|string',
    ]);

    //  If password provided, hash it
    if (isset($validatedData['password'])) {
        $validatedData['password'] = Hash::make($validatedData['password']);
    }

    //  Update only the fields provided in the request
    $user->update($validatedData);

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user
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
