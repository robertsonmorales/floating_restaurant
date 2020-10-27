<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

use App\Models\InventoryTransaction;
use App\Models\ProductUnits;
use App\Models\User;
use Carbon\Carbon;

class InventoryLogController extends Controller
{
    protected $inventoryTransactions, $unit;

    public function __construct(InventoryTransaction $inventoryTransactions, ProductUnits $unit){
        $this->inventory = $inventoryTransactions;
        $this->unit = $unit;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = ['Inventory Logs'];
        $mode = [route('inventory_logs.index')];        
        
        $rows = array();
        $selectRow = ['i.product_name', 'i.product_category_name', 'i.type', 'i.qty', 'i.stocks', 'i.unit', 'p.minimum_stocks', 'i.created_by', 'i.created_at'];
        $rows = DB::table('inventory_transactions as i')
            ->leftJoin('products as p', 'p.id', 'i.product_id')
            // ->whereDay('i.created_at', today())
            ->latest('i.created_at')->get($selectRow);

        $rows = $this->changeValue($rows);

        $arr_set = array(
            'editable'=>false,
            'resizable'=>true,
            'filter'=>true,
            'sortable'=>true,
            'floatingFilter'=>true,
            'resizable'=>true,
            'flex'=>1
        );

        $columnDefs = array();
        $columnDefs[] = array_merge(array('headerName'=>'Categories','field'=>'product_category_name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Products','field'=>'product_name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Transaction Type','field'=>'type'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Quantity','field'=>'qty'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Current Stocks','field'=>'stocks'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);

        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.inventory_logs.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'header' => 'Inventory Logs',
            'title' => 'Inventory Logs'
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
        //
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
        //
    }

    public function changeValue($rows){
        foreach ($rows as $key => $value) {
            if (property_exists($value, 'stocks')) {
                $unit = $this->unit->find($value->unit);
                $value->stocks = $value->stocks.' '.@$unit->name;
            }

            if(property_exists($value, 'created_by')){
                $users = User::select('username')->where('id', $value->created_by)->first();
                $value->created_by = @$users->username;
            }

            if (property_exists($value, 'type')) {
                if ($value->type == 1) {
                    $value->type = 'Deliveries';
                }elseif ($value->type == 2) {
                    $value->type = "Damages";
                }elseif ($value->type == 3) {
                    $value->type = "Sold out";
                }
            }

            if (property_exists($value, 'qty')) {
                $unit = $this->unit->find($value->unit);
                $value->qty = $value->qty.' '.@$unit->name;
            }
        }

        return $rows;
    }
}
