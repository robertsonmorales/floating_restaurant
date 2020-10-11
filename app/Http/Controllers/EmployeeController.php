<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Arr;
use Crypt;

use Carbon\Carbon;
use App\Models\Employees;
use App\Models\EmployeePositions;

class EmployeeController extends Controller
{
    protected $employee, $employeePositions;

    public function __construct(Employees $employee, EmployeePositions $employeePositions){
        $this->employee = $employee;
        $this->employeePositions = $employeePositions;
    }

    public function validator(Request $request)
    {
        $input = [
            'first_name' => $this->safeInputs($request->input('first_name')),
            'middle_name' => $this->safeInputs($request->input('middle_name')),
            'last_name' => $this->safeInputs($request->input('last_name')),
            'birthdate' => $this->safeInputs($request->input('birthdate')),
            'gender' => $this->safeInputs($request->input('gender')),
            'contact_number' => $this->safeInputs($request->input('contact_number')),
            'address' => $this->safeInputs($request->input('address')),
            'position' => $this->safeInputs($request->input('position')),
            'status' => $this->safeInputs($request->input('status'))
        ];

        $rules = [
            'first_name' => 'required|min:1|max:255|string',
            'middle_name' => 'required|min:1|max:255|string',
            'last_name' => 'required|min:1|max:255|string',
            'birthdate' => 'required|date',
            'gender' => 'required|numeric',
            'contact_number' => 'required|max:11',
            'address' => 'required|string|max:255',
            'position' => 'required|string',
            'status' => 'required|numeric',
        ];

        $messages = [];

        $customAttributes = [
            'first_name' => 'first name',
            'middle_name' => 'middle name',
            'last_name' => 'last name',
            'birthdate' => 'birthdate',
            'gender' => 'gender',
            'contact_number' => 'contact number',
            'address' => 'address',
            'position' => 'position',
            'status' => 'status',
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
        $name = ['Employees'];
        $mode = [route('employees.index')];
        
        $rows = array();
        $rows = $this->employee->latest()->get();
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
        $columnDefs[] = array_merge(array('headerName'=>'Birthdate','field'=>'birthdate'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Gender','field'=>'gender'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Mobile #','field'=>'contact_number'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Job Position','field'=>'position'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Status','field'=>'status'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'updated_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.employees.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Employees',
            'title' => 'Employees'
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
        $name = ['Employees', 'Create'];
        $mode = [route('employees.index'), route('employees.create')];

        $this->audit_trail_logs('','','Creating new record','');

        $employee_positions = $this->employeePositions->where('status', 1)->get();

        return view('pages.employees.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Employees',
            'title' => 'Employees',
            'employee_positions' => $employee_positions
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
            $this->employee->first_name = Crypt::encryptString($validated['first_name']);
            $this->employee->middle_name = Crypt::encryptString($validated['middle_name']);
            $this->employee->last_name = Crypt::encryptString($validated['last_name']);
            $this->employee->birthdate = Crypt::encryptString($validated['birthdate']);
            $this->employee->gender = $validated['gender'];
            $this->employee->contact_number = Crypt::encryptString($validated['contact_number']);
            $this->employee->position = $validated['position'];
            $this->employee->address = Crypt::encryptString($validated['address']);
            $this->employee->status = $validated['status'];
            $this->employee->created_by = Auth::id();
            // $this->employee->created_at = now();
            $this->employee->save();

            $this->audit_trail_logs('', 'created', 'employees: '.$request->input('first_name'), $this->employee->id);

            return redirect()->route('employees.index')
                ->with('success', 'Employee Added Successfully');
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
        if($data){
            $mode_action = 'update';
            $name = ['Employees', 'Edit', Crypt::decryptString($data->first_name)];
            $mode = [route('employees.index'), route('employees.edit', $id), route('employees.edit', $id)];

            $this->audit_trail_logs('', '', 'employees: '.$data->first_name, $id);

            $employee_positions = $this->employeePositions->where('status', 1)->get();
            $selected_position = $this->employeePositions->where('id', $data->position)->first();

            return view('pages.employees.create', [
                'mode' => $mode_action,
                'breadcrumbs' => $this->breadcrumbs($name, $mode),
                'header' => 'Employees',
                'title' => 'Employees',
                'data' => $data,
                'employee_positions' => $employee_positions,
                'selected_position' => $selected_position
            ]);
        }else{
            return abort(404);
        }
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
            $data = $this->employee->find($id);
            $data->first_name = Crypt::encryptString($validated['first_name']);
            $data->middle_name = Crypt::encryptString($validated['middle_name']);
            $data->last_name = Crypt::encryptString($validated['last_name']);
            $data->birthdate = Crypt::encryptString($validated['birthdate']);
            $data->gender = $validated['gender'];
            $data->contact_number = Crypt::encryptString($validated['contact_number']);
            $data->position = $validated['position'];
            $data->address = Crypt::encryptString($validated['address']);
            $data->status = $validated['status'];
            $data->updated_by = Auth::id();
            $data->save();

            $this->audit_trail_logs('', 'updated', 'employees: '.$data->name, $id);

            return redirect()->route('employees.index')
                ->with('success', 'Employee Updated Successfully');
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
        $this->audit_trail_logs('', 'deleted', 'employees '.$data->name, $id);
        $data->delete();

        return redirect()->route('employees.index')
            ->with('success','Employee Removed Successfully');
    }

    public function changeValue($rows){
        foreach ($rows as $key => $value) {
            if(Arr::exists($value, 'first_name')){
                $first_name = Crypt::decryptString($value->first_name);
                $value->first_name = $first_name;
            }

            if(Arr::exists($value, 'middle_name')){
                $middle_name = Crypt::decryptString($value->middle_name);
                $value->middle_name = $middle_name;
            }

            if(Arr::exists($value, 'last_name')){
                $last_name = Crypt::decryptString($value->last_name);
                $value->last_name = $last_name;
            }

            if(Arr::exists($value, 'email')){
                $email = Crypt::decryptString($value->email);
                $value->email = $email;
            }

            if(Arr::exists($value, 'contact_number')){
                $contact_number = Crypt::decryptString($value->contact_number);
                $value->contact_number = $contact_number;
            }

            if(Arr::exists($value, 'birthdate')){
                $birthdate = Crypt::decryptString($value->birthdate);
                $value->birthdate = $birthdate;
            }

            if(Arr::exists($value, 'gender')){
                if($value->gender == 1){
                    $value->gender = 'Male';
                }else{
                    $value->gender = 'Female';
                }
            }

            if (Arr::exists($value, 'position')) {
                $position = $this->employeePositions->select('name')->where('id', $value->position)->first();
                $value->position = $position->name;
            }
        }

        return $rows;
    }
}
