<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::latest()->get();

        //return with Api Resource
        return new TagResource(true, 'List Data Tags', $tags);
    }

    public function show($slug)
    {
        $tag = Tag::with('posts.tags', 'posts.category', 'posts.comments')->where('slug', $slug)->first();

        if($tag) {
            //return with Api Resource
            return new TagResource(true, 'List Data Posts By Tag', $tag);
        }

        //return with Api Resource
        return new TagResource(false, 'Tag Data Not Found!', null);
    }
}
