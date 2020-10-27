<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\Damage;
use App\Models\Products;
use App\Models\Stock;
use App\Models\InventoryTransaction;
use App\Models\ProductUnits;

class DamageController extends Controller
{
    protected $damage, $product, $stock, $inventory, $unit;

    public function __construct(Damage $damage, Products $product, Stock $stock, InventoryTransaction $inventory, ProductUnits $unit){
        $this->damage = $damage;
        $this->product = $product;
        $this->stock = $stock;
        $this->inventory = $inventory;
        $this->unit = $unit;
    }

    public function validator(Request $request)
    {
        $input = [
            'product' => $request->product,
            'quantity' => $this->safeInputs($request->quantity),
            'description' => $this->safeInputs($request->description),
            'approved_by' => $this->safeInputs($request->approved_by)
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
        $name = ['Damages'];
        $mode = [route('damages.index')];        
        
        $rows = array();
        $rows = $this->damage->latest()->get();
        $row = $this->changeVal($rows);
        $rows = $this->changeValue($rows);

        $arr_set = array(
            'editable' => false,
            'resizable' => true,
            'filter' => true,
            'sortable' => true,
            'floatingFilter' => true,
            'resizable' => true,
            'flex' => 1
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

        return view('pages.damages.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Damages',
            'title' => 'Damages'
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
        $name = ['Damages', 'Create'];
        $mode = [route('damages.index'), route('damages.create')];

        $products = $this->product->where([
            ['inventoriable', 1],
            ['status', 1]
        ])->latest()->get();

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.damages.create', [
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Damages',
            'title' => 'Damages',
            'products' => $products,
            'stocks' => $this->stock
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
            $stock = $this->stock->where('product_id', $explodeProduct[0])->first();
            if ($stock->stocks >= $validated['quantity']) {
                $this->damage->product_id = $explodeProduct[0];
                $this->damage->product_name = $explodeProduct[1];
                $this->damage->qty = $validated['quantity'];
                $this->damage->unit = $product->unit;
                $this->damage->description = $validated['description'];
                $this->damage->approved_by = $validated['approved_by'];
                $this->damage->created_by = Auth::user()->id;
                $this->damage->created_at = now();
                $this->damage->save();

                $stock = $this->stock->find($explodeProduct[0]);
                $stock->stocks = $stock->stocks - $validated['quantity'];
                $stock->save();

                $this->inventory->product_id = $stock->product_id;
                $this->inventory->product_name = $stock->product_name;
                $this->inventory->product_category_id = $stock->product_category_id;
                $this->inventory->product_category_name = $stock->product_category_name;
                $this->inventory->type = 2;
                $this->inventory->qty = $validated['quantity'];
                $this->inventory->unit = $stock->unit;
                $this->inventory->stocks = $stock->stocks;
                $this->inventory->created_by = Auth::id();
                $this->inventory->created_at = now();
                $this->inventory->save();

                $this->audit_trail_logs('', 'created', 'damages: '.$product[1], $this->damage->id);

                return redirect()->route('damages.index')->with('success', 'Damage Added Successfully');
            }else{
                return back()->with('error', 'Deduction of '.$stock->product_name.' is higher than the current stock');
            }
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
        $data = $this->damage->findOrFail($id);
        $mode_action = 'update';
        $name = ['Damages', 'Edit', $data->product_name];
        $mode = [route('damages.index'), route('damages.edit', $id), route('damages.edit', $id)];

        $this->audit_trail_logs('', '', 'damages: '.$data->product_name, $id);

        $products = $this->product->where('status', 1)->latest()->get();
        $select_product = $this->product->find($data->product_id);

        return view('pages.damages.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Damages',
            'title' => 'Damages',
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

            $data = $this->damage->findOrFail($id);
            $data->product_id = $product[0];
            $data->product_name = $product[1];
            $data->qty = $validated['quantity'];
            $data->unit = $unit->unit;
            $data->description = $validated['description'];
            $data->approved_by = $validated['approved_by'];
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'damages: '.$data->product_name, $id);

            return redirect()->route('damages.index')
                ->with('success', 'Damage Updated Successfully');
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
        $data = $this->damage->findOrFail($id);
        $this->audit_trail_logs('', 'deleted', 'damages '.$data->name, $id);
        $data->delete();

        return redirect()->route('damages.index')->with('success','Damage Removed Successfully');
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
