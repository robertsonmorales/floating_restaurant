<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\Products;
use App\Models\ProductCategories;
use App\Models\ProductUnits;
use App\Models\User;
use App\Models\Stock;

class ProductController extends Controller
{
    protected $stock, $product, $category, $unit;
    public function __construct(Stock $stock, Products $product, ProductCategories $category, ProductUnits $unit){
        $this->stock = $stock;
        $this->product = $product;
        $this->category = $category;
        $this->unit = $unit;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'unit' => $request->input('unit'),
            'product_categories' => $request->input('product_categories'),
            'inventoriable' => $this->safeInputs($request->input('inventoriable')),
            'minimum_stocks' => $this->safeInputs($request->input('minimum_stocks')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:products,name,'.$request->input('id').'',
            'unit' => 'required|string',
            'product_categories' => 'required',
            'inventoriable' => 'required',
            'minimum_stocks' => 'required|numeric',
            'status' => 'required'
        ];

        $messages = [];

        $customAttributes = [
            'name' => 'name',
            'unit' => 'unit',
            'product_categories' => 'product category',
            'inventoriable' => 'inventoriable',
            'minimum_stocks' => 'minimum stocks',
            'status' => 'status'
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
        $name = ['Products'];
        $mode = [route('products.index')];        
        
        $rows = array();
        $rows = $this->product->latest()->get();
        $rows = $this->changeVal($rows);
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
        $columnDefs[] = array_merge(array('headerName'=>'Name','field'=>'name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Category','field'=>'product_categories_id'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Inventoriable','field'=>'inventoriable'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Status','field'=>'status'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'updated_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'created_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.products.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Products',
            'title' => 'Products'
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
        $name = ['Products', 'Create'];
        $mode = [route('products.index'), route('products.create')];

        $this->audit_trail_logs('','','Creating new record','');

        $product_categories = $this->category->all();
        $product_units = $this->unit->where('status', 1)->get();
        return view('pages.products.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Products',
            'title' => 'Products',
            'product_categories' => $product_categories,
            'product_units' => $product_units
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
            $product_category = explode('|', $request->input('product_categories'));
            $unit = explode('|', $request->input('unit'));

            $this->product->product_categories_id = $product_category[0];
            $this->product->name = $validated['name'];
            $this->product->inventoriable = $validated['inventoriable'];
            $this->product->unit = $unit[0];
            $this->product->minimum_stocks = $validated['minimum_stocks'];
            $this->product->status = $validated['status'];
            $this->product->created_by = Auth::id();
            $this->product->created_at = now();
            $insert = $this->product->save();

            if ($insert && $validated['inventoriable'] == 1) {
                $this->stock->product_id = $this->product->id;
                $this->stock->product_name = $this->product->name;
                $this->stock->product_category_id = $this->product->product_categories_id;
                $this->stock->product_category_name = $product_category[1];
                $this->stock->unit = $unit[1];
                $this->stock->status = $validated['status'];
                $this->stock->created_by = Auth::id();
                $this->stock->created_at = now();
                $this->stock->save();
            }
            
            $this->audit_trail_logs('', 'created', 'products: '.$validated['name'], $this->product->id);

            return redirect()->route('products.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->product->findOrFail($id);
        $mode_action = 'update';
        $name = ['Products', 'Edit', $data->name];
        $mode = [route('products.index'), route('products.edit', $id), route('products.edit', $id)];

        $this->audit_trail_logs('', '', 'products: '.$data->name, $id);

        $product_categories = $this->category->all();
        $select_product_categories = $this->category->find($data->product_categories_id);

        $product_units = $this->unit->where('status', 1)->get();
        $select_product_units = $this->unit->find($data->unit);

        return view('pages.products.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Products',
            'title' => 'Products',
            'data' => $data,
            'product_categories' => $product_categories,
            'select_product_categories' => @$select_product_categories,
            'product_units' => $product_units,
            'select_product_units' => @$select_product_units
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
        if ($validated) {
            $product_category = explode('|', $request->input('product_categories'));
            $unit = explode('|', $request->input('unit'));

            $data = $this->product->find($id);
            $data->product_categories_id = $product_category[0];
            $data->name = $validated['name'];
            $data->inventoriable = $validated['inventoriable'];
            $data->unit = $unit[0];
            $data->minimum_stocks = $validated['minimum_stocks'];
            $data->status = $validated['status'];
            $data->updated_by = Auth::id();
            $data->save();

            if ($validated['inventoriable'] == 1) {
                $checkProduct = $this->stock->withTrashed()->where('product_id', $data->id)->first();
                if (!empty($checkProduct)) {
                    $restore = $checkProduct->restore();
                }else{
                    $this->stock->product_id = $data->id;
                    $this->stock->product_name = $data->name;
                    $this->stock->product_category_id = $data->product_categories_id;
                    $this->stock->product_category_name = $product_category[1];
                    $this->stock->unit = $unit[1];
                    $this->stock->status = $validated['status'];
                    $this->stock->created_by = Auth::id();
                    $this->stock->created_at = now();
                    $this->stock->save();
                }
            }else{
                $inActive = $this->stock->where('product_id', $data->id)->delete();
            }

            $this->audit_trail_logs('', 'updated', 'products: '.$data->name, $id);

            return redirect()->route('products.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->product->findOrFail($id);
        $data->deleted_by = Auth::id();
        $save = $data->save();
        if ($save) {
            $data->delete();

            $stock = $this->stock->where('product_id', $id)->first();
            $stock->deleted_by = Auth::id();
            $stock_save = $stock->save();

            if ($stock_save) {
                $stock->delete();   
            }
        }

        $this->audit_trail_logs('', 'deleted', 'products '.$data->name, $id);

        return redirect()->route('products.index')->with('success', 'You have successfully removed '.$data->name);
    }

    public function changeValue($rows){
        foreach ($rows as $key => $value) {
            if (Arr::exists($value, 'product_categories_id')) {
                $menu_types = $this->category->select('name')->where('id', $value->product_categories_id)->first();
                $value->product_categories_id = $menu_types->name;
            }

            if(Arr::exists($value, 'inventoriable')){
                if($value->inventoriable == 1){
                    $value->inventoriable = 'Yes';
                 }else{
                    $value->inventoriable = 'No';
                 }
            }
        }

        return $rows;
    }
}
