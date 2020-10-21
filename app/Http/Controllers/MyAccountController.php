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
        $name = ['Home', 'Account Settings', 'Basic Information'];
        $mode = ['/', route('account_settings.index'), route('account_settings.index')];

        $action_mode = 'update';
        $user = $this->user->find(Auth::user()->id);

        $this->audit_trail_logs('','','','');
        
        return view('pages.account_settings.basic_information.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Account Settings',
            'title' => 'Account Settings',
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
            $data->contact_number = Crypt::encryptString($validator['contact_number']);
            $data->address = $validator['address'];
            $data->ip = $this->ipAddress();
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'user_accounts: '.$validator['username'], $id);
            return back()->with('success', 'You have successfully updated your profile');

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

    public function passwordValidator(Request $request){
        $input = [
            'old_password' => $request->input('old_password'),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation')
        ];

        $rules = [
            'old_password' => 'required',
            'password' => 'required|unique:users,password,'.Auth::id().'
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

    public function password(){
        $name = ['Home', 'Account Settings', 'Password'];
        $mode = ['/', route('account_settings.index'), route('account_settings.password')];

        $data = $this->user->find(Auth::user()->id);

        $this->audit_trail_logs('', '', '', '');

        return view('pages.account_settings.password.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Account Settings',
            'title' => 'Account Settings',
            'users' => $data
        ]);
    }

    public function passwordUpdate(Request $request){
        $currentPassword = $request->input('old_password');
        $newPassword = $request->input('password');

        $userId = Auth::id();
        $user = $this->user->findOrFail($userId);

        if (!Hash::check($currentPassword, $user->password)) {
            return back()->with('error', "Current password is incorrect");
        }else{
            if(Hash::check($newPassword, $user->password)){
                return back()->with('warning', "You can't use current password as a new password, please try again.");
            }else if(Hash::check($newPassword, $user->old_password)){
                return back()->with('warning', "You can't use old password as a new password, please try again.");
            }else{
                $validator = $this->passwordValidator($request);
                if ($validator) {
                    $this->user->where('id', $userId)->update([
                        'password' => Hash::make($newPassword),
                        'password_updated_at' => now(),
                        'password_expiration_date' => now()->addDays(30),
                        'old_password' => Hash::make($currentPassword)
                    ]);

                    $this->audit_trail_logs('', 'updated', 'user_accounts: password', $userId);

                    return back()->with('success', 'You have successfully updated your password');
                }
            }
        }
    }

    public function emailValidator(Request $request){
        $input = ['email' => $this->safeInputs($request->input('email'))];

        $rules = ['email' => 'required|string|max:50|email|unique:users,email,'.Auth::user()->id];

        $messages = [];

        $customAttributes = ['email' => 'email'];

        $validator = Validator::make($input, $rules, $messages,$customAttributes);
        return $validator->validate();
    }

    public function email(){
        $name = ['Home', 'Account Settings', 'Email'];
        $mode = ['/', route('account_settings.index'), route('account_settings.email')];

        $data = $this->user->find(Auth::user()->id);

        $this->audit_trail_logs('', '', '', '');

        return view('pages.account_settings.email.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Account Settings',
            'title' => 'Account Settings',
            'users' => $data
        ]);
    }

    public function emailUpdate(Request $request){
        $validator = $this->emailValidator($request);

        if($validator){
            $id = $request->input('id');
            $data = $this->user->findOrFail($id)->update([
                'email' => Crypt::encryptString($validator['email']),
                'email_updated_at' => now(),
                'updated_by' => Auth::id()
            ]);

            $this->audit_trail_logs('', 'updated', 'user_accounts: '.$validator['email'], $id);
            return back()->with('success', 'You have successfully updated your email');

        }else{
            return back()->withErrors($validator)->withInput();
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
            $userId = Auth::id();

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

            return back()->with('success', 'You have successfully updated your profile picture');

        }else{
            return back()->with('error', "Something wrong with the image, please try again..");
        }
    }
}
