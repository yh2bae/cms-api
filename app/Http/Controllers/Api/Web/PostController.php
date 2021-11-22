<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'category', 'tags', 'comments')->when(request()->q, function($posts) {
            $posts = $posts->where('title', 'like', '%'. request()->q . '%');
        })->latest()->paginate(6);

        //return with Api Resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function show($slug)
    {
        $post = Post::with('user', 'category', 'tags', 'comments')->where('slug', $slug)->first();

        if($post) {
            //return with Api Resource
            return new PostResource(true, 'Post Detail Data', $post);
        }

        //return with Api Resource
        return new PostResource(true, 'Post Detail Data Not Found!', null);
    }

    public function postHomepage()
    {
        $posts = Post::with('user', 'category', 'tags', 'comments')->take(5)->latest()->get();

        //return with Api Resource
        return new PostResource(true, 'List Data Posts Homepage', $posts);
    }

    public function storeComment(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email',
            'comment'  => 'required' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get Post by slug
        $post = Post::where('slug', $request->slug)->first();

        //store comment
        $post->comments()->create([
            'name'      => $request->name,
            'email'     => $request->email,
            'comment'   => $request->comment
        ]);

        //return with Api Resource
        return new PostResource(true, 'Comment Saved Successfully!', $post->comments()->get());

    }

    public function storeImagePost(Request $request)
    {
        //upload new image
        $image = $request->file('upload');
        $image->storeAs('public/post_images', $image->hashName());

        return response()->json([
            'url' => asset('storage/post_images/'.$image->hashName())
        ]);
    }


}
