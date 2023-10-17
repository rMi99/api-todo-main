<?php

namespace App\Http\Controllers;

use App\Models\no;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


use function Laravel\Prompts\error;

class GoogleAuthController extends Controller
{
   

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    
    public function handleGoogleCallback()
    {
        try {
           $google_user = Socialite::driver('google')->user();
           $user = User::where('google_id',$google_user->getId())->first();
            if($user){

                $new_user =User::create([
                    'name' => $google_user->getName(),
                    'email'=> $google_user->getEmail(),
                    'google_id'=> $google_user->getId(),

                ]);

                Auth::login(($new_user));
                return redirect()->intended('home');

            }else{

                Auth::login(($user));
                return redirect()->intended('home');

                
            }


        } catch (\Throwable $th) {
            dd( $th);
        }

    }

    
}


// <?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use GuzzleHttp\Exception\ClientException;
// use Illuminate\Http\JsonResponse;
// use Laravel\Socialite\Contracts\User as SocialiteUser;
// use Laravel\Socialite\Facades\Socialite;



// class GoogleAuthController extends Controller
// {
   
//     public function redirectToAuth(): JsonResponse
//     {
//         return response()->json([
//             // 'url' => Socialite::driver('google')->redirect()->getTargetUrl(),
//             'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),

//         ]);
//     }

//     public function handleAuthCallback(): JsonResponse
//     {
//         try {
//             /** @var SocialiteUser $socialiteUser */
//             $socialiteUser = Socialite::driver('google')->user();
//         } catch (ClientException $e) {
//             return response()->json(['error' => 'Invalid credentials provided.'], 422);
//         }

//         /** @var User $user */
//         $user = User::query()
//             ->firstOrCreate(
//                 [
//                     'email' => $socialiteUser->getEmail(),
//                 ],
//                 [
//                     'email_verified_at' => now(),
//                     'name' => $socialiteUser->getName(),
//                     'google_id' => $socialiteUser->getId(),
//                     'avatar' => $socialiteUser->getAvatar(),
//                 ]
//             );

//         return response()->json([
//             'user' => $user,
//             'access_token' => $user->createToken('google-token')->plainTextToken,
//             'token_type' => 'Bearer',
//         ]);
//     }
// }

    
