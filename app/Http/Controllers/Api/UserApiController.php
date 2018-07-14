<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Modules\Shared\Models\User;
use Laravel\Passport\Client;
use function GuzzleHttp\json_decode;

class UserApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'                  => 'required|max:255|unique:users',
                'email'                 => 'required|email|max:255|unique:users',
                'password'              => 'required|min:6|max:20|confirmed',
                'password_confirmation' => 'required|same:password'
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $user = User::create([
            'name'             => $request->input('name'),
            'email'            => $request->input('email'),
            'password'         => bcrypt($request->input('password')),
            'token'            => str_random(64),
        ]);

        $client = Client::where('password_client', 1)->first();

        $response = app()->handle(Request::create(
            'oauth/token',
            'POST',
            [
                'grant_type'    => 'password',
                'client_id'     => $client->id,
                'client_secret' => $client->secret,
                'username'      => $request['email'],
                'password'      => $request['password'],
                'scope'         => null,
            ]
        ))->getContent();

        $data = json_decode($response, true);

        $data['user'] = $user;

        return $data;
    }
}
