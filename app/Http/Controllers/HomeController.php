<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
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
        $mode = ['/dashbaord'];

        // $this->audit_trail_logs('','','','');
        
        return view('pages.dashboard.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Dashboard',
            'title' => 'Dashboard'
        ]);
    }
}
