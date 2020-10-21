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

    public function addOrder(){
    	
    }
}
