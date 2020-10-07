<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;

use Carbon\Carbon;
use App\Models\CancellationReason;

class CancellationReasonController extends Controller
{
    protected $cancellationReason;
    public function __construct(CancellationReason $cancellationReason){
        $this->cancellation = $cancellationReason;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'description' => $this->safeInputs($request->input('description')),
        ];

        $rules = [
            'name' => 'required|string|max:100|unique:cancellation_reasons,name,'.$this->safeInputs($request->input('id')).'',            
            'description' => 'required|max:255'
        ];

        $messages = [];

        $customAttributes = [
            'name' => 'name',
            'description' => 'description'
        ];                

        $validator = Validator::make($input, $rules, $messages, $customAttributes);
        return $validator->validate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = ['Cancellation Reasons'];
        $mode = [route('cancellation_reasons.index')];
        
        $rows = array();
        $rows = $this->cancellation->latest()->get();
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
        $columnDefs[] = array_merge(array('headerName'=>'Description','field'=>'description'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'updated_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'updated_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.cancellation_reasons.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Cancellation Reasons',
            'title' => 'Cancellation Reasons'
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
        $name = ['Cancellation Reasons', 'Create'];
        $mode = [route('cancellation_reasons.index'), route('cancellation_reasons.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.cancellation_reasons.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Cancellation Reasons',
            'title' => 'Cancellation Reasons'
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
            $this->cancellation->name = $validated['name'];
            $this->cancellation->description = $validated['description'];
            $this->cancellation->created_by = Auth::user()->id;
            $this->cancellation->created_at = now();
            $this->cancellation->save();

            $this->audit_trail_logs('', 'created', 'cancellation_reasons: '.$validated['name'], $this->cancellation->id);

            return redirect()->route('cancellation_reasons.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->cancellation->findOrFail($id);
        $mode_action = 'update';
        $name = ['Cancellation Reasons', 'Edit', $data->name];
        $mode = [route('cancellation_reasons.index'), route('cancellation_reasons.edit', $id), route('cancellation_reasons.edit', $id)];

        $this->audit_trail_logs('', '', 'cancellation_reasons: '.$data->name, $id);

        return view('pages.cancellation_reasons.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Cancellation Reasons',
            'title' => 'Cancellation Reasons',
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
            $data = $this->cancellation->findOrFail($id);
            $data->name = $validated['name'];
            $data->description = $validated['description'];
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'cancellation_reasons: '.$data->name, $id);

            return redirect()->route('cancellation_reasons.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->cancellation->findOrFail($id);
        $this->audit_trail_logs('', 'deleted', 'cancellation_reasons '.$data->name, $id);
        $data->delete();

        return redirect()->route('cancellation_reasons.index')->with('success', 'You have successfully removed '.$data->name);
    }
}
