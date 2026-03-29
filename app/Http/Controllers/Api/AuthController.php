<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Lcobucci\JWT\Signer\Key;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'googleLogin', 'facebookLogin', 'register', 'validateNickname', 'validateToken']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|string|max:50|unique:users',            
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'nickname' => $request->get('nickname'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function deleteAccount(Request $request)
    {
        $forceDelete = $request->input('force', false);
        

        $user = auth('api')->user();
        
        if(!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if($forceDelete) {
            // Permanently delete the user
            $user->forceDelete();
        } else {
            // Soft delete the user
            $user->delete();
        }

        return response()->json(['message' => 'Account deleted successfully']);
    }

    public function validateNickname(Request $request)
    {
        $nickname = $request->input('nickname');

        if(!$nickname) {
            return response()->json(['error' => 'Nickname is required'], 400);
        }

        $exists = User::where('nickname', $nickname)->exists();

        return response()->json(['is_unique' => !$exists]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        if(!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'nickname' => 'sometimes|string|max:50|unique:users,nickname,'.$user->id,
            'name' => 'sometimes|string|max:255',            
            'password' => 'sometimes|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if($request->has('nickname')) {
            $user->nickname = $request->input('nickname');
        }

        if($request->has('name')) {
            $user->name = $request->input('name');
        }

        if($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    public function validateToken(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $token = null;

        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        }

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        if(!$token) {
            return response()->json(['error' => 'Token is required'], 400);
        }

        try {

            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            return $this->respondWithToken($token);
            // return response()->json([
            //     'valid' => true,
            //     'user'  => $user
            // ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token absent'], 401);
        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'error' => 'Invalid token'], 401);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }

    public function updateFullBio(Request $request)
    {
        $user = auth('api')->user();

        if(!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'headline' => 'sometimes|string|max:255',
            'nickname' => 'required|string|max:50|unique:users,nickname,'.$user->id,
            'bio' => 'sometimes|string|max:1000',
            'website' => 'sometimes|url|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user->name = $request->input('name');
        $user->nickname = $request->input('nickname');

        if($request->has('headline')){
            $user->headline = $request->input('headline');
        }
        if ($request->has('bio')) {
            $user->bio = $request->input('bio');
        }
        if ($request->has('website')) {
            $user->website = $request->input('website');
        }
        $user->save();

        return response()->json(['message' => 'Full bio updated successfully', 'user' => $user]);
    }

    public function googleLogin(Request $request){

        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));

        // Obtenemos la info del usuario desde Google
        $oauth2 = new \Google\Service\Oauth2($client);
        $client->setAccessToken($request->token);
        $googleUser = $oauth2->userinfo->get();

        // Buscamos o creamos el usuario en tu BD
        $isUserDeleted = DB::table("users")
                        ->where('email', $googleUser->email)
                        ->whereNotNull('deleted_at')
                        ->exists();

        if($isUserDeleted) {
            $user = User::withTrashed()->where('email', $googleUser->email)->first();
            $user->restore();

            User::where('email', $googleUser->email)->update([
                'google_id' => $googleUser->id,
                'name' => $googleUser->name,
                'nickname' => Str::slug($googleUser->name) . '-' . Str::random(5),
            ]);

        }else{
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'nickname'          => Str::slug($googleUser->name) . '-' . Str::random(5),
                    'name'              => $googleUser->name,
                    'google_id'         => $googleUser->id,
                    'password'          => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                ]
            );
        }


        // Retornamos un token de tu propia app (con Sanctum)
        // $token = $user->createToken('auth_token')->plainTextToken;

        // return response()->json(['token' => $token, 'user' => $user]);

        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    public function facebookLogin(Request $request){

        $graphResponse = Http::get('https://graph.facebook.com/me', [
            'fields'       => 'id,name,email',
            'access_token' => $request->token,
        ]);

        if ($graphResponse->failed()) {
            return response()->json([
                'error' => 'Token de Facebook inválido o expirado.'
            ], 401);
        }

        $fbUser = $graphResponse->json();

        $email = $fbUser['email'] ?? null;

        if (!$email) {
            return response()->json([
                'error' => 'Tu cuenta de Facebook no tiene email asociado.'
            ], 422);
        }

        $isUserDeleted = DB::table("users")
                        ->where('email', $email)
                        ->whereNotNull('deleted_at')
                        ->exists();

        if($isUserDeleted) {
            $user = User::withTrashed()->where('email', $email)->first();
            $user->restore();

            User::where('email', $email)->update([
                'facebook_id' => $fbUser['id'],
                'name'        => $fbUser['name'],
                'nickname'    => Str::slug($fbUser['name']) . '-' . Str::random(5),
            ]);

        }else{
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $fbUser['name'],
                    'facebook_id'       => $fbUser['id'],
                    'password'          => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'nickname'          => Str::slug($fbUser['name']) . '-' . Str::random(5),
                ]
            );
        }       

        // Retornamos el token de tu app (igual que Google)
        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    
    }
}