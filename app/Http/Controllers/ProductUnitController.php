<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\ProductUnits;

class ProductUnitController extends Controller
{
    public function __construct(ProductUnits $productUnit){
        $this->productUnit = $productUnit;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:product_units,name,'.$this->safeInputs($request->input('id')).'',
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
        $name = ['Product Units'];
        $mode = [route('product_units.index')];
        
        $rows = array();
        $rows = $this->productUnit->latest()->get();
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

        return view('pages.product_units.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Product Units',
            'title' => 'Product Units'
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
        $name = ['Product Units', 'Create'];
        $mode = [route('product_units.index'), route('product_units.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.product_units.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Product Units',
            'title' => 'Product Units'
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
            $this->productUnit->name = $validated['name'];
            $this->productUnit->status = $validated['status'];
            $this->productUnit->created_by = Auth::user()->id;
            $this->productUnit->created_at = Carbon::now();
            $this->productUnit->save();

            $this->audit_trail_logs('', 'created', 'product_units: '.$validated['name'], $this->productUnit->id);

            return redirect()->route('product_units.index')->with('success', 'You have successfully added '.$validated['name'].' product unit');
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
        $data = $this->productUnit->findOrFail($id);
        $mode_action = 'update';
        $name = ['Product Units', 'Edit', $data->name];
        $mode = [route('product_units.index'), route('product_units.edit', $id), route('product_units.edit', $id)];

        $this->audit_trail_logs('', '', 'product_units: '.$data->name, $id);

        return view('pages.product_units.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Product Units',
            'title' => 'Product Units',
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
            $data = $this->productUnit->find($id);
            $data->name = $validated['name'];
            $data->status = $validated['status'];
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'product_units: '.$data->name, $id);

            return redirect()->route('product_units.index')->with('success', 'You have successfully updated '.$validated['name'].' product unit');
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
        $data = ProductUnits::findOrFail($id);
        $this->audit_trail_logs('', 'deleted', 'product_units '.$data->name, $id);
        $data->delete();

        return redirect()->route('product_units.index')->with('success', 'You have successfully removed '.$data->name.' product unit');
    }
}
