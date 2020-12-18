<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Arr;

use Carbon\Carbon;
use App\Models\Expenses;
use App\Models\ExpensesCategories;

class ExpenseController extends Controller
{
    protected $expenses, $categories;

    public function __construct(Expenses $expenses, ExpensesCategories $categories){
        $this->expenses = $expenses;
        $this->expenseCategory = $categories;
    }

    public function validator(Request $request)
    {
        $input = [
            'expense_category' => $this->safeInputs($request->input('expense_category')),
            'name' => $this->safeInputs($request->input('name')),
            'cost' => $this->safeInputs($request->input('cost')),
        ];

        $rules = [            
            'expense_category' => 'required|string|max:50',
            'name' => 'required|string|max:255|unique:expenses,name,'.$this->safeInputs($request->input('id')),
            'cost' => 'required|string|max:50',
        ];

        $messages = [];

        $customAttributes = [
            'expense_category' => 'expense category',
            'name' => 'name',
            'cost' => 'cost',
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
        $name = ['Expenses'];
        $mode = [route('expenses.index')];        
        
        $rows = array();
        $rows = $this->expenses->latest()->get();
        $row = $this->changeVal($rows);
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
        $columnDefs[] = array_merge(array('headerName'=>'Expense Category','field'=>'expense_categories_id'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Name','field'=>'name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Cost','field'=>'cost'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'created_at'), $arr_set);

        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.expenses.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Expenses',
            'title' => 'Expenses'
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
        $name = ['Expenses', 'Create'];
        $mode = [route('expenses.index'), route('expenses.create')];

        $expenseCategories = $this->expenseCategory->where('status', 1)->get();

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.expenses.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Expenses',
            'title' => 'Expenses',
            'expense_categories' => $expenseCategories
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
            $data = $this->expenses->insert([
                'expense_categories_id' => $validated['expense_category'],
                'name' => $validated['name'],
                'cost' => $validated['cost'],
                'created_by' => Auth::id(),
                'created_at' => Carbon::now()
            ]);            

            $this->audit_trail_logs('', 'created', 'expenses: '.$validated['name'], $this->safeInputs($request->input('id')));

            return redirect()->route('expenses.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->expenses->findOrFail($id);
        
        $mode_action = 'update';
        $name = ['Expenses', 'Edit', $data->name];
        $mode = [route('expenses.index'), route('expenses.edit', $id), route('expenses.edit', $id)];

        $expenseCategories = $this->expenseCategory->where('status', 1)->get();
        $this->audit_trail_logs('', '', 'expenses: '.$data->name, $id);
        
        return view('pages.expenses.create', [
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Expenses',
            'title' => 'Expenses',
            'data' => $data,
            'expense_categories' => $expenseCategories,
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
            $this->expenses->findOrFail($id)->update([
                'expense_categories_id' => $validated['expense_category'],
                'name' => $validated['name'],
                'cost' => $validated['cost'],
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);         

            $this->audit_trail_logs('', 'updated', 'expenses: '.$validated['name'], $id);

            return redirect()->route('expenses.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->expenses->findOrFail($id);
        $data->delete();
        $this->audit_trail_logs('', 'deleted', 'expenses '.$data->name, $id);
        
        return redirect()->route('expenses.index')
            ->with('success', 'You have successfully removed '.$data->name);
    }

    public function changeValue($rows)
    {       
        foreach ($rows as $value) {
            if (Arr::exists($value, 'status')) {
                if ($value->status == 1) {
                    $value->status = "Active";
                }else{
                    $value->status = "In-active";
                }
            }

            if (Arr::exists($value, 'expense_categories_id')) {
                $expenseCategory = $this->expenseCategory->find($value->expense_categories_id);
                $value->expense_categories_id = $expenseCategory->name;
            }
        }

        return $rows;
    }
}
