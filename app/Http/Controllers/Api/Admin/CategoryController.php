<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        //get categories
        $categories = Category::when(request()->q, function($categories) {
            $categories = $categories->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
        
        //return with Api Resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'    => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'     => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        // $image = $request->file('image');
        // $image->storeAs('public/categories', $image->hashName());

        $img = $request->file('image');
        $category['image'] = $img->getClientOriginalName();

        $filePath = public_path('/upload/categories');
        $img->move($filePath, $category['image']);

        //create category
        $category = Category::create([
            'image'=> $category['image'],
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Category Data Saved Successfully!', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Category Data Saved Failed!', null);
    }

    public function show($id)
    {
        $category = Category::whereId($id)->first();
        
        if($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Category Data Details!', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Category Data Details Not Found!', null);
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,'.$category->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('image')) {

            //remove old image
            $old_image = public_path('upload/categories/'.basename($category->image)); 
            if(File::exists($old_image)) {
                File::delete($old_image);
            }
        
            //upload new image
            $img = $request->file('image');
            $category['image'] = $img->getClientOriginalName();

            $filePath = public_path('/upload/categories');
            $img->move($filePath, $category['image']);

            //update category with new image
            $category->update([
                'image'=> $category['image'],
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
            ]);

        }

        //update category without image
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Category Data Updated Successfully!', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Category Data Updated Failed!!', null);
    }


    public function destroy(Category $category)
    {
        //remove image
        $old_image = public_path('upload/categories/'.basename($category->image)); 
        if(File::exists($old_image)) {
            File::delete($old_image);
        } 

        if($category->delete()) {
            //return success with Api Resource
            return new CategoryResource(true, 'Category Data Deleted Successfully!', null);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Category Data Deleted Failed!', null);
    }

    
}
