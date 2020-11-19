<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Arr;

use Carbon\Carbon;
use App\Models\Delivery;
use App\Models\Products;
use App\Models\Stock;
use App\Models\InventoryTransaction;
use App\Models\ProductUnits;

class DeliveryController extends Controller
{
    protected $delivery, $product, $stock, $inventory, $unit;

    public function __construct(Delivery $delivery, Products $product, Stock $stock, InventoryTransaction $inventory, ProductUnits $unit){
        $this->delivery = $delivery;
        $this->product = $product;
        $this->stock = $stock;
        $this->inventory = $inventory;
        $this->unit = $unit;
    }

    public function validator(Request $request)
    {
        $input = [
            'product' => $request->input('product'),
            'quantity' => $this->safeInputs($request->input('quantity')),
            'description' => $this->safeInputs($request->input('description')),
            'approved_by' => $this->safeInputs($request->input('approved_by'))
        ];

        $rules = [
            'product' => 'required|string|max:255',
            'quantity' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
            'approved_by' => 'required|string|max:100',
        ];

        $messages = [];

        $customAttributes = [
            'product' => 'product',
            'quantity' => 'quantity',
            'description' => 'description',
            'approved_by' => 'approval',
        ];                

        $validator = Validator::make($input, $rules, $messages,$customAttributes);
        return $validator->validate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = ['Deliveries'];
        $mode = [route('deliveries.index')];        
        
        $rows = array();
        $rows = $this->delivery->latest()->get();
        $row = $this->changeVal($rows);
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
        $columnDefs[] = array_merge(array('headerName'=>'Products','field'=>'product_name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Quantity','field'=>'qty'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Approved By','field'=>'approved_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Description','field'=>'description'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'created_at'), $arr_set);

        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.deliveries.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Deliveries',
            'title' => 'Deliveries'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mode_action = 'create';
        $name = ['Deliveries', 'Create'];
        $mode = [route('deliveries.index'), route('deliveries.create')];

        $products = $this->product->where([
            ['inventoriable', 1],
            ['status', 1]
        ])->latest()->get();

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.deliveries.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Deliveries',
            'title' => 'Deliveries',
            'products' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $this->validator($request);
        if($validated){
            $explodeProduct = explode("|", $request->input('product'));
            $product = $this->product->find($explodeProduct[0]);

            $this->delivery->product_id = $explodeProduct[0];
            $this->delivery->product_name = $explodeProduct[1];
            $this->delivery->qty = $validated['quantity'];
            $this->delivery->unit = $product->unit;
            $this->delivery->description = $validated['description'];
            $this->delivery->approved_by = $validated['approved_by'];
            $this->delivery->created_by = Auth::user()->id;
            $this->delivery->created_at = now();
            $this->delivery->save();

            $stock = $this->stock->find($explodeProduct[0]);
            $stock->stocks = $stock->stocks + $validated['quantity'];
            $stock->save();

            $this->inventory->product_id = $stock->product_id;
            $this->inventory->product_name = $stock->product_name;
            $this->inventory->product_category_id = $stock->product_category_id;
            $this->inventory->product_category_name = $stock->product_category_name;
            $this->inventory->type = 1;
            $this->inventory->qty = $validated['quantity'];
            $this->inventory->unit = $stock->unit;
            $this->inventory->stocks = $stock->stocks;
            $this->inventory->created_by = Auth::id();
            $this->inventory->created_at = now();
            $this->inventory->save();

            $this->audit_trail_logs('', 'created', 'deliveries: '.$explodeProduct[1], $this->delivery->id);

            return redirect()->route('deliveries.index')->with('success', 'You have successfully added '.$stock->product_name);
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
        $data = $this->delivery->findOrFail($id);
        $mode_action = 'update';
        $name = ['Deliveries', 'Edit', $data->product_name];
        $mode = [route('deliveries.index'), route('deliveries.edit', $id), route('deliveries.edit', $id)];

        $this->audit_trail_logs('', '', 'deliveries: '.$data->product_name, $id);

        $products = $this->product->where('status', 1)->latest()->get();
        $select_product = $this->product->find($data->product_id);

        return view('pages.deliveries.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Deliveries',
            'title' => 'Deliveries',
            'data' => $data,
            'products' => $products,
            'select_product' => $select_product
        ]);
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
        $validated = $this->validator($request);
        if($validated){
            $product = explode("|", $request->input('product'));
            $unit = $this->product->find($product[0]);

            $data = $this->delivery->findOrFail($id);
            $data->product_id = $product[0];
            $data->product_name = $product[1];
            $data->qty = $validated['quantity'];
            $data->unit = $unit->unit;
            $data->description = $validated['description'];
            $data->approved_by = $validated['approved_by'];
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'deliveries: '.$data->product_name, $id);

            return redirect()->route('deliveries.index')->with('success', 'You have successfully updated '.$product[1]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->delivery->findOrFail($id);
        $data->delete();
        $this->audit_trail_logs('', 'deleted', 'deliveries '.$data->product_name, $id);

        return redirect()->route('deliveries.index')->with('success', 'You have successfully removed '.$data->product_name);
    }

    public function changeValue($rows)
    {       
        foreach ($rows as $value) {
            if (Arr::exists($value, 'qty')) {
                $unit = $this->unit->findOrFail($value->unit);
                $value->qty = ''.$value->qty.' '.$unit->name.'';
            }
        }

        return $rows;
    }
}
