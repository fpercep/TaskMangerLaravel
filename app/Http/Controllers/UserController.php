<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

    public function searchUsers(Request $request): JsonResponse
    {
        $users = User::select(['id', 'name', 'email'])
            ->search($request->query('search'))
            ->limit(5)
            ->get();

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }
}
