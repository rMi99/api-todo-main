<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();
        $existingUser = User::where('google_id', $user->getId())->first();

        if ($existingUser) {
            auth()->login($existingUser);

            // Generate a token for the authenticated user
            $token = $existingUser->createToken('API Access')->accessToken;

            // return response()->json([
            //     'user_id' => $existingUser->id,
            //     'username' => $existingUser->name,
            //     'email' => $existingUser->email,
            //     'access_token' => $token,
            // ]);
            
            return redirect('http://localhost:3000/login?token='.base64_encode($existingUser->google_id));
        
        } else {
            $newUser = new User();
            $newUser->name = $user->getName();
            $newUser->email = $user->getEmail();
            $newUser->google_id = $user->getId();
            $newUser->password = Hash::make('your_default_password');
            $newUser->save();

            auth()->login($newUser);

            
            $token = $newUser->createToken('API Access')->accessToken;

            return redirect('http://localhost:3000/login?token='.base64_encode($newUser->google_id));

        }
    }

    public function confirmGoogleCallback(Request $request){
        $validator = Validator::make($request->all(), [
            'token' => 'required'
       ]);

       if ($validator->fails()) {
           return response()->json([
               'status' => 401,
               'message' => "Token Required"
           ],400);
       }

       $user = User::where ("google_id",base64_decode($request->token))->select('id','name','email')->first();

       if(!$user){
           return response()->json([
               'status' => 401,
               'message' => "Unauthorized"
           ],401);
        }

        $token= $user->createToken("API TOKEN")->plainTextToken;

        Arr::add($user,'token',$token);
           return response()->json($user);
    }
}
