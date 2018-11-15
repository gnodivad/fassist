<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use App\Modules\Shared\Models\User;
use App\Modules\Shared\Resources\UserResource;
use App\Modules\Shared\Traits\RetrieveAccessToken;
use App\Exceptions\ApplicationException;

class AuthController extends Controller
{
    use AuthenticatesUsers, RetrieveAccessToken;

    public function store(Request $request) : JsonResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|max:20',
            'name' => 'required|max:255',
            'fcm' => 'required'
        ]);
        
        $user = User::create([
            'email' => $validatedData('email'),
            'password' => bcrypt($validatedData('password')),
            'name' => $validatedData('name'),
            'fcm' => $validatedData('fcm')
        ]);

        $accessToken = $this->requestAccessToken($validatedData['email'], $validatedData['password']);

        $data = array_merge($accessToken, ['user' => new UserResource($user)]);

        return response()->json(['data' => $data], 201)->header('Content-Type', 'application/vnd.api+json');
    }

    public function login(Request $request) : JsonResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'fcm' => 'required'
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (is_null($user) || !$this->attemptLogin($request)) {
            throw new ApplicationException("Email or password is incorrect!");
        }
        
        $accessToken = $this->requestAccessToken($validatedData['email'], $validatedData['password']);
        
        $user->update(['fcm' => $request['fcm']]);

        $data = array_merge($accessToken, ['user' => new UserResource($user)]);

        return response()->json(['data' => $data], 200)->header('Content-Type', 'application/vnd.api+json');
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        $request->user()->update([
            'fcm' => null
        ]);

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
