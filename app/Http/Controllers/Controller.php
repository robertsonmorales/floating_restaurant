<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

use App\Models\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function ipAddress(){
        $_IP = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $_IP = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $_IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $_IP = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $_IP = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $_IP = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $_IP = $_SERVER['REMOTE_ADDR'];
        else
            $_IP = 'UNKNOWN';
        return $_IP;
    }

    public function audit_trail_logs($module, $action_taken, $remarks, $dataid){
        $user = Auth::user();
        $username = Auth::check() ? $user->username : '';

        $currentPath= Route::getFacadeRoot()->current()->uri();
        $currentPath = explode('/', $currentPath);
        $module = ($module == '') ? strtoupper($currentPath[0]) : strtoupper($module);

        $mode = strtoupper((array_key_exists(2, $currentPath)) ? $currentPath[2] : '');
        $data = ($dataid == '') ? '' : ' ID: '.$dataid;
        $action_taken = ($mode == 'EDIT' || $mode == 'DELETE')
            ? $mode.' ID: '.$dataid
            : (($action_taken == '') 
                ? 'VIEWING '.$module
                : strtoupper($action_taken.$data));

        DB::table('audit_trail_logs')->insert([
            'module' => $module,
            'username' => $username,
            'action_taken' => $action_taken,
            'remarks' => strtoupper($remarks),
            'ip' => $this->ipAddress(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
    
    public function breadcrumbs($name, $mode){
        return $breadcrumb = array(
            'name' => $name,
            'mode' => $mode
        );
    }

    public function safeInputs($input){
        $inputs = strip_tags(htmlspecialchars(trim($input)));
        return $inputs;
    }

    public function changeVal($rows){
        $array = $rows;
        foreach ($array as $key => $value) {
            if(Arr::exists($value, 'status')){
                 if($value->status == 1){
                    $value->status = 'Active';
                 }else{
                    $value->status = 'In-active';
                 }
            }

            if(Arr::exists($value, 'verification')){
                 if($value->verification == 1){
                    $value->verification = 'Required';
                 }else{
                    $value->verification = 'Not Required';
                 }
            }

            if(Arr::exists($value, 'created_by')){
                if ($value->created_by == null) {
                    $value->created_by = 'NULL';
                }else{
                    $users = User::select('username')->where('id', $value->created_by)->first();
                    $value->created_by = $users->username;
                }
            }

            if(Arr::exists($value, 'updated_by')){
                if ($value->updated_by == null) {
                    $value->updated_by = 'NULL';
                }else{
                    $users = User::select('username')->where('id', $value->updated_by)->first();
                    $value->updated_by = $users->username;
                }
            }

            if (Arr::exists($value, 'product_categories_id')) {
                $menu_types = ProductCategories::select('name')->where('id', $value->product_categories_id)->first();
                $value->product_categories_id = $menu_types->name;
            }

            if (Arr::exists($value, 'price')) {
                $value->price = "Php ".$value->price.".00";
            }

            if(Arr::exists($value, 'inventoriable')){
                if($value->inventoriable == 1){
                    $value->inventoriable = 'Yes';
                 }else{
                    $value->inventoriable = 'No';
                 }
            }

            if(Arr::exists($value, 'menu_categories_id')){
                $menu_categories = MenuCategories::find($value->menu_categories_id);
                $value->menu_categories_id = $menu_categories->name;
            }

            if(Arr::exists($value, 'menu_type_id')){
                $menu_type = MenuTypes::find($value->menu_type_id);
                $value->menu_type_id = $menu_type->name;   
            }

            if(Arr::exists($value, 'percentage')){
                $value->percentage = $value->percentage.'%';
            }

            if (Arr::exists($value, 'expense_categories_id')) {
                $expense_categories = ExpensesCategories::find($value->expense_categories_id);
                $value->expense_categories_id = $expense_categories->name;
            }

            if (Arr::exists($value, 'cost')) {
                $value->cost = "Php ".$value->cost.".00";
            }

            if(Arr::exists($value, 'first_name')){
                $first_name = Crypt::decryptString($value->first_name);
                $value->first_name = $first_name;
            }

            if(Arr::exists($value, 'last_name')){
                $last_name = Crypt::decryptString($value->last_name);
                $value->last_name = $last_name;
            }

            if(Arr::exists($value, 'middle_name')){
                $middle_name = Crypt::decryptString($value->middle_name);
                $value->middle_name = $middle_name;
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

            if(Arr::exists($value, 'contact_number')){
                $contact_number = Crypt::decryptString($value->contact_number);
                $value->contact_number = $contact_number;
            }

            if(Arr::exists($value, 'email')){
                $email = Crypt::decryptString($value->email);
                $value->email = $email;
            }

            if(Arr::exists($value, 'user_type')){
                $user_types = ['Admin', 'Cashier', 'Manager', 'Cook'];

                if ($value->user_type == 1) {
                    $value->user_type = $user_types[0];
                }else if($value->user_type == 2){
                    $value->user_type = $user_types[1];
                }else if($value->user_type == 3){
                    $value->user_type = $user_types[2];
                }else if($value->user_type == 4){
                    $value->user_type = $user_types[3];
                }
            }

            if (Arr::exists($value, 'position')) {
                $position = EmployeePositions::select('name')->where('id', $value->position)->first();
                $value->position = $position->name;
            }

            if (Arr::exists($value, 'type')) {
                if ($value->type == 1) {
                    $value->type = 'Deliveries';
                }elseif ($value->type == 2) {
                    $value->type = "Damages";
                }elseif ($value->type == 3) {
                    $value->type = "Sold out";
                }
            }

            if (Arr::exists($value, 'stocks')) {
                $unit = ProductUnits::find($value->unit);
                $value->stocks = $value->stocks.' '.$unit->name;
            }

            if (Arr::exists($value, 'deliveries')) {
                $unit = ProductUnits::find($value->unit);
                $value->deliveries = $value->deliveries.' '.$unit->name;
            }

            if (Arr::exists($value, 'damages')) {
                $unit = ProductUnits::find($value->unit);
                $value->damages = $value->damages.' '.$unit->name;
            }

            if (Arr::exists($value, 'sold_out')) {
                $unit = ProductUnits::find($value->unit);
                $value->sold_out = $value->sold_out.' '.$unit->name;
            }

            if (Arr::exists($value, 'qty')) {
                $unit = ProductUnits::find($value->unit);
                $value->qty = $value->qty.' '.$unit->name;
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

        return $array;
    }
}
