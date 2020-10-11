<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\CustomerDiscount;

class CustomerDiscountController extends Controller
{
    protected $discount;
    public function __construct(CustomerDiscount $discount){
        $this->discount = $discount;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'percentage' => $this->safeInputs($request->input('percentage')),
            'verification' => $this->safeInputs($request->input('verification')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:customer_discounts,name,'.$this->safeInputs($request->input('id')).'',
            'percentage' => 'required|numeric|max:100',
            'verification' => 'required|numeric',
            'status' => 'required|numeric'
        ];

        $messages = [];

        $customAttributes = [
            'name' => 'name',
            'percentage' => 'percentage',
            'verification' => 'verification',
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
        $name = ['Customer Discount'];
        $mode = [route('customer_discounts.index')];
        
        $rows = array();
        $rows = $this->discount->latest()->get();
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
        $columnDefs[] = array_merge(array('headerName'=>'Percentage','field'=>'percentage'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Verification','field'=>'verification'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Status','field'=>'status'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'updated_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'updated_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.customer_discounts.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Customer Discount',
            'title' => 'Customer Discount'
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
        $name = ['Customer Discount', 'Create'];
        $mode = [route('customer_discounts.index'), route('customer_discounts.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.customer_discounts.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Customer Discount',
            'title' => 'Customer Discount'
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
            $this->discount->name = $validated['name'];
            $this->discount->percentage = $validated['percentage'];
            $this->discount->verification = $validated['verification'];
            $this->discount->status = $validated['status'];
            $this->discount->created_by = Auth::user()->id;
            $this->discount->created_at = Carbon::now();
            $this->discount->save();

            $this->audit_trail_logs('', 'created', 'customer_discounts: '.$validated['name'], $this->discount->id);

            return redirect()->route('customer_discounts.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->discount->findOrFail($id);
        $mode_action = 'update';
        $name = ['Customer Discount', 'Edit', $data->name];
        $mode = [route('customer_discounts.index'), route('customer_discounts.edit', $id), route('customer_discounts.edit', $id)];

        $this->audit_trail_logs('', '', 'customer_discounts: '.$data->name, $id);

        return view('pages.customer_discounts.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Customer Discount',
            'title' => 'Customer Discount',
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
            $data = $this->discount->find($id);
            $data->name = $request->input('name');
            $data->percentage = $request->input('percentage');
            $data->verification = $request->input('verification');
            $data->status = $request->input('status');
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'customer_discounts: '.$data->name, $id);

            return redirect()->route('customer_discounts.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->discount->findOrFail($id);
        $this->audit_trail_logs('', 'deleted', 'customer_discounts '.$data->name, $id);
        $data->delete();

        return redirect()->route('customer_discounts.index')->with('success', 'You have successfully removed '.$data->name);
    }

    public function changeValue($rows){
        foreach ($rows as $key => $value) {
            if(Arr::exists($value, 'verification')){
                 if($value->verification == 1){
                    $value->verification = 'Required';
                 }else{
                    $value->verification = 'Not Required';
                 }
            }

            if(Arr::exists($value, 'percentage')){
                $value->percentage = $value->percentage.'%';
            }
        }

        return $rows;
    }
}
