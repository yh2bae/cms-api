<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tag;

class DashboardController extends Controller
{
    public function index()
    {
        $posts      = Post::count();
        $comments   = Comment::count();
        $categories = Category::count();
        $tags = Tag::count();
        $users      = User::count();

        return response()->json([
            'success' => true,
            'message' => 'List Count Data Table',  
            'data' => [
                'posts'      => $posts,
                'comments'   => $comments,
                'categories' => $categories,
                'tags'       => $tags,
                'users'      => $users
            ],
        ], 200);
    }
}
