<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\MenuCategories;
use App\Models\MenuTypes;
use App\Models\Products;
use App\Models\MenuRecipe;

class MenuController extends Controller
{
    protected $menu, $menuCategory, $menuType, $product, $menuRecipe;
    public function __construct(Menu $menu, MenuCategories $menuCategory, MenuTypes $menuType, Products $product, MenuRecipe $menuRecipe){
        $this->menu = $menu;
        $this->category = $menuCategory;
        $this->type = $menuType;
        $this->product = $product;
        $this->recipe = $menuRecipe;
    }

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
        $rows = $this->menu->get();
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
        $columnDefs[] = array_merge(array('headerName'=>'Category','field'=>'menu_categories_id'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Price','field'=>'price'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Type','field'=>'menu_type_id'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Status','field'=>'status'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'created_at'), $arr_set);
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

        $menuCategory = $this->category->all();
        $menuType = $this->type->all();
        $products = $this->product->where('inventoriable', 1)->get();

        return view('pages.menus.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Menus',
            'title' => 'Menus',
            'products' => $products,
            'menu_category' => $menuCategory,
            'menu_type' => $menuType
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
            $insert = $this->menu->insert([
               'menu_categories_id' => $validated['menu_category'],
               'menu_type_id' => $validated['menu_type'],
               'name' => $validated['name'],
               'price' => $validated['price'],
               'status' => $validated['status'],
               'created_by' => Auth::id(),
               'created_at' => now()
            ]);

            if ($insert) {
                for ($i=0; $i < count($request->input('recipe')); $i++) { 
                    $stock_out = $request->input('recipe_qty');
                    $recipes = $request->input('recipe');
                    $product = explode('|', $recipes[$i]);

                    $menu = $this->menu->latest()->first();

                    $this->recipe->insert([
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'product_id' => $product[0],
                        'product_name' => $product[1],
                        'stock_out' => $stock_out[$i],
                        'created_by' => Auth::id(),
                        'created_at' => now()
                    ]);
                }
            }

            $this->audit_trail_logs('', 'created', 'menus: '.$validated['name'], $this->menu->id);

            return redirect()->route('menus.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $data = $this->menu->findOrFail($id);
        $recipes = $this->recipe->where('menu_id', $data->id)->get();
        $mode_action = 'update';
        $name = ['Menus', 'Edit', $data->name];
        $mode = [route('menus.index'), route('menus.edit', $id), route('menus.edit', $id)];

        $this->audit_trail_logs('', '', 'menus: '.$data->name, $id);

        $menu_category = $this->category->where('status', 1)->get();
        $select_menu_category = $this->category->find($data->menu_categories_id);

        $menu_type = $this->type->where('status', 1)->get();
        $select_menu_type = $this->type->find($data->menu_type_id);

        $products = $this->product->where(array('inventoriable' => 1, 'status' => 1))->get();

        return view('pages.menus.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Menus',
            'title' => 'Menus',
            'data' => $data,
            'products' => $products,
            'menu_category' => $menu_category,
            'select_menu_category' => $select_menu_category,
            'menu_type' => $menu_type,
            'select_menu_type' => $select_menu_type,
            'recipes' => $recipes
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
            $update = $this->menu->find($id)->update([
                'menu_categories_id' => $validated['menu_category'],
                'menu_type_id' => $validated['menu_type'],
                'name' => $validated['name'],
                'price' => $validated['price'],
                'status' => $validated['status'],
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

            if ($update) {
                for ($i=0; $i < count($request->input('recipe')); $i++) { 
                    $stock_out = $request->input('recipe_qty');
                    $recipes = $request->input('recipe');
                    $product = explode('|', $recipes[$i]);

                    $menu = $this->menu->latest()->first();

                    $this->recipe->menu_id = $menu->id;
                    $this->recipe->menu_name = $menu->name;
                    $this->recipe->product_id = $product[0];
                    $this->recipe->product_name = $product[1];
                    $this->recipe->stock_out = $stock_out[$i];
                    $this->recipe->created_by = Auth::id();
                    $this->recipe->created_at = now();
                    $this->recipe->save();
                }
            }

            $this->audit_trail_logs('', 'updated', 'menus: '.$this->menu->name, $id);

            return redirect()->route('menus.index')
                ->with('success', 'Menu Updated Successfully');
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
}
