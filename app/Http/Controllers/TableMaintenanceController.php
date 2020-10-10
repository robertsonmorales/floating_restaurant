<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\TableManagement;

class TableMaintenanceController extends Controller
{
    protected $table;
    public function __construct(TableManagement $table){
        $this->table = $table;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name'))
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:table_management,name,'.$this->safeInputs($request->input('id')).''
        ];

        $messages = [];

        $customAttributes = [
            'name' => 'name'
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
        $name = ['Table Management'];
        $mode = [route('table_maintenance.index')];
        
        $rows = array();
        $rows = $this->table->latest()->get();
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
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'updated_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'updated_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.table_maintenance.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Table Maintenance',
            'title' => 'Table Maintenance'
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
        $name = ['Table Maintenance', 'Create'];
        $mode = [route('table_maintenance.index'), route('table_maintenance.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.table_maintenance.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Table Maintenance',
            'title' => 'Table Maintenance'
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
            $this->table->name = $validated['name'];
            $this->table->created_by = Auth::user()->id;
            $this->table->created_at = Carbon::now();
            $this->table->save();

            $this->audit_trail_logs('', 'created', 'table_maintenance: '.$validated['name'], $this->table->id);

            return redirect()->route('table_maintenance.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->table->findOrFail($id);
        $mode_action = 'update';
        $name = ['Table Management', 'Edit', $data->name];
        $mode = [route('table_maintenance.index'), route('table_maintenance.edit', $id), route('table_maintenance.edit', $id)];

        $this->audit_trail_logs('', '', 'table_maintenance: '.$data->name, $id);

        return view('pages.table_maintenance.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Table Maintenance',
            'title' => 'Table Maintenance',
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
            $data = $this->table->find($id);

            $data->name = $validated['name'];
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'table_management: '.$data->name, $id);

            return redirect()->route('table_maintenance.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->table->findOrFail($id);        
        $data->delete();
        $this->audit_trail_logs('', 'deleted', 'table_management '.$data->name, $id);

        return redirect()->route('table_maintenance.index')->with('success', 'You have successfully removed '.$data->name);
    }
}
