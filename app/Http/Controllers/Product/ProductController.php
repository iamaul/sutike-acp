<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Request as ProductRequest;

use Yajra\Datatables\Datatables;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Product $products, ProductCategory $product_categories)
    {
        parent::__construct();
        $this->products = $products;
        $this->product_categories = $product_categories;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function index(ProductRequest $request)
    {
        if ($request->ajax()) {
            $select_query = [
                'products.id',
                'product_categories.name AS category_name',
                'products.name',
                'products.slug',
                'products.image AS product_image',
                'products.stock',
                'products.status'
            ];

            $order_column = [
                'id',
                'category_name',
                'name',
                'slug',
                'product_image',
                'stock',
                'status'
            ];
            $raw_columns = ['action'];

            $products = $this->products->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')->select($select_query);

            $req_search = (string) ($request->search['value'] != null ? $request->search['value'] : null);
            if ($req_search) {
                $products = $products
                    ->where('product_categories.name','like', '%'.$req_search.'%')
                    ->orWhere('products.name','like', '%'.$req_search.'%')
                    ->orWhere('products.slug','like', '%'.$req_search.'%');
            }
            // counter
            $counter = $products;
            $counter = $counter->count();
            // order manual data
            if (isset($request->order[0]['column'])) {
                if ($request->order[0]['column'] != "0") {
                    $order_dir = (isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'asc');
                    $products = $products->orderBy($order_column[(int)$request->order[0]['column']], $order_dir);
                }
            }

            $limit = (isset($request->length) ? ((int)$request->length > 0 ? $request->length : $counter) : 10);
            $start = (isset($request->start) ? (int)$request->start : 0);
            $page = ((int)$start / (int)$limit) + 1;

            $request->merge([
                "search" => [
                    "regex" => "false",
                    "value" => null
                ]
            ]);

            // $products = $products->select($select_query);

            // pagination model data
            $products = $products->paginate($limit, $select_query, 'page', $page);
            $total = $products->total();
            $products = $products->getCollection();
            return Datatables::of($products)
                ->addColumn('action', __v().'.products.datatables.action')
                ->editColumn('product_image', '<img src="{{ Storage::cloud()->url($product_image) }}" alt="{{ ucwords($product_image) }}" class="img-responsive avatar-on-table">')
                ->editColumn('status', '{{ $status ? "Public" : "Private" }}')
                ->skipPaging()
                ->setTotalRecords($total)
                ->setFilteredRecords($total)
                ->rawColumns(['product_image', 'action'])
                ->make(true);
        }
        return view("{$this->view}::products.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function create(ProductRequest $request)
    {
        return view("{$this->view}::products.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Product\Request
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function store(ProductRequest $request)
    {
        $filename = '';

        if ($request->ajax()) {
            $slug = Str::slug($request->name, '-');

            $product = $this->products->create([
                'product_category_id'   =>  $request->product_category_id,
                'name'                  =>  $request->name,
                'slug'                  =>  $slug,
                'image'                 =>  '',
                'description'           =>  $request->description,
                'short_description'     =>  $request->short_description,
                'price'                 =>  intval(str_replace(".", "", str_replace("Rp", "", $request->price))),
                'on_sale'               =>  $request->on_sale ? true : false,
                'sale_price'            =>  intval(str_replace(".", "", str_replace("Rp", "", $request->sale_price))),
                'stock'                 =>  $request->stock,
                'status'                =>  $request->status
            ]);
            $product_category = $this->product_categories->find($request->product_category_id);
            $category = $product_category['name'];

            if ($request->file('product_image')) {
                $product->image = $request->file('product_image')->store('products/'.$category, 'spaces');
                Storage::cloud()->setVisibility($product->image, 'public');
            } else {
                $product->image = null;
            }
            $product->save();

            return response()->successResponse(microtime_float(), $product, 'Product created successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to create product');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function show(ProductRequest $request, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function edit(ProductRequest $request, $id)
    {
        $product = $this->products->with('productCategories')->find($id);
        return view("{$this->view}::products.form", ['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Product\Request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function update(ProductRequest $request, $id)
    {
        $product = $this->products->find($id);

        $current_file = '';
        $slug = Str::slug($request->name, '-');

        if ($request->file('product_image')) {
            // New file image
            Storage::cloud()->putFile('blogs/'.$request->category, $request->file('product_image'), 'public');
            // Old file image
            $old_file = $product->image;
            Storage::cloud()->delete($old_file);
        }

        if ($request->ajax()) {   
            $product->update([
                'product_category_id'   =>  $request->product_category_id,
                'name'                  =>  $request->name,
                'slug'                  =>  $slug,
                'image'                 =>  $current_file == '' ? $product->image : $current_file,
                'description'           =>  $request->description,
                'short_description'     =>  $request->short_description,
                'price'                 =>  intval(str_replace(".", "", str_replace("Rp", "", $request->price))),
                'on_sale'               =>  $request->on_sale ? true : false,
                'sale_price'            =>  intval(str_replace(".", "", str_replace("Rp", "", $request->sale_price))),
                'stock'                 =>  $request->stock,
                'status'                =>  $request->status
            ]);
            return response()->successResponse(microtime_float(), $product, 'Product updated successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to update product');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function destroy(ProductRequest $request, $id)
    {
        if ($request->ajax()) {
            $product = $this->products->findOrFail($id);
            // Delete file from cloud storage
            Storage::cloud()->delete($product->image);
            if ($product->delete()) {
                return response()->successResponse(microtime_float(), [], 'Product deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete product');
        }
    }

    /**
     * Remove multiple resource from storage.
     *
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function destroyMany(ProductRequest $request)
    {
        if ($request->ajax()) {
            $id_can_be_destroy = [];
            $images = [];
            foreach ($request->all() as $id) {
                $products = $this->blogs->findOrFail($id);
                if ($products) {
                    array_push($id_can_be_destroy, $id);
                    array_push($images, $products->image);
                }
            }
            if (count($images) < 2) {
                return response()->failedResponse(microtime_float(), 'Please select more items'); 
            } else if ($products->destroy($id_can_be_destroy)) {
                Storage::cloud()->delete($images);
                return response()->successResponse(microtime_float(), [], 'products deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete products');
        }
    }
}
