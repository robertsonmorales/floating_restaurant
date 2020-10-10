<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\EmployeePositions;

class EmployeePositionController extends Controller
{
    protected $employees;
    public function __construct(EmployeePositions $employees){
        $this->employee = $employees;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:employee_positions,name,'.$this->safeInputs($request->input('id')).'',            
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
        $name = ['Employee Positions'];
        $mode = [route('employee_positions.index')];
        
        $rows = array();
        $rows = $this->employee->latest()->get();
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

        return view('pages.employee_positions.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Employee Positions',
            'title' => 'Employee Positions'
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
        $name = ['Employee Positions', 'Create'];
        $mode = [route('employee_positions.index'), route('employee_positions.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.employee_positions.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Employee Positions',
            'title' => 'Employee Positions'
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
            $this->employee->name = $validated['name'];
            $this->employee->status = $validated['status'];
            $this->employee->created_by = Auth::user()->id;
            $this->employee->created_at = Carbon::now();
            $this->employee->save();

            $this->audit_trail_logs('', 'created', 'employee_positions: '.$validated['name'], $this->employee->id);

            return redirect()->route('employee_positions.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->employee->findOrFail($id);
        $mode_action = 'update';
        $name = ['Employee Positions', 'Edit', $data->name];
        $mode = [route('employee_positions.index'), route('employee_positions.edit', $id), route('employee_positions.edit', $id)];

        $this->audit_trail_logs('', '', 'employee_positions: '.$data->name, $id);

        return view('pages.employee_positions.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Employee Positions',
            'title' => 'Employee Positions',
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
            $data = $this->employee->find($id);
            $data->name = $validated['name'];
            $data->status = $validated['status'];
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'employee_positions: '.$data->name, $id);

            return redirect()->route('employee_positions.index')->with('success', 'You have successfully updated '.$validated['name']);  
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
        $data = $this->employee->findOrFail($id);
        $this->audit_trail_logs('', 'deleted', 'employee_positions '.$data->name, $id);
        $data->delete();

        return redirect()->route('employee_positions.index')->with('success', 'You have successfully removed '.$data->name);
    }
}
