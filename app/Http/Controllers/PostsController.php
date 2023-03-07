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

    /**
     * Create a Post
     * 
     * Only available to authenticated users
     * 
     * @method PUT
     * /api/posts
     *
     * @param Request $request
     * @return Response
     */
    public function createPost(Request $request): Response 
    {
        if (!Auth::user()->id) {

            return response([
                'status' => 'error',
                'message' => 'Authentication required'
            ], 401);

        }

        if ($request->content == '' || !isset($request->content)) {

            return response([
                'status' => 'error',
                'message' => 'Missing or incomplete parameter: Content'
            ], 400);

        }

        $id = Post::create([
            'user_id' => Auth::user()->id,
            'content' => $request->content
        ]);

        if ($id) {
            return response([
                'status' => 'success',
                'message' => 'Post ' . $id . ' was created successfully'
            ]);
        }
        
        return response([
            'status' => 'error',
            'message' => 'An error occured, please try again'
        ], 500);

    }

    /**
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

    /**
     * 'Like' a single post
     * 
     * only available to authenticated users
     * 
     * /api/posts/{id}/like
     *
     * @param int $post_id
     * @return Response
     */
    public function like($post_id): Response
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
            ]);
        }

        return response([
            'status' => 'error',
            'message' => 'Authentication required'
        ], 401);
    }

    /**
     * 'Unlike' a single post
     * 
     * only available to authenticated users
     * 
     * /api/posts/{id}/unlike
     * 
     * @param int $post_id
     * @return Response
     */
    public function unlike($post_id): Response
    {
        if (Auth::user()->id) {

            User::where('id', Auth::user()->id)
                ->first()
                ->likes()        
                ->where([
                    'likes.post_id' => $post_id
                ])
                ->delete();

            return response([
                'status' => 'success',
                'message' => 'Like removed from post ID: ' . $post_id
            ]);
        }

        return response([
            'status' => 'error',
            'message' => 'Authentication required'
        ], 401);
    }
}
