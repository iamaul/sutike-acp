<?php

namespace App\Http\Controllers\Blog;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\Request as BlogRequest;

use Yajra\Datatables\Datatables;

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
            $req_search = (string) ($request->search['value'] != null ? $request->search['value'] : null);
            $select_query = [
                \DB::raw('GROUP_CONCAT(DISTINCT blogs.id) AS id'),
                \DB::raw('GROUP_CONCAT(DISTINCT blog_tags.name) AS tag_name'),
                \DB::raw('GROUP_CONCAT(DISTINCT blogs.title) AS title'),
                \DB::raw('GROUP_CONCAT(DISTINCT blogs.slug) AS slug'),
                \DB::raw('GROUP_CONCAT(DISTINCT blogs.header_image) AS header_image'),
                \DB::raw('GROUP_CONCAT(DISTINCT blogs.body) AS body')
                // \DB::raw('GROUP_CONCAT(DISTINCT blogs.created_at) AS created_at')
            ];

            $order_column = [
                'id',
                'tag_name',
                'title',
                'slug',
                'header_image',
                'body'
            ];
            $raw_columns = ['action'];

            $blogs = $this->blogs->leftJoin('blog_tags', 'blogs.tag_id', '=', 'blog_tags.id');

            if ($req_search) {
                $blogs = $blogs
                    ->where('blog_tags.name','like', '%'.$req_search.'%')
                    ->orWhere('blogs.title','like', '%'.$req_search.'%')
                    ->orWhere('blogs.slug','like', '%'.$req_search.'%')
                    ->orWhere('blogs.header_image','like', '%'.$req_search.'%');
            }
            // counter
            $counter = $blogs;
            $counter = $counter->count();
            // order manual data
            if (isset($request->order[0]['column'])) {
                if ($request->order[0]['column'] != "0") {
                    $order_dir = (isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'asc');
                    $blogs = $blogs->orderBy($order_column[(int)$request->order[0]['column']], $order_dir);
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

            // $blogs = $blogs->groupBy('blogs.id')->groupBy(\DB::raw('DATE_FORMAT(FROM_UNIXTIME(blogs.created_at), "%Y-%m-%d")'));
            $blogs = $blogs->select($select_query);

            // pagination model data
            $blogs = $blogs->paginate($limit, $select_query, 'page', $page);
            $total = $blogs->total();
            $blogs = $blogs->getCollection();
            return Datatables::of($blogs)
                ->addColumn('action', __v().'.blogs.datatables.action')
                ->skipPaging()
                ->setTotalRecords($counter)
                ->setFilteredRecords($counter)
                ->rawColumns(['action'])
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
        return view("{$this->view}::blogs.form");
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
        $filename = '';

        if ($request->ajax()) {
            $slug = Str::slug($request->title, '-');

            $blog = $this->blogs->create([
                'tag_id'    =>  $request->tag_id,
                'title'     =>  $request->title,
                'slug'      =>  $slug,
                'header_image'  =>  '',
                'body'      =>  $request->body  
            ]);

            if ($request->file('header_image')) {
                $blog->header_image = $request->file('header_image')->store('blogs/'.$slug, 'spaces');
                Storage::cloud()->setVisibility($blog->header_image, 'public');
            } else {
                $blog->header_image = null;
            }
            $blog->save();

            return response()->successResponse(microtime_float(), $blog, 'Blog created successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to create blog');
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
        $blog = $this->blogs->with('blogTags')->find($id);
        $blog['header_image_name'] = '';
        if (strpos($blog['header_image'], 'namespace_')) {
            $blog['header_image_name'] = substr($blog['header_image'], strpos($blog['header_image'], 'namespace_') + 10);
        }
        $blog['header_imagepath'] = Storage::cloud()->url($blog->header_image);

        return view("{$this->view}::blogs.form", ['blog' => $blog]);
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
        $blog = $this->blogs->find($id);

        $current_file = '';
        $old_file = '';
        $slug = Str::slug($request->title, '-');

        if ($request->file('header_image')) {
            // Delete old file
            // Storage::cloud()->delete('blogs/'.$blog->slug.'/'.$blog->header_image_name);
            Storage::cloud()->delete($blog->header_image_name);
            // Replace with a new file
            $current_file = Storage::cloud()->put('blogs/'.$slug, $request->file('header_image'));
            $old_file = $blog->header_image_name;
        }

        if ($request->ajax()) {   
            $blog->update([
                'tag_id'    =>  $request->tag_id,
                'title'     =>  $request->title,
                'slug'      =>  $slug,
                'header_image'  => (strlen($current_file) > 1) ? $current_file : ($old_file ? $old_file : null),
                'body'      =>  $request->body
            ]);
            return response()->successResponse(microtime_float(), $blog, 'Blog updated successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to update blog');
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
        if ($request->ajax()) {
            $blog = $this->blogs->findOrFail($id);
            // Delete file from cloud storage

            if ($blog->delete()) {
                return response()->successResponse(microtime_float(), [], 'Blog deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete blog');
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
            return response()->failedResponse(microtime_float(), 'Failed to delete blogs');
        }
    }
}
