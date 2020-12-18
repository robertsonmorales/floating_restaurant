<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Arr;

use Carbon\Carbon;
use App\Models\Delivery;
use App\Models\Products;
use App\Models\Stock;
use App\Models\InventoryTransaction;
use App\Models\ProductUnits;
use App\Models\DeliveredProduct;

class DeliveryController extends Controller
{
    protected $delivery, $product, $stock, $inventory, $unit, $deliverProduct;

    public function __construct(Delivery $delivery, Products $product, Stock $stock, InventoryTransaction $inventory, ProductUnits $unit, DeliveredProduct $deliverProduct){
        $this->delivery = $delivery;
        $this->product = $product;
        $this->stock = $stock;
        $this->inventory = $inventory;
        $this->unit = $unit;
        $this->deliveredProduct = $deliverProduct;
    }

    public function validator(Request $request)
    {
        $input = [
            'delivery_name' => $this->safeInputs($request->input('delivery_name')),
            'description' => $this->safeInputs($request->input('description')),
            'approved_by' => $this->safeInputs($request->input('approved_by'))
        ];

        $rules = [
            'delivery_name' => 'required|string|max:255|unique:deliveries,delivery_name,'.$this->safeInputs($request->input('id')),
            'description' => 'nullable|string|max:1000',
            'approved_by' => 'required|string|max:100',
        ];

        $messages = [];

        $customAttributes = [
            'delivery_name' => 'delivery name',
            'description' => 'description',
            'approved_by' => 'approval',
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
        $name = ['Deliveries'];
        $mode = [route('deliveries.index')];        
        
        $rows = array();
        $rows = $this->delivery->latest()->get();
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
        $columnDefs[] = array_merge(array('headerName'=>'Delivery Name','field'=>'delivery_name'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Approved By','field'=>'approved_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Description','field'=>'description'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated By','field'=>'created_by'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Created At','field'=>'created_at'), $arr_set);
        $columnDefs[] = array_merge(array('headerName'=>'Updated At','field'=>'created_at'), $arr_set);

        $data = json_encode(array('rows'=>$rows, 'column'=>$columnDefs));

        $this->audit_trail_logs('','','','');

        return view('pages.deliveries.index', [
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'data' => $data,
            'add' => 'Add New Record',
            'header' => 'Deliveries',
            'title' => 'Deliveries'
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
        $name = ['Deliveries', 'Create'];
        $mode = [route('deliveries.index'), route('deliveries.create')];

        $products = $this->product->where([
            ['inventoriable', 1],
            ['status', 1]
        ])->latest()->get();

        $this->audit_trail_logs('','','Creating new record','');

        return view('pages.deliveries.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Deliveries',
            'title' => 'Deliveries',
            'products' => $products
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
            $validatedProduct = $request->input('product');
            $validatedQty = $request->input('qty');
            $countProducts = count($validatedProduct);
            $findDelivery = $this->delivery->latest()->first();

            $data = $this->delivery;
            $data->delivery_name = $validated['delivery_name'];
            $data->description = $validated['description'];
            $data->approved_by = $validated['approved_by'];
            $data->created_by = Auth::id();
            $data->created_at = now();
            $data->save();
            
            if ($data) {
                for ($i=0; $i < $countProducts; $i++) { 
                    $explodeProducts = explode("|", $validatedProduct[$i]);
                    $productId = $this->safeInputs($explodeProducts[0]);
                    $productName = $this->safeInputs($explodeProducts[1]);

                    $findProduct = $this->product->find($productId);

                    $findDeliveredProduct = $this->deliveredProduct->where(array(
                        'delivery_id' => $data->id,
                        'product_id' => $productId
                    ))->first();

                    if (empty($findDeliveredProduct) || $findDeliveredProduct == null) {
                        $delivered = $this->deliveredProduct->insert([
                            'delivery_id' => $data->id,
                            'product_id' => $productId,
                            'product_name' => $productName,
                            'qty' => $validatedQty[$i],
                            'unit' => $findProduct->unit,
                            'created_by' => Auth::id(),
                            'created_at' => now()
                        ]);

                        if ($delivered) {
                            $findStock = $this->stock->find($productId);
                            
                            $findStock->update([
                                'stocks' => ($findStock->stocks + $validatedQty[$i]), 
                            ]);

                            $this->inventory->insert([
                                'product_id' => $productId,
                                'product_name' => $productName,
                                'product_category_id' => $findStock->product_category_id,
                                'product_category_name' => $findStock->product_category_name,
                                'type' => 1,
                                'qty' => $validatedQty[$i],
                                'unit' => $findProduct->unit,
                                'stocks' => $findStock->stocks,
                                'created_by' => Auth::id(),
                                'created_at' => now()
                            ]);

                            $this->audit_trail_logs('', 'created', 'deliveries: '.$productName, $this->delivery->id);   
                        }       
                    }
                }
            }

            return redirect()->route('deliveries.index')->with('success', 'You have successfully added '.$validated['delivery_name']);
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
        $data = $this->delivery->findOrFail($id);
        $mode_action = 'update';
        $name = ['Deliveries', 'Edit', $data->product_name];
        $mode = [route('deliveries.index'), route('deliveries.edit', $id), route('deliveries.edit', $id)];
        
        $deliverProduct = $this->deliveredProduct->where('delivery_id', $data->id)->get();

        $this->audit_trail_logs('', '', 'deliveries: '.$data->product_name, $id);

        $products = $this->product->where('status', 1)->latest()->get();
        $select_product = $this->product->find($data->product_id);

        return view('pages.deliveries.create', [            
            'mode' => $mode_action,
            'breadcrumbs' => $this->breadcrumbs($name, $mode),
            'header' => 'Deliveries',
            'title' => 'Deliveries',
            'data' => $data,
            'delivered_products' => $deliverProduct,
            'products' => $products,
            'select_product' => $select_product
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
        // $validated = $this->validator($request);
        // if($validated){
        //     $product = explode("|", $request->input('product'));
        //     $unit = $this->product->find($product[0]);

        //     $data = $this->delivery->findOrFail($id);
        //     $data->product_id = $product[0];
        //     $data->product_name = $product[1];
        //     $data->qty = $validated['quantity'];
        //     $data->unit = $unit->unit;
        //     $data->description = $validated['description'];
        //     $data->approved_by = $validated['approved_by'];
        //     $data->updated_by = Auth::user()->id;
        //     $data->save();

        //     $this->audit_trail_logs('', 'updated', 'deliveries: '.$data->product_name, $id);

        //     return redirect()->route('deliveries.index')->with('success', 'You have successfully updated '.$data->delivery_name);
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->delivery->findOrFail($id);
        $data->delete();
        $this->audit_trail_logs('', 'deleted', 'deliveries '.$data->product_name, $id);

        return redirect()->route('deliveries.index')->with('success', 'You have successfully removed '.$data->delivery_name);
    }

    public function changeValue($rows)
    {       
        foreach ($rows as $value) {
            if (Arr::exists($value, 'qty')) {
                $unit = $this->unit->findOrFail($value->unit);
                $value->qty = ''.$value->qty.' '.$unit->name.'';
            }
        }

        return $rows;
    }
}
