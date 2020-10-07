<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\OrderStatus;

class OrderStatusController extends Controller
{
    protected $orderStatus;

    public function __construct(OrderStatus $orderStatus){
        $this->orderStatus = $orderStatus;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'color' => $this->safeInputs($request->input('color')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:order_statuses,name,'.$this->safeInputs($request->input('id')).'',
            'color' => 'required|string|unique:order_statuses,color,'.$this->safeInputs($request->input('id')).'',
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
        $name = ['Order Status'];
        $mode = [route('order_status.index')];
        
        $rows = array();
        $rows = $this->orderStatus->latest()->get();
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

        return view('pages.order_status.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Order Status',
            'title' => 'Order Status'
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
        $name = ['Order Status', 'Create'];
        $mode = [route('order_status.index'), route('order_status.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.order_status.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Order Status',
            'title' => 'Order Status'
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
            $this->orderStatus->name = $validated['name'];
            $this->orderStatus->color = $validated['color'];
            $this->orderStatus->status = $validated['status'];
            $this->orderStatus->created_by = Auth::id();
            $this->orderStatus->created_at = now();
            $this->orderStatus->save();

            $this->audit_trail_logs('', 'created', 'order_status: '.$validated['name'], $this->orderStatus->id);

            return redirect()->route('order_status.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->orderStatus->findOrFail($id);
        $mode_action = 'update';
        $name = ['Order Status', 'Edit', $data->name];
        $mode = [route('order_status.index'), route('order_status.edit', $id), route('order_status.edit', $id)];

        $this->audit_trail_logs('', '', 'order_status: '.$data->name, $id);

        return view('pages.order_status.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Order Status',
            'title' => 'Order Status',
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
            $data = $this->orderStatus->find($id);
            $data->name = $validated['name'];
            $data->color = $validated['color'];
            $data->status = $validated['status'];
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'order_status: '.$data->name, $id);

            return redirect()->route('order_status.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->orderStatus->findOrFail($id);
        $data->delete();
        $this->audit_trail_logs('', 'deleted', 'order_status '.$data->name, $id);

        return redirect()->route('order_status.index')->with('success', 'You have successfully removed '.$data->name);
    }
}
