<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Crypt;
use Validator;
use Str;

use App\Models\MenuCategories;
use Carbon\Carbon;

class MenuCategoryController extends Controller
{
    protected $category;
    public function __construct(MenuCategories $category){
        $this->category = $category;
    }

    public function validator(Request $request)
    {
        $input = [
            'upload_type' => $this->safeInputs($request->input('upload_type')),
            'category_image' => $request->file('category_image'),
            'url_image' => $request->input('url_image'),
            'category_icon' => $this->safeInputs($request->input('category_icon')),
            'tag_color' => $this->safeInputs($request->input('tag_color')),
            'name' => $this->safeInputs($request->input('name')),
            'status' => $this->safeInputs($request->input('status')),
        ];

        $rules = [
            'upload_type' => 'required',
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'url_image' => 'nullable|url|',
            'category_icon' => 'required|max:100',
            'tag_color' => 'required|max:50|unique:menu_categories,name,'.$this->safeInputs($request->input('id')).'',
            'name' => 'required|string|max:255|unique:menu_categories,name,'.$this->safeInputs($request->input('id')).'',
            'status' => 'required|numeric'
        ];

        $messages = [
            'status.numeric' => "The status must have a valid value",
        ];

        $customAttributes = [
            'upload_type' => 'upload type',
            'category_image' => 'file',
            'url_image' => 'url',
            'category_icon' => 'icon',
            'tag_color' => 'tag color',
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
        $name = ['Menu Categories'];
        $mode = [route('menu_categories.index')];
        
        $rows = array();
        $rows = $this->category->latest()->get();
        $rows = $this->changeVal($rows);
            
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
        $columnDefs[] = array_merge(array('headerName'=>'Status','field'=>'status'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'updated_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'updated_at'), $arr_set);
        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.menu_categories.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Menu Categories',
            'title' => 'Menu Categories'
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
        $name = ['Menu Categories', 'Create'];
        $mode = [route('menu_categories.index'), route('menu_categories.create')];

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.menu_categories.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Menu Categories',
            'title' => 'Menu Categories'
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
            $uploadType = explode('|', $validated['upload_type']);
            $uploadIndex = $uploadType[0];
            $uploadName = $uploadType[1];

            $urlImage = $validated['url_image'];
            $fileImage = $validated['category_image'];

            $data = $this->category;
            $data->upload_type = $validated['upload_type'];
            if ($uploadIndex == 0) { // URL
                $data->category_image = $urlImage;
            }else if($uploadIndex == 1){ // FILE
                if ($fileImage->isValid()) {
                    $publicFolder = public_path('images/menu_categories/');
                    $profileImage = $fileImage->getClientOriginalName(); // returns original name
                    $extension = $fileImage->getclientoriginalextension(); // returns the file extension
                    $newProfileImage = strtoupper(Str::random(20)).'.'.$extension;
                    $move = $fileImage->move($publicFolder, $newProfileImage);
                    if ($move) {
                        $data->category_image = $newProfileImage;
                    }else{
                        return back()->with('error', "Failed to upload image");
                    }
                }else{
                    return back()->with('error', "Something wrong with the image, please try again..");
                }
            }
            $data->category_icon = $validated['category_icon'];
            $data->tag_color = $validated['tag_color'];
            $data->name = $validated['name'];
            $data->status = $validated['status'];
            $data->created_by = Auth::user()->id;
            $data->created_at = Carbon::now();
            $data->save();

            $this->audit_trail_logs('', 'created', 'menu_categories: '.$validated['name'], $this->category->id);

            return redirect()->route('menu_categories.index')->with('success', 'You have successfully added '.$validated['name']);
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
        $name = ['Menu Categories', 'Edit', $data->name];
        $mode = [route('menu_categories.index'), route('menu_categories.edit', $id), route('menu_categories.edit', $id)];

        $this->audit_trail_logs('', '', 'menu_categories: '.$data->name, $id);

        return view('pages.menu_categories.create', [
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Menu Categories',
            'title' => 'Menu Categories',
            'data' => $data,
            'uniqueData' => $this->category->all()
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
        $data = $this->category->findOrFail($id);
        $validated = $this->validator($request);
        if($validated){
            $uploadType = explode('|', $validated['upload_type']);
            $uploadIndex = $uploadType[0];
            $uploadName = $uploadType[1];
            $urlImage = $validated['url_image'];
            $fileImage = $validated['category_image'];

            $data->upload_type = $validated['upload_type'];
            if ($uploadIndex == 0) { // URL
                $data->category_image = $urlImage;
            }else if($uploadIndex == 1){ // FILE
                if ($fileImage->isValid()) {
                    $publicFolder = public_path('images/menu_categories/');
                    $profileImage = $fileImage->getClientOriginalName(); // returns original name
                    $extension = $fileImage->getclientoriginalextension(); // returns the file extension
                    $newProfileImage = strtoupper(Str::random(20)).'.'.$extension;
                    $move = $fileImage->move($publicFolder, $newProfileImage);
                    if ($move) {
                        $data->category_image = $newProfileImage;
                    }else{
                        return back()->with('error', "Failed to upload image");
                    }
                }else{
                    return back()->with('error', "Something wrong with the image, please try again..");
                }
            }
            $data->category_icon = $validated['category_icon'];
            $data->tag_color = $validated['tag_color'];
            $data->name = $validated['name'];
            $data->status = $validated['status'];
            $data->updated_by = Auth::user()->id;
            $data->save();

            $this->audit_trail_logs('', 'updated', 'menu_categories: '.$data->name, $id);

            return redirect()->route('menu_categories.index')
                ->with('success', 'You have successfully updated '.$validated['name']);
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
        $this->audit_trail_logs('', 'deleted', 'menu_categories '.$data->name, $id);
        
        return redirect()->route('menu_categories.index')
            ->with('success', 'You have successfully removed '.$data->name);
    }
}
