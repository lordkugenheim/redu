<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Post as PostModel;
use App\Models\User;
use App\Models\Post;

class PostsController extends Controller
{
    public function getPost($post_id)
    {
        $posts = Post::where('id', $post_id)
            ->get([
                'id',
                'content',
                'user_id'
            ])
            ->toArray();

            // total number of likes across all users
            // has the authenticated user liked it

        return response([
            'status' => 'success',
            'posts' => $posts
        ]);
    }

    public function getPosts()
    {
        $followers = User::where('id', Auth::user()->id)
            ->with('followers')
            ->get()
            ->pluck('followers')
            ->first()
            ->pluck('follower_id')
            ->toArray();

        $posts = Post::whereIn('user_id', $followers)
            ->get();

        return response([
            'status' => 'success',
            'posts' => $posts
        ]);
    }

    public function like($post_id)
    {
        User::where('id', Auth::user()->id)
            ->first()
            ->likes()
            ->updateOrCreate([
                'post_id' => $post_id
            ]);
    }

    public function unlike($post_id)
    {
        User::where('id', Auth::user()->id)
            ->first()
            ->likes()        
            ->where([
                'likes.post_id' => $post_id
            ])
            ->delete();
    }
}
