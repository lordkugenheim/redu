<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Post as PostModel;
use App\Models\User;
use App\Models\Post;

class PostsController extends Controller
{

    private $PostModel = '';

    public function __construct()
    {
        $PostModel = new PostModel();
    }

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
        // return a list of all the posts from all the people the authenticated user has followed

        $user = new User();
        $query = $user->where('id', Auth::user()->id)->with('posts')->get();

        dd($query);
    
    }

    public function like()
    {
        die('hayo');
    }

    public function unlike()
    {
        die('hayo');
    }
}
