<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Modules\Shared\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $attributes = $request->validate([
            'name' => 'sometimes|required|max:255'
        ]);

        $user = auth()->user();

        $user->update($attributes);
        if ($request->exists('avatar_url')) {
            $user
                ->addMediaFromBase64($request->get('avatar_url'))
                ->toMediaCollection();
        }

        return response()->json(['data' => ['user' => new UserResource($request->user())]])->header('Content-Type', 'application/vnd.api+json');;
    }

    public function user(Request $request)
    {
        return response()->json(['data' => ['user' => new UserResource($request->user())]])->header('Content-Type', 'application/vnd.api+json');;
    }
}
