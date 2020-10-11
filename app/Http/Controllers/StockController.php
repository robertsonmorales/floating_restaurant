<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Arr;

use App\Models\Stock;
use App\Models\ProductUnits;
use App\Models\User;

class StockController extends Controller
{
    protected $stock, $unit;

    public function __construct(Stock $stock, ProductUnits $unit){
        $this->stock = $stock;        
        $this->unit = $unit;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = ['Stocks'];
        $mode = [route('stocks.index')];        
        
        $rows = array();

        $selectRow = ['s.product_name', 's.product_category_name', 's.stocks', 's.unit', 'p.minimum_stocks', 's.created_by', 's.created_at'];
        $rows = DB::table('stocks as s')
            ->leftJoin('products as p', 'p.id', 's.product_id')
            ->latest('p.created_at')->get($selectRow);

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
        $columnDefs[] = array_merge(array('headerName'=>'Categories','field'=>'product_category_name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Products','field'=>'product_name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Stocks','field'=>'stocks'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);

        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');        

        return view('pages.stocks.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'header' => 'Stocks',
            'title' => 'Stocks'
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
                $value->stocks = $value->stocks.' '.$unit->name;
            }

            if(property_exists($value, 'created_by')){
                $users = User::select('username')->where('id', $value->created_by)->first();
                $value->created_by = $users->username;
            }
        }

        return $rows;
    }
}
