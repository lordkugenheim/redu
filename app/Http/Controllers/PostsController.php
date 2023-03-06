<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Post as PostModel;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;

class PostsController extends Controller
{
    public function getPost($post_id)
    {
        $posts = Post::select(    
                'id',
                'content',
                'user_id'
            )
            ->where('id', $post_id)
            ->withCount('likes')
            ->orderBy('created_at', 'DESC')
            ->first()
            ->toArray();

        $like = Like::where('user_id', Auth::user()->id)
            ->where('post_id', $post_id)
            ->get();

        $posts['user_liked'] = !$like->isEmpty();

        return response([
            'status' => 'success',
            'data' => $posts
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
