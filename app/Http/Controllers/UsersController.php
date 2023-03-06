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
        $data = User::where('id', $user_id)
            ->first([
                'id',
                'name',
                Auth::user()->id == $user_id ? 'email' : ''
            ])
            ->toArray();

        if (Auth::user()->id == $user_id) {

            $followers_count = Follower::where('following_id', Auth::user()->id)
                ->count();

            $following_count = Follower::where('follower_id', Auth::user()->id)
                ->count();

            $data = array_merge($data, [
                'followers_count' => $followers_count,
                'following_count' => $following_count
            ]);

        }

        return response([
            'status' => 'success',
            'data' => $data
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
