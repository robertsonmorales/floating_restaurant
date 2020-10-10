<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditTrailLogs;

class AuditTrailLogController extends Controller
{
    public function __construct(AuditTrailLogs $logs){
        $this->log = $logs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = ['Audit Trail Logs'];
        $mode = [route('audit_trail_logs.index')];
        
        $rows = array();
        $rows = $this->log->latest()->get();
            
        $arr_set = array(
            'editable'=>false,
            'resizable'=>true,
            'filter'=>true,
            'sortable'=>true,
            'floatingFilter'=>true,
            'flex'=>1
        );

        $columnDefs = array();
        $columnDefs[] = array_merge(array('headerName'=>'Module','field'=>'module'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Username','field'=>'username'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Action Taken','field'=>'action_taken'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Remarks','field'=>'remarks'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'IP Address','field'=>'ip'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created at','field'=>'created_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');
        
        return view('pages.audit_trail_logs.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'header' => 'Audit Trail Logs',
            'title' => 'Audit Trail Logs'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
