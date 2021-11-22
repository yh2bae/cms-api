<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::oldest()->get();

        //return with Api Resource
        return new MenuResource(true, 'List Data Menus', $menus);
    }
}
