<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;

use App\Models\MenuCategories;
use App\Models\Menu;

class ApiController extends Controller
{
	protected $menuCategories, $menu;
    public function __construct(MenuCategories $menuCategories, Menu $menu)
    {
        $this->categories = $menuCategories;
        $this->menu = $menu;
    }

    public function addOrder(Request $request){
    	return $request;
    }

    public function getData(){
    	$categories = $this->categories->where('status', 1)->get();
    	$menus = $this->menu->where('status', 1)->get();
    	return response()->json([
    		'categories' => $categories,
    		'menus' => $menus
    	]);
    }
}
