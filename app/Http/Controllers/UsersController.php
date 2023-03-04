<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Post as PostModel;
use App\Models\User;
use App\Models\Follower;

class UsersController extends Controller
{
    public function __construct()
    {
        // add the authenticated user model in here
        // save checking that in each endpoint
    }

    public function getUser($user_id)
    {
        $user = User::where('id', $user_id)
            ->get([
                'id',
                'name',
                'email'
            ])
            ->toArray();

            // Follower/Following count when your own profile
            // email when your own profile

        return response([
            'status' => 'success',
            'user' => $user
        ]);
    }

    public function getUsers()
    {
        $users = User::get([
                'id',
                'name',
                'email'
            ])
            ->toArray();

            // Same thing as single endpoint only for all users, authenticated user has extra fields
            // Follower/Following count when your own profile
            // email when your own profile

        return response([
            'status' => 'success',
            'users' => $users
        ]);
    }

    public function followUser($user_id)
    {
        User::where('id', Auth::user()->id)
            ->first()
            ->followers()
            ->updateOrCreate([
                'following_id' => $user_id
            ]);
    }

    public function unfollowUser($user_id)
    {
        User::where('id', Auth::user()->id)
            ->first()
            ->followers()
            ->where([
                'followers.following_id' => $user_id
            ])
            ->delete();
    }
}
