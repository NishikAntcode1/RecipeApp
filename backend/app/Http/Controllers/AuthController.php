<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use JWTAuth;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request ->all(), [
            'firstname' => 'required|string|min:2|max:50',
            'lastname' => 'required|string|min:2|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6'               // confirmed   -> password_confirmation
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors(),400);
        }

        $user = User::create([
            'firstname' => $request -> firstname,
            'lastname' => $request -> lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Successfully registered !',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request ->all(), [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:6'            

        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors(),400);
        }
        
        if(!$token = auth()->attempt($validator->validated())){
            return response() -> json(['error' => 'Unauthorized']);
        }
        return $this -> respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 600
        ]);
    }

    public function profile(){
        return response()->json(auth()->user());
    }

    

    public function updateProfile(Request $request)
    {
        try {
            // Get the authenticated user using JWT token
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'accountname' => 'string|min:2|max:100',
                'firstname' => 'required|string|min:2|max:50',
                'lastname' => 'required|string|min:2|max:50',
                'email' => 'required|string|email|max:100',
                'bio' => 'string|min:10',
                // 'profileImage' => 'image',
                // 'coverImage' => 'image',
                'type' => 'integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user->fill($request->all());

            if ($request->hasFile('profileImage')) {
                $profileImageName = $request->file('profileImage');
                $fileName = time().'_'.$profileImageName->getClientOriginalName();
                $profileImageName->storeAs('public/images', $fileName);
                $user->profileimage = $fileName;
            }

            if ($request->hasFile('coverImage')) {
                $coverImageName = $request->file('coverImage');
                $fileName = time().'_'.$coverImageName->getClientOriginalName();
                $coverImageName->storeAs('public/images', $fileName);
                $user->coverimage = $fileName;
            }

            $user->save();

            return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
        } 
        catch (JWTException $e) {
            return response()->json(['message' => 'Failed to authenticate token'], 500);
        }
    }

    public function getAllUsers(){
        $users = User::all();
        return $users;
    }

}
