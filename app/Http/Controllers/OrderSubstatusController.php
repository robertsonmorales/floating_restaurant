<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\OrderSubStatus;

class OrderSubstatusController extends Controller
{

    protected $orderSubstatus;

    public function __construct(OrderSubStatus $orderSubstatus){
        $this->substatus = $orderSubstatus;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'color' => $this->safeInputs($request->input('color')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:order_sub_statuses,name,'.$this->safeInputs($request->input('id')).'',
            'color' => 'required|unique:order_sub_statuses,color,'.$this->safeInputs($request->input('id')).'',
            'status' => 'required'
        ];

        $messages = [];

        $customAttributes = [
            'name' => 'name',
            'color' => 'color',
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
        $name = ['Order Substatus'];
        $mode = [route('order_substatus.index')];
        
        $rows = array();
        $rows = $this->substatus->latest()->get();
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

        return view('pages.order_substatus.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Order Substatus',
            'title' => 'Order Substatus'
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
        $name = ['Order Substatus', 'Create'];
        $mode = [route('order_substatus.index'), route('order_substatus.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.order_substatus.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Order Substatus',
            'title' => 'Order Substatus'
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
            $this->substatus->name = $validated['name'];
            $this->substatus->color = $validated['color'];
            $this->substatus->status = $validated['status'];
            $this->substatus->created_by = Auth::id();
            $this->substatus->created_at = now();
            $this->substatus->save();

            $this->audit_trail_logs('', 'created', 'order_substatus: '.$validated['name'], $this->substatus->id);

            return redirect()->route('order_substatus.index')->with('success', 'You have successfully added '.$validated['name'].' order substatus');
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
        $data = $this->substatus->findOrFail($id);
        $mode_action = 'update';
        $name = ['Order Substatus', 'Edit', $data->name];
        $mode = [route('order_substatus.index'), route('order_substatus.edit', $id), route('order_substatus.edit', $id)];

        $this->audit_trail_logs('', '', 'order_substatus: '.$data->name, $id);

        return view('pages.order_substatus.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Order Substatus',
            'title' => 'Order Substatus',
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
            $data = $this->substatus->find($id);
            $data->name = $request->input('name');
            $data->color = $request->input('color');
            $data->status = $request->input('status');
            $data->updated_by = Auth::id();
            $data->save();

            $this->audit_trail_logs('', 'updated', 'order_substatus: '.$data->name, $id);

            return redirect()->route('order_substatus.index')->with('success', 'You have successfully updated '.$validated['name'].' order substatus');
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
        $data = OrderSubStatus::findOrFail($id);
        $this->audit_trail_logs('', 'deleted', 'order_substatus '.$data->name, $id);
        $data->delete();

        return redirect()->route('order_substatus.index')->with('success', 'You have successfully removed '.$data->name.' order substatus');
    }
}
