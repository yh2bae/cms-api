<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);

        //return with Api Resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    public function show($slug)
    {
        $category = Category::with('posts.tags', 'posts.category', 'posts.comments')->where('slug', $slug)->first();

        if($category) {
            //return with Api Resource
            return new CategoryResource(true, 'List Data Post By Category', $category);
        }

        //return with Api Resource
        return new CategoryResource(false, 'Category Data Not Found!', null);
    }

    public function categorySidebar()
    {
        $categories = Category::orderBy('name', 'ASC')->get();

        //return with Api Resource
        return new CategoryResource(true, 'List Data Categories Sidebar', $categories);
    }
}
