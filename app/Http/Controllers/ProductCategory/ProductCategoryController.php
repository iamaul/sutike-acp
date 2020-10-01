<?php

namespace App\Http\Controllers\ProductCategory;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategory\Request as ProductCategoryRequest;

class ProductCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProductCategory $product_categories)
    {
        parent::__construct();
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
    public function index(ProductCategoryRequest $request)
    {
        if ($request->ajax()) {
            return app('datatables')->eloquent($this->product_categories->query())
                ->addColumn('action', __v().'.product-categories.datatables.action')
                ->rawColumns(['action'])
                ->orderColumns([], ':column $1')
                ->addIndexColumn()
                ->make(true);
        }
        return view("{$this->view}::product-categories.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function create(ProductCategoryRequest $request)
    {
        if ($request->ajax()) {
            if (request()->has('id')) {
                $categories = $this->product_categories->where('name', request('name'))->where('id', '<>', request('id'))->first();
            } else {
                $categories = $this->product_categories->where(['name' => request('name')])->first();
            }
            return response()->json(['valid' => $categories ? false : true ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductCategory\Request
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function store(ProductCategoryRequest $request)
    {
        if ($request->ajax()) {
            $category = $this->product_categories->create([
                'name'  =>  $request->name,
                'slug'  =>  Str::slug($request->name, '-')
            ]);
            return response()->successResponse(microtime_float(), $category, 'Product category created successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to create product category');
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
    public function show(ProductCategoryRequest $request, $id)
    {
        //
    }

    /**
     * Show product categories for select2.
     */
    public function select2(ProductCategoryRequest $request)
    {
        $page = (isset($request->page) ? (int)$request->page : 1);
        $limit = (isset($request->limit) ? (int)$request->limit : 10);
        $search = (string)($request->search != null ? $request->search : null);

        $categories = $this->product_categories;
        if (isset($request->id)) {
            if ($request->id != "") {
                $categories = $categories->where('id', '!=', $request->id);
            }
        }
        if ($search != null) {
            $categories = $categories->where('name', 'like', '%'.$search.'%');
        }
        $total = $categories;
        $total = $total->count();
        $categories = $categories->paginate($limit, ['id', 'name'], 'page', $page);
        $categories = $categories->getCollection()->map(function ($value, $key) {
            return [
                'id' => $value['id'],
                'text' => $value['name']
            ];
        });
        $data = [
            'total_count' => $total,
            'incomplete_results' => false,
            'items' => $categories->toArray(),
        ];
        return json_encode($data);
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
    public function edit(ProductCategoryRequest $request, $id)
    {
        if ($request->ajax()) {
            $category = $this->product_categories->findOrFail($id);
            return response()->successResponse(microtime_float(), $category);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProductCategory\Request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function update(ProductCategoryRequest $request, $id)
    {
        if ($request->ajax()) {
            $category = $this->product_categories->find($id)->update([
                'name'  =>  $request->name,
                'slug'  =>  Str::slug($request->name, '-')
            ]);
            return response()->successResponse(microtime_float(), $category, 'Product category updated successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to update product category');
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
    public function destroy(ProductCategoryRequest $request, $id)
    {
        if ($request->ajax()) {
            $category = $this->product_categories->findOrFail($id);
            if ($category->delete()) {
                return response()->successResponse(microtime_float(), [], 'Product category deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete product category');
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
    public function destroyMany(ProductCategoryRequest $request)
    {
        if ($request->ajax()) {
            $id_can_be_destroy = [];
            foreach ($request->all() as $id) {
                $product_categories = $this->product_categories->findOrFail($id);
                if ($product_categories) array_push($id_can_be_destroy, $id);
            }
            if ($product_categories->destroy($id_can_be_destroy)) {
                return response()->successResponse(microtime_float(), [], 'product_categories deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete product_categories');
        }
    }
}
