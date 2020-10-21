<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;

use App\Models\MenuCategories;
use App\Models\Menu;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $menuCategories, $menu;
    public function __construct(MenuCategories $menuCategories, Menu $menu)
    {
        // $this->middleware('auth');
        $this->categories = $menuCategories;
        $this->menu = $menu;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showMain(){
        // if (Auth::id() == 1) { // ADMIN
        //     return redirect()->route('dashboard');
        // }else if(Auth::id() == 2){ // CASHIER
        //     return redirect()->route('home');
        // }else if(Auth::id() == 3){ // MANAGER
        //     return redirect()->route('home');
        // }else if (Auth::id() == 4) { // COOK
        //     return redirect()->route('home'); 
        // }

        return redirect()->route('dashboard');
    }

    public function dashboard()
    {
        $name = ['Dashboard'];
        $mode = ['/dashboard'];

        $this->audit_trail_logs('','','','');
        
        return view('pages.dashboard.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Dashboard',
            'title' => 'Dashboard'
        ]);
    }

    public function cashier(){
        $name = ['Cashier'];
        $mode = ['/cashier'];

        $categories = $this->categories->where('status', 1)->oldest()->get();
        $menus = $this->menu->where('status', 1)->latest()->get();
        $countMenus = count($menus);

        $this->audit_trail_logs('','','','');
        
        return view('pages.cashier.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Cashier',
            'title' => 'Cashier',
            'menu_categories' => $categories,
            'menus' => $menus,
            'countMenus' => $countMenus
        ]);
    }

    public function cook(){
        $name = ['Cook'];
        $mode = ['/cook'];

        $this->audit_trail_logs('','','','');
        
        return view('pages.cook.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Cook',
            'title' => 'Cook'
        ]);
    }
}
