<?php

namespace App\Http\Controllers\BlogTag;

use App\Models\BlogTag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlogTag\Request as BlogTagRequest;

class BlogTagController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BlogTag $blog_tags)
    {
        parent::__construct();
        $this->blog_tags = $blog_tags;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function index(BlogTagRequest $request)
    {
        if ($request->ajax()) {
            return app('datatables')->eloquent($this->blog_tags->query())
                ->addColumn('action', __v().'.blog_tags.datatables.action')
                ->rawColumns(['action'])
                ->orderColumns([], ':column $1')
                ->addIndexColumn()
                ->make(true);
        }
        return view("{$this->view}::blog-tags.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function create(BlogTagRequest $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BlogTag\Request
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function store(BlogTagRequest $request)
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
    public function show(BlogTagRequest $request, $id)
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
    public function edit(BlogTagRequest $request, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BlogTag\Request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @author 
     * @since @version 0.1
     */
    public function update(BlogTagRequest $request, $id)
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
    public function destroy(BlogTagRequest $request, $id)
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
    public function destroyMany(BlogTagRequest $request)
    {
        if ($request->ajax()) {
            $id_can_be_destroy = [];
            foreach ($request->all() as $id) {
                $blog_tags = $this->blog_tags->findOrFail($id);
                if ($blog_tags) array_push($id_can_be_destroy, $id);
            }
            if ($blog_tags->destroy($id_can_be_destroy)) {
                return response()->successResponse(microtime_float(), [], 'Deleted blog_tags successfully');
            }
            return response()->failedResponse(microtime_float(), 'Deleted blog_tags unsuccessfully');
        }
    }
}
