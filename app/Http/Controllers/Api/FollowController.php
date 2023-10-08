<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request)
    {
        try {
            $user = \auth()->user();
            $following = User::where('username', $request->username)->first();

            if (!$following) {
                return response()->json(
                    data: [
                        'message' => 'There is a failure when try to follow ' . $request->username . '!',
                        'success' => false,
                        'data' => null,
                        'error' => [
                            'code' => 404,
                            'message' => $request->username . ' is not a valid username!',
                        ],
                    ],
                    status: 404,
                );
            }

            $success = $user->follow($following);

            if (!$success) {
                return response()->json(
                    data: [
                        'message' => 'There is a failure when try to follow ' . $request->username . '!',
                        'success' => false,
                        'data' => null,
                        'error' => [
                            'code' => 400,
                            'message' => 'Failed to follow ' . $request->username . '!',
                        ],
                    ],
                    status: 400,
                );
            }

            return response()->json([
                'message' => 'You are now following ' . $request->username,
                'success' => false,
                'data' => $user->refresh(),
                'error' => null,
            ], status: 200);
        } catch (\Throwable $th) {
            return response()->json(
                data: [
                    'message' => 'There is a failure when try to follow!',
                    'success' => false,
                    'data' => null,
                    'error' => [
                        'code' => $th->getCode(),
                        'message' => $th->getMessage(),
                    ],
                ],
                status: 400,
            );
        }
    }

    public function unfollow(Request $request)
    {
        try {
            $user = \auth()->user();
            $following = User::where('username', $request->username)->first();

            if (!$following) {
                return response()->json(
                    data: [
                        'message' => 'There is a failure when try to unfollow ' . $request->username . '!',
                        'success' => false,
                        'data' => null,
                        'error' => [
                            'code' => 404,
                            'message' => $request->username . ' is not a valid username!',
                        ],
                    ],
                    status: 404,
                );
            }

            $user->unfollow($following);



            return response()->json([
                'message' => 'You are now unfollowing ' . $request->username,
                'success' => false,
                'data' => $user->refresh(),
                'error' => null,
            ], status: 200);
        } catch (\Throwable $th) {
            return response()->json(
                data: [
                    'message' => 'There is a failure when try to follow!',
                    'success' => false,
                    'data' => null,
                    'error' => [
                        'code' => $th->getCode(),
                        'message' => $th->getMessage(),
                    ],
                ],
                status: 400,
            );
        }
    }
}
