<?php

namespace App\Http\Controllers\BlogTag;

use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        if ($request->ajax()) {
            if (request()->has('id')) {
                $tags = $this->blog_tags->where('name', request('name'))->where('id', '<>', request('id'))->first();
            } else {
                $tags = $this->blog_tags->where(['name' => request('name')])->first();
            }
            return response()->json(['valid' => $tags ? false : true ]);
        }
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
        if ($request->ajax()) {
            $tag = new BlogTags([
                'name'  =>  $request->name,
                'slug'  =>  Str::slug($request->name, '-')
            ]);
            $tag->save();
            return response()->successResponse(microtime_float(), $tag, 'Blog tag created successfully');
        }
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
        if ($request->ajax()) {
            $tag = $this->blog_tags->findOrFail($id);
            return response()->successResponse(microtime_float(), $tag);
        }
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
        if ($request->ajax()) {
            $tag = $this->blog_tags->find($id)->update([
                'name'  =>  $request->name,
                'slug'  =>  Str::slug($request->name, '-')
            ]);
            return response()->successResponse(microtime_float(), $tag, 'Blog tag updated successfully');
        }
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
        if ($request->ajax()) {
            $tag = $this->blog_tags->findOrFail($id);
            if ($tag->delete()) {
                return response()->successResponse(microtime_float(), [], 'Blog tag deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete blog tag');
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
    public function destroyMany(BlogTagRequest $request)
    {
        if ($request->ajax()) {
            $id_can_be_destroy = [];
            foreach ($request->all() as $id) {
                $blog_tags = $this->blog_tags->findOrFail($id);
                if ($blog_tags) array_push($id_can_be_destroy, $id);
            }
            if ($blog_tags->destroy($id_can_be_destroy)) {
                return response()->successResponse(microtime_float(), [], 'Blog tags deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete blog tags');
        }
    }
}
