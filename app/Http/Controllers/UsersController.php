<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Post as PostModel;
use App\Models\User;
use App\Models\Follower;

class UsersController extends Controller
{
    /**
     * Retrieve a single user profile
     * 
     * Authenticated users retrieving their own profile 
     * have 'email', 'followers_count' and 'following_count' included
     * 
     * /api/users/{id}
     * 
     * @param int $user_id
     * @return Response
     */
    public function getUser($user_id): Response
    {
        $select_fields = ['id', 'name'];
        
        Auth::user()->id == $user_id ? array_push($select_fields, ['email']) : '';

        $data = User::where('id', $user_id)
            ->first($select_fields)
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

    /**
     * Retrieve all users
     * 
     * Authenticated users 
     * have 'email', 'followers_count' and 'following_count'
     * included on their own profile entry
     * 
     * /api/users
     *
     * @return Response
     */
    public function getUsers(): Response
    {
        $users = User::get([
            'id',
            'name',
            'email'
        ]);

        $users->map(function($user) {
           
            if ($user->id == Auth::user()->id) {

                $user->followers_count = Follower::where('following_id', Auth::user()->id)
                    ->count();

                $user->following_count = Follower::where('follower_id', Auth::user()->id)
                    ->count();

            } else {
                unset($user->email);
            }

            return $user;

        });

        $users->chunk(10)
            ->toArray();

        return response([
            'status' => 'success',
            'users' => $users
        ]);
    }

    /**
     * 'Follow' a user
     * 
     * only available to authenticated users
     * 
     * /api/users/{id}/follow
     * 
     * @param int $user_id
     * @return Response
     */
    public function followUser($user_id): Response
    {
        if (Auth::user()->id) {

            User::where('id', Auth::user()->id)
                ->first()
                ->followers()
                ->updateOrCreate([
                    'following_id' => $user_id
                ]);

            return response([
                'status' => 'success',
                'message' => 'Follow added for profile ' . $user_id
            ]);
        }

        return response([
            'status' => 'error',
            'message' => 'Authentication required'
        ], 401);
    }

    /**
     * 'Unfollow' a user
     * 
     * only available to authenticated users
     * 
     * /api/users/{id}/unfollow
     * 
     * @param int $user_id
     * @return Response
     */
    public function unfollowUser($user_id): Response
    {
        if (Auth::user()->id) {

            User::where('id', Auth::user()->id)
                ->first()
                ->followers()
                ->where([
                    'followers.following_id' => $user_id
                ])
                ->delete();

            return response([
                'status' => 'success',
                'message' => 'Follow removed for profile ' . $user_id
            ]);
        }

        return response([
            'status' => 'error',
            'message' => 'Authentication required'
        ], 401);
    }
}
