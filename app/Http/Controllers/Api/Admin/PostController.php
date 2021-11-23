<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'category', 'comments', 'tags')->when(request()->q, function($posts) {
            $posts = $posts->where('title', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'         => 'required|unique:posts',
            'category_id'   => 'required',
            'content'       => 'required',
            'description'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        // $image = $request->file('image');
        // $image->storeAs('public/posts', $image->hashName());

        $img = $request->file('image');
        $post['image'] = $img->getClientOriginalName();

        $filePath = public_path('/upload/posts');
        $img->move($filePath, $post['image']);

        $post = Post::create([
            'image'       => $post['image'],
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id'     => auth()->guard('api')->user()->id,
            'content'     => $request->content,
            'description' => $request->description    
        ]);

        //assign tags
        $post->tags()->attach($request->tags);
        $post->save();

        if($post) {
            //return success with Api Resource
            return new PostResource(true, 'Post Data Saved Successfully!', $post);
        }

        //return failed with Api Resource
        return new PostResource(false, 'Post Data Saved Failed!', null);
    }

    public function show($id)
    {
        $post = Post::with('tags', 'category')->whereId($id)->first();
        
        if($post) {
            //return success with Api Resource
            return new PostResource(true, 'Post Data Details!', $post);
        }

        //return failed with Api Resource
        return new PostResource(false, 'Post Data Details Not Found!', null);
    }

    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:posts,title,'.$post->id,
            'category_id'   => 'required',
            'content'       => 'required',
            'description'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('image')) {

            //remove old image
            $old_image = public_path('upload/posts/'.basename($post->image)); 
            if(File::exists($old_image)) {
                File::delete($old_image);
            }

            //upload new image
            $img = $request->file('image');
            $post['image'] = $img->getClientOriginalName();

            $filePath = public_path('/upload/posts');
            $img->move($filePath, $post['image']);

            $post->update([
                'image'       => $post['image'],
                'title'       => $request->title,
                'slug'        => Str::slug($request->title, '-'),
                'category_id' => $request->category_id,
                'user_id'     => auth()->guard('api')->user()->id,
                'content'     => $request->content,
                'description' => $request->description    
            ]);

        }

        $post->update([
            'title'       => $request->title,
                'slug'        => Str::slug($request->title, '-'),
                'category_id' => $request->category_id,
                'user_id'     => auth()->guard('api')->user()->id,
                'content'     => $request->content,
                'description' => $request->description   
        ]);

        //sync tags
        $post->tags()->sync($request->tags);
        $post->save();

        if($post) {
            //return success with Api Resource
            return new PostResource(true, 'Post Data Updated Successfully!', $post);
        }

        //return failed with Api Resource
        return new PostResource(false, 'Post Data Updated Failed!', null);
    }

    public function destroy(Post $post)
    {
        $post->tags()->detach();
        //remove image
        $old_image = public_path('upload/posts/'.basename($post->image)); 
        if(File::exists($old_image)) {
            File::delete($old_image);
        }

        if($post->delete()) {
            //return success with Api Resource
            return new PostResource(true, 'Post Data Deleted Successfully!', null);
        }

        //return failed with Api Resource
        return new PostResource(false, 'Post Data Deleted Failed!', null);
    }
}
