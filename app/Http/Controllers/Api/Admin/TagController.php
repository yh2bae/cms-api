<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    public function index()
    {
        //get tags
        $tags = Tag::when(request()->q, function($tags) {
            $tags = $tags->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
        
        //return with Api Resource
        return new TagResource(true, 'List Data Tags', $tags);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:tags',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create tag
        $tag = Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($tag) {
            //return success with Api Resource
            return new TagResource(true, 'Tag Data Saved Successfully!', $tag);
        }

        //return failed with Api Resource
        return new TagResource(false, 'Tag Data Saved Failed!', null);
    }

    public function show($id)
    {
        $tag = Tag::whereId($id)->first();
        
        if($tag) {
            //return success with Api Resource
            return new TagResource(true, 'Tag Data Details!', $tag);
        }

        //return failed with Api Resource
        return new TagResource(false, 'Tag Data Details Not Found!', null);
    }

    public function update(Request $request, Tag $tag)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:tags,name,'.$tag->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update tag
        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($tag) {
            //return success with Api Resource
            return new TagResource(true, 'Tag Data Updated Successfully!', $tag);
        }

        //return failed with Api Resource
        return new TagResource(false, 'Tag Data Updated Failed!', null);
    }

    public function destroy(Tag $tag)
    {
        if($tag->delete()) {
            //return success with Api Resource
            return new TagResource(true, 'Tag Data Deleted Successfully!', null);
        }

        //return failed with Api Resource
        return new TagResource(false, 'Tag Data Deleted Failed!', null);
    }
}
