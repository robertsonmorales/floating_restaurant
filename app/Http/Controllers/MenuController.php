<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Carbon\Carbon;
use Validator;

use App\Models\Menu;
use App\Models\MenuCategories;
use App\Models\MenuTypes;
use App\Models\Products;

class MenuController extends Controller
{
    public function validator(Request $request)
    {
        $input = [
            'menu_category' => $this->safeInputs($request->input('menu_category')),
            'menu_type' => $this->safeInputs($request->input('menu_type')),
            'name' => $this->safeInputs($request->input('name')),
            'price' => $this->safeInputs($request->input('price')),
            'status' => $this->safeInputs($request->input('status')),

        ];

        $rules = [
            'menu_category' => 'required',
            'menu_type' => 'required',
            'name' => 'required|string|max:255|unique:menus,name,'.$this->safeInputs($request->input('id')).'',
            'price' => 'required|numeric',
            'status' => 'required'
        ];

        $messages = [];

        $customAttributes = [
            'menu_category' => 'menu category',
            'menu_type' => 'menu type',
            'name' => 'name',
            'price' => 'price',
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
        $name = ['Menus'];
        $mode = [route('menus.index')];        
        
        $rows = array();
        $rows = Menu::orderBy('created_at', 'desc')
            ->get();
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
        $columnDefs[] = array_merge(array('headerName'=>'Menu Category','field'=>'menu_categories_id'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Price','field'=>'price'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Menu Type','field'=>'menu_type_id'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Status','field'=>'status'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.menus.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Menus',
            'title' => 'Menus'
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
        $name = ['Menus', 'Create'];
        $mode = [route('menus.index'), route('menus.create')];

        $this->audit_trail_logs('','','Creating new record','');

        $menu_category = MenuCategories::all();
        $menu_type = MenuTypes::all();
        $products = Products::where('inventoriable', 1)->get();

        return view('pages.menus.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Menus',
            'title' => 'Menus',
            'products' => $products,
            'menu_category' => $menu_category,
            'menu_type' => $menu_type
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
        if ($validated) {
            $recipe = ($request->input('recipe')) ? array_filter($request->input('recipe')) : null;
            $recipe_qty = ($request->input('recipe_qty')) ? array_filter($request->input('recipe_qty')) : null;
            $recipes = [];

            $counter = ($recipe == null) ? 0 : count($recipe);
            for ($i=0; $i < $counter; $i++) {
                $recipes[] = [
                    'product' => $recipe[$i],
                    'stock_out' => $recipe_qty[$i] 
                ];
            }    

            $json_recipe = ($recipes == []) ? null : json_encode($recipes, true);

            $data = new Menu;
            $data->menu_categories_id = $validated['menu_category'];
            $data->menu_type_id = $validated['menu_type'];
            $data->name = $validated['name'];
            $data->price = $validated['price'];
            $data->recipes = $json_recipe;
            $data->status = $validated['status'];
            $data->created_by = Auth::user()->id;
            $data->created_at = Carbon::now();
            $data->save();

            $this->audit_trail_logs('', 'created', 'menus: '.$validated['name'], $data->id);

            return redirect()->route('menus.index')
                ->with('success', 'Menu Added Successfully');                
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
