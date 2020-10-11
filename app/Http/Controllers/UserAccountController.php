<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Arr;
use Auth;
use DB;
use Crypt;
use Hash;
use Validator;

use App\Models\User;
use Carbon\Carbon;

class UserAccountController extends Controller
{
    protected $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function validator(Request $request)
    {
        $input = [
            'first_name' => $this->safeInputs($request->input('first_name')),
            'last_name' => $this->safeInputs($request->input('last_name')),
            'email' => $this->safeInputs($request->input('email')),
            'username' => $this->safeInputs($request->input('username')),
            'password' => $request->input('password'),
            'contact_number' => $this->safeInputs($request->input('contact_number')),
            'address' => $this->safeInputs($request->input('address')),
            'status' => $this->safeInputs($request->input('status')),
            'user_role' => $this->safeInputs($request->input('user_role'))
        ];

        $rules = [
            'first_name' => 'required|string|max:55',
            'last_name' => 'required|string|max:55',
            'email' => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|max:30|unique:users,username',
            'password' => 'required|min:8
                |regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/|unique:users,password',
            'contact_number' => 'required|numeric|digits:11',
            'address' => 'required|string',
            'status' => 'required',
            'user_role' => 'required'
        ];

        $messages = [
            'password.regex' => 'Your password must be more than 8 characters long, should contain at-least one Uppercase, one Lowercase, one Numeric and one special character.'
        ];

        $customAttributes = [
            'first_name' => 'first name',
            'last name' => 'last name',
            'email' => 'email',
            'username' => 'username', 
            'password' => 'password',
            'contact_number' => 'contact number',
            'address' => 'address',
            'status' => 'status',
            'user_role' => 'user role'
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
        $name = ['User Accounts'];
        $mode = [route('user_accounts.index')];
        
        $rows = array();
        $rows = $this->user->where('id', '!=', Auth::id())->latest()->get();
        $rows = $this->changeVal($rows);
        $rows = $this->changeValue($rows);
            
        $arr_set = array(
            'editable'=>false,
            'resizable'=>true,
            'filter'=>true,
            'sortable'=>true,
            'floatingFilter'=>true,
            'flex'=>1,
        );

        $columnDefs = array();
        $columnDefs[] = array_merge(array('headerName'=>'Name','field'=>'name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Username','field'=>'username'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Email','field'=>'email'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Contact No.','field'=>'contact_number'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Status','field'=>'status'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Role','field'=>'user_role'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.user_accounts.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'User Accounts',
            'title' => 'User Accounts'
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
        $name = ['User Accounts', 'Create'];
        $mode = [route('user_accounts.index'), route('user_accounts.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.user_accounts.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'User Accounts',
            'title' => 'User Accounts'
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
            $password = Hash::make($validated['password']);
            $first_name = Crypt::encryptString($validated['first_name']);
            $last_name = Crypt::encryptString($validated['last_name']);
            $email = Crypt::encryptString($validated['email']);
            $contact_number = Crypt::encryptString($validated['contact_number']);

            $this->user->first_name = $first_name;
            $this->user->last_name = $last_name;
            $this->user->username = $validated['username'];
            $this->user->email = $email;
            $this->user->password = $password;
            $this->user->contact_number = $contact_number;
            $this->user->address = $validated['address'];
            $this->user->status = $validated['status'];
            $this->user->user_role = $validated['user_role'];
            $this->user->created_by = Auth::id();
            $this->user->created_at = now();
            $this->user->save();

            $this->audit_trail_logs('', 'created', 'user_accounts: '.$validated['username'], $this->user->id);

            return redirect()->route('user_accounts.index')->with('success', 'You have successfully added '.$this->user->username);
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
        $data = $this->user->findOrFail($id);
        $mode_action = 'update';
        $name = ['User Accounts', 'Edit', $data->username];
        $mode = [route('user_accounts.index'), route('user_accounts.edit', $id), route('user_accounts.edit', $id)];

        $this->audit_trail_logs('', '', 'user_accounts: '.$data->username, $id);

        return view('pages.user_accounts.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'User Accounts',
            'title' => 'User Accounts',
            'user' => $data
        ]);
    }

    public function updateValidator(Request $request){
        $input = [
            'first_name' => $this->safeInputs($request->input('first_name')),
            'last_name' => $this->safeInputs($request->input('last_name')),
            'address' => $this->safeInputs($request->input('address')),
            'status' => $this->safeInputs($request->input('status')),
            'user_role' => $this->safeInputs($request->input('user_role'))
        ];

        $rules = [
            'first_name' => 'required|string|max:55',
            'last_name' => 'required|string|max:55',
            'address' => 'required|string',
            'status' => 'required',
            'user_role' => 'required'
        ];

        $messages = [];

        $customAttributes = [
            'first_name' => 'first name',
            'last name' => 'last name',
            'address' => 'address',
            'status' => 'status',
            'user_role' => 'user role'
        ];

        $validator = Validator::make($input, $rules, $messages,$customAttributes);
        return $validator->validate();
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
        $validated = $this->updateValidator($request);
        if ($validated) {
            $data = $this->user->find($id);
            $data->first_name = Crypt::encryptString($validated['first_name']);
            $data->last_name = Crypt::encryptString($validated['last_name']);
            $data->address = $validated['address'];
            $data->status = $validated['status'];
            $data->user_role = $validated['user_role'];
            $data->updated_by = Auth::id();
            $data->save();

            $this->audit_trail_logs('', 'updated', 'user_accounts: '.$data->username, $id);

            return redirect()->route('user_accounts.index')->with('success', 'You have successfully updated '.$data->username);
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
        $data = $this->user->findOrFail($id);
        $data->deleted_by = Auth::id();
        $save = $data->save();
        if ($save) {
            $data->delete();
        }

        $this->audit_trail_logs('', 'deleted', 'user_account '.$data->username, $id);

        return redirect()->route('user_accounts.index')->with('success', 'You have successfully removed '.$data->username);
    }

    public function changeValue($rows){
        foreach ($rows as $key => $value) {
            if(Arr::exists($value, 'first_name')){
                $first_name = Crypt::decryptString($value->first_name);
                $value->first_name = $first_name;
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

            // 1 = admin, 2 = cashier, 3 = manager, 4 = cook
            if (Arr::exists($value, 'user_role')) {
                if ($value->user_role == 1) {
                    $value->user_role = "Admin";
                }elseif ($value->user_role == 2) {
                    $value->user_role = "Cashier";
                }elseif ($value->user_role == 3) {
                    $value->user_role = "Manager";
                }elseif ($value->user_role == 4) {
                    $value->user_role = "Cook";
                }
            }
        }

        return $rows;
    }
}
