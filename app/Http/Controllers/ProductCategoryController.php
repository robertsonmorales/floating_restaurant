<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\ProductCategories;

class ProductCategoryController extends Controller
{
    protected $productCategories;
    public function __construct(ProductCategories $productCategories){
        $this->productCategory = $productCategories;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:product_categories,name,'.$this->safeInputs($request->input('id')).'',
            'status' => 'required'
        ];

        $messages = [];

        $customAttributes = [
            'name' => 'name',
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
        $name = ['Product Categories'];
        $mode = [route('product_categories.index')];
        
        $rows = array();
        $rows = $this->productCategory->latest()->get();
        $rows = $this->changeVal($rows);
            
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
        $columnDefs[] = array_merge(array('headerName'=>'Name','field'=>'name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Status','field'=>'status'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'updated_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'updated_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.product_categories.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Product Categories',
            'title' => 'Product Categories'
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
        $name = ['Product Categories', 'Create'];
        $mode = [route('product_categories.index'), route('product_categories.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.product_categories.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Product Categories',
            'title' => 'Product Categories'
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
        if ($validated) {
            $this->productCategory->name = $validated['name'];
            $this->productCategory->status = $validated['status'];
            $this->productCategory->created_by = Auth::id();
            $this->productCategory->created_at = Carbon::now();
            $this->productCategory->save();

            $this->audit_trail_logs('', 'created', 'product_categories: '.$validated['name'], $data->id);

            return redirect()->route('product_categories.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->productCategory->findOrFail($id);
        $mode_action = 'update';
        $name = ['Product Categories', 'Edit', $data->name];
        $mode = [route('product_categories.index'), route('product_categories.edit', $id), route('product_categories.edit', $id)];

        $this->audit_trail_logs('', '', 'product_categories: '.$data->name, $id);

        return view('pages.product_categories.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Product Categories',
            'title' => 'Product Categories',
            'data' => $data
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
            $data = $this->productCategory->find($id);
            $data->name = $validated['name'];
            $data->status = $validated['status'];
            $data->updated_by = Auth::id();
            $data->save();

            $this->audit_trail_logs('', 'updated', 'product_categories: '.$data->name, $id);

            return redirect()->route('product_categories.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->productCategory->findOrFail($id);
        $data->delete();

        $this->audit_trail_logs('', 'deleted', 'product_categories '.$data->name, $id);

        return redirect()->route('product_categories.index')->with('success', 'You have successfully removed '.$data->name);
    }
}
