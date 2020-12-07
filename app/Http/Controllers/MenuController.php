<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Arr;
use Validator;
use Str;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\MenuCategories;
use App\Models\MenuTypes;
use App\Models\Products;
use App\Models\MenuRecipe;
use App\Models\User;

class MenuController extends Controller
{
    protected $menu, $menuCategory, $menuType, $product, $menuRecipe, $user;
    public function __construct(Menu $menu, MenuCategories $menuCategory, MenuTypes $menuType, Products $product, MenuRecipe $menuRecipe, User $user){
        $this->menu = $menu;
        $this->category = $menuCategory;
        $this->type = $menuType;
        $this->product = $product;
        $this->recipe = $menuRecipe;
        $this->user = $user;
    }

    public function validator(Request $request)
    {
        $input = [
            'upload_type' => $this->safeInputs($request->input('upload_type')),
            'url_image' => $request->input('url_image'),
            'menu_image' => $request->file('menu_image'),
            'menu_category' => $this->safeInputs($request->input('menu_category')),
            'menu_type' => $this->safeInputs($request->input('menu_type')),
            'name' => $this->safeInputs($request->input('name')),
            'price' => $this->safeInputs($request->input('price')),
            'status' => $this->safeInputs($request->input('status')),
            'recipe' => $request->input('recipe'),
            'recipe_qty' => $request->input('recipe_qty')
        ];

        $rules = [
            'upload_type' => 'required',
            'url_image' => 'nullable|url|',
            'menu_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'menu_category' => 'required',
            'menu_type' => 'required',
            'name' => 'required|string|max:255|unique:menus,name,'.$this->safeInputs($request->input('id')).'',
            'price' => 'required|numeric',
            'status' => 'required',
            'recipe.*' => 'required|string|max:100',
            'recipe_qty.*' => 'required|numeric'
        ];

        $messages = [
            'status.numeric' => "The status must have a valid value",
        ];

        $customAttributes = [
            'upload_type' => 'upload type',
            'url_image' => 'url',
            'menu_image' => 'image',
            'menu_category' => 'menu category',
            'menu_type' => 'menu type',
            'name' => 'name',
            'price' => 'price',
            'status' => 'status',
            'recipe' => 'recipe',
            'recipe_qty' => 'qty'
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
        $rows = $this->menu->latest()->get();
        $rows = $this->changeVal($rows);
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
        $columnDefs[] = array_merge(array('headerName'=>'Name','field'=>'name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Category','field'=>'menu_categories_id'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Price','field'=>'price'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Recipe','field'=>'recipe'), $arr_set);
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
            $uploadType = explode('|', $validated['upload_type']);
            $uploadIndex = @$uploadType[0];
            $uploadName = @$uploadType[1];
            
            $urlImage = $validated['url_image'];
            $fileImage = $validated['menu_image'];

            $data = $this->menu->insert([
                'menu_categories_id' => $validated['menu_category'],
                'menu_type_id' => $validated['menu_type'],
                'upload_type' => $validated['upload_type'],
                'menu_image' => @($uploadIndex == 1) ? $this->uploadImage($fileImage) : $urlImage,
                'name' => $validated['name'],
                'price' => $validated['price'],
                'status' => $validated['status'],
                'created_by' => Auth::id(),
                'created_at' => now()
            ]);

            if ($data && $request->has('recipe')) {
                $menu = $this->menu->latest()->first();

                for ($i=0; $i < count($validated['recipe']); $i++) { 
                    $stock_out = $validated['recipe_qty'];
                    $recipes = $validated['recipe'];
                    $product = explode('|', $recipes[$i]);

                    $checkRecipe = $this->recipe->where(array(
                        'menu_id' => $menu->id,
                        'product_id' => $product[0]
                    ))->first();

                    if(empty($checkRecipe)){
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
            'select_menu_category' => @$select_menu_category,
            'menu_type' => $menu_type,
            'select_menu_type' => @$select_menu_type,
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
            $uploadType = explode('|', $validated['upload_type']);
            $uploadIndex = @$uploadType[0];
            $uploadName = @$uploadType[1];
            $urlImage = $validated['url_image'];
            $fileImage = $validated['menu_image'];

            $data = $this->menu->find($id)->update([
                'menu_categories_id' => $validated['menu_category'],
                'menu_type_id' => $validated['menu_type'],
                'upload_type' => $validated['upload_type'],
                'menu_image' => @($uploadIndex == 1) ? $this->uploadImage($fileImage) : $urlImage,
                'name' => $validated['name'],
                'price' => $validated['price'],
                'status' => $validated['status'],
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

            $findMenu = $this->menu->find($id);
            $getRecipes = $this->recipe->where('menu_id', $id)->get(); 
            
            if ($data && $request->has('recipe')) {
                
                $currentRecipes = count($getRecipes);
                $newRecipes = count($validated['recipe']); 

                foreach ($getRecipes as $key => $value) {
                    $this->recipe->where('id', $value['id'])->delete();
                }

                for ($i=0; $i < $newRecipes; $i++) {
                    $stock_out = $validated['recipe_qty'];
                    $recipes = $validated['recipe'];
                    $product = explode('|', $recipes[$i]);

                    $checkRecipe = $this->recipe->where(array(
                        'menu_id' => $findMenu->id,
                        'product_id' => $product[0]
                    ))->first();
                    
                    if(empty($checkRecipe) || is_null($checkRecipe)){
                        $this->recipe->insert([
                            'menu_id' => $findMenu->id,
                            'menu_name' => $findMenu->name,
                            'product_id' => $product[0],
                            'product_name' => $product[1],
                            'stock_out' => $stock_out[$i],
                            'created_by' => Auth::id(),
                            'created_at' => now()
                        ]);
                    }
                }
            }else{
                $this->recipe->where('menu_id', $findMenu->id)->delete();
            }

            $this->audit_trail_logs('', 'updated', 'menus: '.$validated['name'], $id);

            return redirect()->route('menus.index')->with('success', 'You have successfully updated '.$validated['name']);
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
        $data = $this->menu->findOrFail($id);
        $data->delete();

        $recipes = $this->recipe->where('menu_id', $id)->get();
        foreach ($recipes as $key => $recipe) {
            $this->recipe->find($recipe->id)->delete();
        }

        $this->audit_trail_logs('', 'deleted', 'menus '.$data->name, $id);

        return redirect()->route('menus.index')->with('success', 'You have successfully added '.$data->name);
    }

    public function importValidator(Request $request){
      $input = ['import_file'=> $request->file('import_file')];
      $rules = ['import_file' => 'required|mimetypes:text/csv,text/plain,application/csv'];
      $messages = ['import_file' => 'Invalid File type'];
      $customAttributes = ['import_file' => 'Import File',];
      $validator = Validator::make($input, $rules, $messages,$customAttributes);
      return $validator->validate();
    }

    public function import(Request $request){
        $validator = $this->importValidator($request);
        $file = $validator['import_file'];

        if ($file->isValid()) {
            $handleFile = fopen($request->file('import_file'), "r");
            $rows = [];

            while($line = fgetcsv($handleFile)) {
                $rows[] = $line;
            }

            fclose($handleFile);

            if(count($rows) <= 1){
                return back()->with('import_failed', 'File is empty, Please check the file or try again');
            }else{
                $data = array();
                for ($i=1; $i < count($rows); $i++) { 
                    $data[] = $rows[$i];
                }

                for ($j=0; $j < count($data); $j++) {
                    $this->menu->insert([
                        'menu_categories_id' => $this->safeInputs(@$data[$j][0]),
                        'menu_type_id' => $this->safeInputs(@$data[$j][1]),
                        'name' => $this->safeInputs(@$data[$j][2]),
                        'price' => $this->safeInputs(@$data[$j][3]),
                        'status' => 1,
                        'created_by' => Auth::id(),
                        'created_at' => now()
                    ]);
                }

                return back()->with('import', 'File Imported Successfully');
            }
        }else{
            return back()->with('error', 'Invalid File Type');
        }
    }

    public function uploadImage($data){
        if ($data->isValid()) {
            $publicFolder = ('images/menus/');
            $profileImage = $data->getClientOriginalName(); // returns original name
            $extension = $data->getclientoriginalextension(); // returns the file extension
            $newProfileImage = strtoupper(Str::random(20)).'.'.$extension;
            $move = $data->storeAs($publicFolder, $newProfileImage);
          
            if ($move) {
                return $newProfileImage;
            }
        }
    }

    public function changeValue($rows){
        foreach ($rows as $key => $value) {
            if(Arr::exists($value, 'menu_categories_id')){
                $menu_categories = $this->category->find($value->menu_categories_id);
                $value->menu_categories_id = @$menu_categories->name;
            }

            if (Arr::exists($value, 'price')) {
                $value->price = "Php ".$value->price.".00";
            }

            if(Arr::exists($value, 'menu_type_id')){
                $menu_type = $this->type->find($value->menu_type_id);
                $value->menu_type_id = @$menu_type->name;   
            }
        }

        return $rows;
    }
}
