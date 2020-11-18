<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Arr;
use Crypt;

use Carbon\Carbon;
use App\Models\OrderedMenus;
use App\Models\Orders;
use App\Models\Customers;
use App\Models\Menu;
use App\Models\OrderStatus;
use App\Models\User;

class OrderController extends Controller
{
    protected $orders, $orderedMenus, $customer, $menu;
    public function __construct(Orders $orders, OrderedMenus $orderedMenus, Customers $customer, Menu $menu, OrderStatus $orderStatus, User $user){
        $this->order = $orders;
        $this->orderedMenu = $orderedMenus;
        $this->customer = $customer;
        $this->menu = $menu;
        $this->customer = $customer;
        $this->orderStatus = $orderStatus;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = ['Orders'];
        $mode = [route('orders.index')];
            
        $rows = array();
        $rows = $this->order->latest()->get();
        // $rows = $this->changeVal($rows);
        $rows = $this->changeValue($rows);
            
        $arr_set = array(
            'editable'=>false,
            'resizable'=>true,
            'filter'=>true,
            'sortable'=>true,
            'floatingFilter'=>true,
            'flex'=>1
        );

        $columnDefs = array();
        $columnDefs[] = array_merge(array('headerName'=>'Trasanction No.','field'=>'transaction_no'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Name','field'=>'customer_id'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Status','field'=>'status'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Served By','field'=>'served_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'updated_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'updated_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.orders.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'header' => 'Orders',
            'title' => 'Orders'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $menuId = $this->safeInputs($request->input('menu_id'));
        $qty = $this->safeInputs($request->input('qty'));

        $customers = $this->customer->latest('id')->first();
        $customers = @$this->changeVal($customers);
        if(!empty($customers)){
            $order = $this->order->where('customer_id', $customers->id)->latest('id')->first();
            if(!empty($order)){
                $menu = $this->menu->where('id', $menuId)->first();

                $checkOrder = $this->orderedMenu->where(array(
                    'order_id' => $order->id,
                    'menu_id' => $menu->id
                ))->first();

                if (!empty($checkOrder)) {
                    return response()->json([
                        'status' => 404,
                        'text' => 'This menu already exist in the order list, please try the other menus.',
                        'icon' => 'Warning'
                    ]);
                }else{

                    $data = $this->orderedMenu;
                    $data->order_id = $order->id;
                    $data->menu_id = $menu->id;
                    $data->menu_name = $menu->name;
                    $data->unit_price = $menu->price;
                    $data->qty = $qty;
                    $data->total_price = $menu->price * $qty;
                    $data->order_substatus = 1;
                    $data->created_by = Auth::id();
                    $data->created_at = now();
                    $data->save();

                    $orderedItems = $this->orderedMenu->where('order_id', $order->id)->get()->count();
                    return response()->json([
                        'status' => 200,
                        'text' => 'Success',
                        'icon' => 'Success',
                        'data' => array(
                            'ordered_menu' => $data,
                            'menu' => $menu,
                            'ordered_items' => $orderedItems
                        )
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 404,
                    'text' => 'No order history of this customer',
                    'icon' => 'Error'
                ]);
            }
        }else{
            return response()->json([
                'status' => 404,
                'text' => 'Add customer for todays transaction',
                'icon' => 'Error'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = explode(',', $id);
        $orderMenuId = $data[0];
        $orderId = $data[1];

        $data = $this->orderedMenu->findOrFail($orderMenuId);
        $data->delete();

        $orderedItems = $this->orderedMenu->where('order_id', $orderId)->get()->count();
        return response()->json([
            'data' => array(
                'status' => 200,
                'ordered_items' => $orderedItems
            )
        ]);
    }

    public function getOrders(){
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
                    ->leftJoin('menus as m', 'm.id', 'om.menu_id')->where('om.order_id', $order->id)->latest('id')->first();

                // $orderedMenu = $this->orderedMenu->where('order_id', $order->id)->latest('id')->first();
                $orderedMenuCount = $this->orderedMenu->where('order_id', $order->id)->get()->count();
                $orderMenuTotal = $orderList->sum('om.total_price');

                return response()->json([
                    'ordered_menu' => $orderList,
                    'order_count' => $orderedMenuCount,
                    'orderMenuTotal' => $orderMenuTotal
                ]);

            }else{
                return response()->json([
                    'status' => 404,
                    'text' => 'No order history of this customer',
                    'icon' => 'warning'
                ]);
            }
        }else{
            return response()->json([
                'status' => 404,
                'text' => 'Add customer for todays transaction',
                'icon' => 'warning'
            ]);
        }
    }

    public function changeValue($rows){
        foreach ($rows as $key => $value) {
            if (Arr::exists($value, 'transaction_no')) {
                $value->transaction_no = "#".sprintf("%06d", $value->transaction_no);
            }

            if (Arr::exists($value, 'customer_id')) {
                $customer = $this->customer->find($value->customer_id);
                $value->customer_id = $customer->name;
            }

            if(Arr::exists($value, 'status')){
                 $status = $this->orderStatus->find($value->status);
                 $value->status = $status->color.'|'.$status->name;
            }

            if(Arr::exists($value, 'created_by')){
                $users = $this->user->select('username')->where('id', $value->created_by)->first();
                $value->created_by = @$users->username;
            }

            if(Arr::exists($value, 'updated_by')){
                $users = $this->user->select('username')->where('id', $value->updated_by)->first();
                $value->updated_by = @$users->username;
            }
        }

        return $rows;
    }
}
