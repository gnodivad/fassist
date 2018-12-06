<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Shared\Resources\UserResource;

class UserController extends Controller
{
    public function user(Request $request)
    {
        return response()->json(['data' => ['user' => new UserResource($request->user())]]);
    }
}
