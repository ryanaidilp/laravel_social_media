<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;

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

    public function followers(Request $request)
    {
        try {
            $user = \auth()->user();
            $followers = QueryBuilder::for(User::class)
                ->allowedFilters(['username'])
                ->whereRelation('followables', 'followable_id', '==', $user->id)
                ->paginate(10)
                ->appends($request->query());

            if (!$followers) {
                return response()->json(
                    data: [
                        'message' => 'There is a failure when try to get followers!',
                        'success' => false,
                        'data' => null,
                        'error' => [
                            'code' => 404,
                            'message' => 'Followers not found!',
                        ],
                    ],
                    status: 404,
                );
            }

            $user->attachFollowStatus($followers);

            return response()->json([
                'message' => 'These are your followers!',
                'success' => false,
                'data' => $followers->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $followers->total(),
                        'currentPage' => $followers->currentPage(),
                        'perPage' => $followers->perPage(),
                        'lastPage' => $followers->lastPage(),
                        'hasMorePages' => $followers->currentPage() < $followers->lastPage(),
                    ]
                ],
                'error' => null,
            ], status: 200);
        } catch (\Throwable $th) {
            return response()->json(
                data: [
                    'message' => 'There is a failure when try to get your followers!',
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

    public function followings(Request $request)
    {
        try {
            $user = \auth()->user();
            $followings = QueryBuilder::for(User::class)
                ->allowedFilters(['username'])
                ->whereRelation('followables', 'user_id', $user->id)
                ->paginate(10)
                ->appends($request->query());

            if (!$followings) {
                return response()->json(
                    data: [
                        'message' => 'There is a failure when try to get followings!',
                        'success' => false,
                        'data' => null,
                        'error' => [
                            'code' => 404,
                            'message' => 'followings not found!',
                        ],
                    ],
                    status: 404,
                );
            }

            $user->attachFollowStatus($followings);


            return response()->json([
                'message' => 'These are your followings!',
                'success' => false,
                'data' => $followings->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $followings->total(),
                        'currentPage' => $followings->currentPage(),
                        'perPage' => $followings->perPage(),
                        'lastPage' => $followings->lastPage(),
                        'hasMorePages' => $followings->currentPage() < $followings->lastPage(),
                    ]
                ],
                'error' => null,
            ], status: 200);
        } catch (\Throwable $th) {
            return response()->json(
                data: [
                    'message' => 'There is a failure when try to get your followings!',
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

    public function users(Request $request)
    {
        try {
            $user = \auth()->user();
            $query = User::where('id', '!=', $user->id)
                ->where(function ($query) use ($user) {
                    return $query->whereHas('followables', function ($query) use ($user) {
                        return $query->where([
                            ['user_id', '!=', $user->id],
                            ['followable_id', '!=', $user->id]
                        ]);
                    })
                        ->orWhereDoesntHave('followables');
                });
            $users = QueryBuilder::for($query)
                ->defaultSort('name')
                ->allowedSorts('name', 'username')
                ->allowedFilters(['username'])
                ->paginate(10)
                ->appends($request->query());

            if (!$users) {
                return response()->json(
                    data: [
                        'message' => 'There is a failure when try to get users!',
                        'success' => false,
                        'data' => null,
                        'error' => [
                            'code' => 404,
                            'message' => 'Users not found!',
                        ],
                    ],
                    status: 404,
                );
            }

            $user->attachFollowStatus($users);

            return response()->json([
                'message' => 'These are your users!',
                'success' => false,
                'data' => $users->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $users->total(),
                        'currentPage' => $users->currentPage(),
                        'perPage' => $users->perPage(),
                        'lastPage' => $users->lastPage(),
                        'hasMorePages' => $users->currentPage() < $users->lastPage(),
                    ]
                ],
                'error' => null,
            ], status: 200);
        } catch (\Throwable $th) {
            return response()->json(
                data: [
                    'message' => 'There is a failure when try to get your users!',
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
