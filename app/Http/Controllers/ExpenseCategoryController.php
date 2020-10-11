<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\ExpensesCategories;

class ExpenseCategoryController extends Controller
{
    protected $category;
    public function __construct(ExpensesCategories $category){
        $this->category = $category;
    }

    public function validator(Request $request)
    {
        $input = [
            'name' => $this->safeInputs($request->input('name')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'name' => 'required|string|max:255|unique:expenses_categories,name,'.$this->safeInputs($request->input('id')).'',
            'status' => 'required|numeric'
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
        $name = ['Expense Categories'];
        $mode = [route('expense_categories.index')];
        
        $rows = array();
        $rows = $this->category->latest()->get();
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

        return view('pages.expense_categories.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Expense Categories',
            'title' => 'Expense Categories'
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
        $name = ['Expense Categories', 'Create'];
        $mode = [route('expense_categories.index'), route('expense_categories.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.expense_categories.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Expense Categories',
            'title' => 'Expense Categories'
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
            $this->category->name = $validated['name'];
            $this->category->status = $validated['status'];
            $this->category->created_by = Auth::id();
            $this->category->created_at = Carbon::now();
            $this->category->save();

            $this->audit_trail_logs('', 'created', 'expense_categories: '.$validated['name'], $this->category->id);

            return redirect()->route('expense_categories.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->category->findOrFail($id);
        $mode_action = 'update';
        $name = ['Expense Categories', 'Edit', $data->name];
        $mode = [route('expense_categories.index'), route('expense_categories.edit', $id), route('expense_categories.edit', $id)];

        $this->audit_trail_logs('', '', 'expense_categories: '.$data->name, $id);

        return view('pages.expense_categories.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Expense Categories',
            'title' => 'Expense Categories',
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
            $data = $this->category->find($id);
            $data->name = $validated['name'];
            $data->status = $validated['status'];
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'expense_categories: '.$data->name, $id);

            return redirect()->route('expense_categories.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->category->findOrFail($id);
        $data->delete();
        $this->audit_trail_logs('', 'deleted', 'expense_categories '.$data->name, $id);

        return redirect()->route('expense_categories.index')->with('success', 'You have successfully removed '.$data->name);
    }
}
