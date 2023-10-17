<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        // header('Access-Control-Allow-Origin:*');

        $user = Auth::user();

        if ($user) {

            return response()->json([
                'status' => true,
                'user' => $user,
            ], 200);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function user($id){
        $user = User::where('id', $id)->first();
        return response()->json($user);
    }

    public function userChange($id, Request $request) {
        try {

            $user = User::find($id);
    
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
    
            $user->name = $request->input('name', $user->name);
            $user->email = $request->input('email', $user->email);
    
            $newPassword = $request->input('password');
            if ($newPassword) {
    
                $user->password = bcrypt($newPassword);
    
            }
                $user->save();
    
            return response()->json(['message' => 'User information updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
    
}
