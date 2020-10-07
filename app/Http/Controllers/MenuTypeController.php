<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\MenuTypes;

class MenuTypeController extends Controller
{
    protected $menuTypes;
    public function __construct(MenuTypes $menuTypes){
        $this->menuType = $menuTypes;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:menu_types,name,'.$this->safeInputs($request->input('id')).'',            
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
        $name = ['Menu Types'];
        $mode = [route('menu_types.index')];
        
        $rows = array();
        $rows = $this->menuType->latest()->get();
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

        return view('pages.menu_types.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Menu Types',
            'title' => 'Menu Types'
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
        $name = ['Menu Types', 'Create'];
        $mode = [route('menu_types.index'), route('menu_types.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.menu_types.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Menu Types',
            'title' => 'Menu Types'
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
            $this->menuType->name = $validated['name'];
            $this->menuType->status = $validated['status'];
            $this->menuType->created_by = Auth::id();
            $this->menuType->created_at = Carbon::now();
            $this->menuType->save();

            $this->audit_trail_logs('', 'created', 'menu_types: '.$validated['name'], $this->menuType->id);

            return redirect()->route('menu_types.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->menuType->findOrFail($id);
        $mode_action = 'update';
        $name = ['Menu Types', 'Edit', $data->name];
        $mode = [route('menu_types.index'), route('menu_types.edit', $id), route('menu_types.edit', $id)];

        $this->audit_trail_logs('', '', 'menu_types: '.$data->name, $id);

        return view('pages.menu_types.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Menu Types',
            'title' => 'Menu Types',
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
        if($validated){
            $data = $this->menuType->findOrFail($id);
            $data->name = $validated['name'];
            $data->status = $validated['status'];
            $data->updated_by = Auth::id();
            $data->save();

            $this->audit_trail_logs('', 'updated', 'menu_types: '.$data->name, $id);

            return redirect()->route('menu_types.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->menuType->findOrFail($id);
        $this->audit_trail_logs('', 'deleted', 'menu_types '.$data->name, $id);
        $data->delete();
        return redirect()->route('menu_types.index')->with('success', 'You have successfully removed '.$data->name);
    }
}
