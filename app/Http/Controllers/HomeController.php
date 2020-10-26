<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;
use Arr;

use App\Models\MenuCategories;
use App\Models\Menu;
use App\Models\MenuTypes;
use App\Models\OrderedMenus;
use App\Models\Orders;
use App\Models\Customers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $menuCategories, $menu, $menuType, $orders, $orderedMenus;
    public function __construct(MenuCategories $menuCategories, Menu $menu, MenuTypes $menuType, Orders $orders, OrderedMenus $orderedMenus, Customers $customer){
        // $this->middleware('auth');
        $this->categories = $menuCategories;
        $this->menu = $menu;
        $this->type = $menuType;
        $this->order = $orders;
        $this->orderedMenu = $orderedMenus;
        $this->customer = $customer;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showMain(){
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
        $menus = $this->menu->where('status', 1)->paginate(7);
        $menus = $this->changeValue($menus);
        $countMenus = count($menus);

        $customers = $this->customer->latest('id')->first();
        $customers = @$this->changeVal($customers);
        if(!empty($customers)){
            $order = $this->order->where('customer_id', $customers->id)->latest('id')->first();
            if(!empty($order)){
                $selectedFields = [
                    'm.id',
                    'm.upload_type',
                    'm.menu_image',
                    'om.menu_id',
                    'om.menu_name',
                    'om.unit_price',
                    'om.qty',
                    'om.total_price',
                ];

                $orderList = DB::table('ordered_menus as om')
                    ->select($selectedFields)
                    ->leftJoin('menus as m', 'm.id', 'om.menu_id')->where('om.order_id', $order->id)->latest('om.created_at')->get();
                // $orderedMenu = $this->orderedMenu->where('order_id', $order->id)->get();
                $orderedMenuCount = count($orderList);
                $orderedMenuTotal = $orderList->sum('total_price');
            }
        }

        $this->audit_trail_logs('','','','');
        
        return view('pages.cashier.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Cashier',
            'title' => 'Cashier',
            'menu_categories' => $categories,
            'countMenus' => $countMenus,
            'paginator' => $menus,
            'orderedMenus' => $orderList,
            'orderedMenuCount' => $orderedMenuCount,
            'orderedMenuTotal' => $orderedMenuTotal,
            'transaction_no' => $order->transaction_no
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

    public function changeValue($rows){
        foreach ($rows as $key => $value) {
            if(Arr::exists($value, 'menu_categories_id')){
                $menu_categories = $this->categories->find($value->menu_categories_id);
                $value->menu_categories_id = $menu_categories->name;
            }

            if(Arr::exists($value, 'menu_type_id')){
                $menu_type = $this->type->find($value->menu_type_id);
                $value->menu_type_id = $menu_type->name;   
            }
        }

        return $rows;
    }
}
