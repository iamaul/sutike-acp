<?php

namespace App\Http\Controllers\ProductCategory;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
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
                ->addColumn('action', __v().'.product_categories.datatables.action')
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
        //
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
        //
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
        //
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
        //
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
        //
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
