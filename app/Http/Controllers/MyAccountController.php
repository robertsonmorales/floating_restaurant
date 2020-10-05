<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use DB;
use Crypt;
use Hash;
use Validator;

use Carbon\Carbon;
use App\Models\User;

class MyAccountController extends Controller
{
    protected $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function validator(Request $request){
        $input = [
            'first_name' => $this->safeInputs($request->input('first_name')),
            'last_name' => $this->safeInputs($request->input('last_name')),
            'email' => $this->safeInputs($request->input('email')),
            'username' => $this->safeInputs($request->input('username')),
            'contact_number' => $this->safeInputs($request->input('contact_number')),
            'address' => $this->safeInputs($request->input('address'))
        ];

        $rules = [
            'first_name' => 'required|string|max:55',
            'last_name' => 'required|string|max:55',
            'email' => 'nullable|string|max:50|email|unique:users,email,'.Auth::user()->id,
            'username' => 'required|string|max:30|unique:users,username,'.Auth::user()->id,
            'contact_number' => 'required|numeric|digits:11',
            'address' => 'required|string|max:255'
        ];

        $messages = [];

        $customAttributes = [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'email' => 'email',
            'username' => 'username',
            'contact_number' => 'contact number',
            'address' => 'address'
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
        $name = ['Home', Auth::user()->username, 'Personal Information'];
        $mode = ['/', route('my_account.index'), route('my_account.index')];

        $action_mode = 'update';
        $user = $this->user->find(Auth::user()->id);

        $this->audit_trail_logs('','','','');
        
        return view('pages.my_account.general.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'My Account',
            'title' => 'My Account',
            'users' => $user,
            'mode' => $action_mode
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
        $data = $this->user->findOrFail($id);
        $validator = $this->validator($request);

        if($validator){
            $data->first_name = Crypt::encryptString($validator['first_name']);
            $data->last_name = Crypt::encryptString($validator['last_name']);
            $data->username = $validator['username'];
            $data->email = Crypt::encryptString($validator['email']);
            $data->contact_number = Crypt::encryptString($validator['contact_number']);
            $data->address = $validator['address'];
            $data->ip = $this->ipAddress();
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'user_accounts: '.$validator['username'], $id);

            return back()->with('success', 'Your account is updated Successfully');
        }else{
            return back()->withErrors($validator)->withInput();
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
        //
    }

    public function changingPassword(){
        $name = ['Home', Auth::user()->username, 'Change Password'];
        $mode = ['/', route('my_account.index'), route('my_account.change_password')];

        $data = $this->user->find(Auth::user()->id);

        $this->audit_trail_logs('', '', '', '');

        return view('pages.my_account.change_password.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'My Account',
            'title' => 'My Account',
            'users' => $data
        ]);
    }

    public function passwordValidator(Request $request){
        $input = [
            'old_password' => $request->input('old_password'),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation')
        ];

        $rules = [
            'old_password' => 'required',
            'password' => 'required|unique:users,password,'.Auth::user()->id.'
                |confirmed|min:8|max:30|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
            'password_confirmation' => 'required|min:8|max:30'
        ];

        $messages = [
            'password.regex' => 'Your password must be more than 8 characters long, should contain at-least <b>one uppercase, one lowercase, one numeric and one special character.'
        ];

        $customAttributes = [
            'old_password' => 'old password',
            'password' => 'password',
            'password_confirmation' => 'password confirmation'
        ];

        $validator = Validator::make($input, $rules, $messages,$customAttributes);
        return $validator->validate();
    }

    public function updatePassword(Request $request){        
        $user_id = Auth::user()->id;
        $user = $this->user->findOrFail($user_id);
        $current_password = $this->safeInputs($request->input('old_password'));
        $new_password = $this->safeInputs($request->input('password'));

        if (!Hash::check($current_password, $user->password)) {
            return back()->with('incorrect', "Old password is incorrect");
        }else{
            if(Hash::check($new_password, $user->password)){
                return back()->with('match', "You can't user old password as a new password, please try again.");
            }else{
                $validator = $this->passwordValidator($request);
                if ($validator) {
                    $old_password = [];
                    $user->password = Hash::make($new_password);
                    $user->password_updated_at = now();
                    $user->password_expiration_date = now()->addDays(30);
                    $user->old_password = Hash::make($current_password);
                    $user->save();

                    $this->audit_trail_logs('', 'updated', 'user_accounts: password', $user_id);

                    return back()->with('success', 'Password Updated Successfully');
                }
            }
        }
    }

    // public function imageValidator(Request $request){
    //     $input = [
    //         'profile_image' => $request->file('profile_image')
    //     ];

    //     $rules = [
    //         'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif'
    //     ];

    //     $messages = [];

    //     $customAttributes = [
    //         'profile_image' => 'profile image'
    //     ];

    //     $validator = Validator::make($input, $rules, $messages, $customAttributes);
    //     return $validator->validate();
    // }

    public function changeProfile(Request $request){
        $image = $request->file('profile-image');
        if ($request->file('profile-image')->isValid()) {
            $username = Auth::user()->username;
            $userId = Auth::user()->id;

            $publicFolder = public_path('images/user_profiles/'.$username.$userId);

            if (!file_exists($publicFolder)) {
                mkdir($publicFolder);
            }

            $data = $this->user->findOrFail($userId);
            $profileImage = $image->getClientOriginalName(); // returns original name
            $extension = $image->getclientoriginalextension(); // returns the file extension
            $newProfileImage = strtoupper(Str::random(20)).'.'.$extension;
            $image->move($publicFolder, $newProfileImage);

            $data->profile_image = $newProfileImage;
            $data->profile_image_updated_at = now();
            $data->profile_image_expiration_date = now()->addDays(15);
            $data->save();

            return back()->with('success', 'Profile Changed Successfully');
        }else{
            return 'not okie';
        }
    }
}
