<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index()
    {
        //get menus
        $menus = Menu::when(request()->q, function($menus) {
            $menus = $menus->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(10);
        
        //return with Api Resource
        return new MenuResource(true, 'List Data Menu', $menus);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'url'      => 'required' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create menu
        $menu = Menu::create([
            'name' => $request->name,
            'url'  => $request->url,
        ]);

        if($menu) {
            //return success with Api Resource
            return new MenuResource(true, 'Menu Data Saved Successfully!', $menu);
        }

        //return failed with Api Resource
        return new MenuResource(false, 'Menu Data Saved Failed!', null);
    }

    public function show($id)
    {
        $menu = Menu::whereId($id)->first();
        
        if($menu) {
            //return success with Api Resource
            return new MenuResource(true, 'Menu Data Details!', $menu);
        }

        //return failed with Api Resource
        return new MenuResource(false, 'Menu Data Details Not Found!', null);
    }

    public function update(Request $request, Menu $menu)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'url'      => 'required' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update menu
        $menu->update([
            'name' => $request->name,
            'url'  => $request->url,
        ]);

        if($menu) {
            //return success with Api Resource
            return new MenuResource(true, 'Menu Data Updated Successfully!', $menu);
        }

        //return failed with Api Resource
        return new MenuResource(false, 'Menu Data Updated Failed!', null);
    }

    public function destroy(Menu $menu)
    {
        if($menu->delete()) {
            //return success with Api Resource
            return new MenuResource(true, 'Menu Data Deleted Successfully!', null);
        }

        //return failed with Api Resource
        return new MenuResource(false, 'Menu Data Deleted Failed!', null);
    }
}
