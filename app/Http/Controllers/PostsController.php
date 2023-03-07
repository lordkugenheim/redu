<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Post as PostModel;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;

class PostsController extends Controller
{
    /**
     * Return a single post
     * 
     * /api/posts/{id}
     * 
     * @param int $post_id
     * @return Response
     */
    public function getPost($post_id): Response
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

    /*
     * Return all posts of users that the authenticated user has followed
     * 
     * /api/posts
     * 
     * @return Response
     */
    public function getPosts(): Response
    {
        $followers = User::where('id', Auth::user()->id)
            ->with('followers')
            ->get()
            ->pluck('followers')
            ->first()
            ->pluck('follower_id')
            ->toArray();

        $posts = Post::select(
                'id',
                'content',
                'user_id'
            )
            ->whereIn('user_id', $followers)
            ->withCount('likes')
            ->get()
            ->chunk(10)
            ->toArray();

        return response([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    /*
     * 'Like' a single post
     * 
     * only available to authenticated users
     * 
     * /api/posts/{id}/like
     * 
     * @return Response
     */
    public function like($post_id)
    {
        if (Auth::user()->id) {

            User::where('id', Auth::user()->id)
                ->first()
                ->likes()
                ->updateOrCreate([
                    'post_id' => $post_id
                ]); 

            return response([
                'status' => 'success',
                'message' => 'Like added to post ID: ' . $post_id
            ])
        }
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
