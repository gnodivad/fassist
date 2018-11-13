<?php

namespace App\Modules\Shared\Traits;

use Illuminate\Http\Request;
use Laravel\Passport\Client;

trait RetrieveAccessToken
{
    protected function requestAccessToken(string $email, string $password)
    {
        $client = Client::where('password_client', 1)->firstOrFail();

        $response = app()->handle(Request::create(
            'oauth/token',
            'POST',
            [
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $email,
                'password' => $password,
                'scope' => null,
            ]
        ))->getContent();

        return json_decode($response, true);
    }
}
