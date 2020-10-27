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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use App\Models\User;
use App\Models\ProductCategories;
use App\Models\MenuCategories;
use App\Models\MenuTypes;
use App\Models\ExpensesCategories;
use App\Models\EmployeePositions;
use App\Models\ProductUnits;
use App\Models\Products;

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

            if(Arr::exists($value, 'created_by')){
                $users = User::select('username')->where('id', $value->created_by)->first();
                $value->created_by = @$users->username;
            }

            if(Arr::exists($value, 'updated_by')){
                $users = User::select('username')->where('id', $value->updated_by)->first();
                $value->updated_by = @$users->username;
            }
        }

        return $array;
    }
}
