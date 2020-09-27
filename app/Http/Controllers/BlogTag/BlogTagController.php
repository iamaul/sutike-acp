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
                ->addColumn('action', __v().'.blog-tags.datatables.action')
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
            $tag = $this->blog_tags->create([
                'name'  =>  $request->name,
                'slug'  =>  Str::slug($request->name, '-')
            ]);
            return response()->successResponse(microtime_float(), $tag, 'Blog tag created successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to create blog tag');
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
     * Show blog tags for select2.
     */
    public function select2(BlogTagRequest $request)
    {
        $page = (isset($request->page) ? (int)$request->page : 1);
        $limit = (isset($request->limit) ? (int)$request->limit : 10);
        $search = (string)($request->search != null ? $request->search : null);

        $tags = $this->blog_tags;
        if (isset($request->id)) {
            if ($request->id != "") {
                $tags = $tags->where('id', '!=', $request->id);
            }
        }
        if ($search != null) {
            $tags = $tags->where('name', 'like', '%'.$search.'%');
        }
        $total = $tags;
        $total = $total->count();
        $tags = $tags->paginate($limit, ['id', 'name'], 'page', $page);
        $tags = $tags->getCollection()->map(function ($value, $key) {
            return [
                'id' => $value['id'],
                'text' => $value['name']
            ];
        });
        $data = [
            'total_count' => $total,
            'incomplete_results' => false,
            'items' => $tags->toArray(),
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
        return response()->failedResponse(microtime_float(), 'Failed to update blog tag');
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
