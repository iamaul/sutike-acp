<?php

namespace App\Http\Controllers\Blog;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\Request as BlogRequest;

class BlogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Blog $blogs)
    {
        parent::__construct();
        $this->blogs = $blogs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function index(BlogRequest $request)
    {
        if ($request->ajax()) {
            return app('datatables')->eloquent($this->blogs->query())
                ->addColumn('action', __v().'.blogs.datatables.action')
                ->rawColumns(['action'])
                ->orderColumns([], ':column $1')
                ->addIndexColumn()
                ->make(true);
        }
        return view("{$this->view}::blogs.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function create(BlogRequest $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Blog\Request
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function store(BlogRequest $request)
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
    public function show(BlogRequest $request, $id)
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
    public function edit(BlogRequest $request, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Blog\Request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function update(BlogRequest $request, $id)
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
    public function destroy(BlogRequest $request, $id)
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
    public function destroyMany(BlogRequest $request)
    {
        if ($request->ajax()) {
            $id_can_be_destroy = [];
            foreach ($request->all() as $id) {
                $blogs = $this->blogs->findOrFail($id);
                if ($blogs) array_push($id_can_be_destroy, $id);
            }
            if ($blogs->destroy($id_can_be_destroy)) {
                return response()->successResponse(microtime_float(), [], 'Deleted blogs successfully');
            }
            return response()->failedResponse(microtime_float(), 'Deleted blogs unsuccessfully');
        }
    }
}
