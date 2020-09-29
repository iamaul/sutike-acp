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
            $select_query = [
                'blogs.id AS id',
                'blog_tags.name AS tag_name',
                'blogs.title AS title',
                'blogs.slug AS slug',
                'blogs.header_image AS header_image',
                'blogs.body AS body'
                // 'blogs.created_at AS created_at'
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

            $blogs = $this->blogs->leftJoin('blog_tags', 'blogs.tag_id', '=', 'blog_tags.id')->select($select_query);

            $req_search = (string) ($request->search['value'] != null ? $request->search['value'] : null);
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

            // $blogs = $blogs->select($select_query);

            // pagination model data
            $blogs = $blogs->paginate($limit, $select_query, 'page', $page);
            $total = $blogs->total();
            $blogs = $blogs->getCollection();
            return Datatables::of($blogs)
                ->addColumn('action', __v().'.blogs.datatables.action')
                ->editColumn('header_image', '<img src="{{ Storage::cloud()->url($header_image) }}" alt="{{ ucwords($header_image) }}" class="img-responsive avatar-on-table">')
                ->skipPaging()
                ->setTotalRecords($total)
                ->setFilteredRecords($total)
                ->rawColumns(['header_image', 'action'])
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
                $blog->header_image = $request->file('header_image')->store('blogs', 'spaces');
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
        $slug = Str::slug($request->title, '-');

        if ($request->file('header_image')) {
            // New file image
            $current_file = Storage::cloud()->put('blogs', file_get_contents($request->file('header_image')), 'public');
            // Old file image
            $old_file = $blog->header_image;
            Storage::cloud()->delete($old_file);
        }

        if ($request->ajax()) {   
            $blog->update([
                'tag_id'    =>  $request->tag_id,
                'title'     =>  $request->title,
                'slug'      =>  $slug,
                'header_image'  => $current_file == '' ? $blog->header_image : $current_file,
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
            Storage::cloud()->delete($blog->header_image);
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
            $images = [];
            foreach ($request->all() as $id) {
                $blogs = $this->blogs->findOrFail($id);
                if ($blogs) {
                    array_push($id_can_be_destroy, $id);
                    array_push($images, $blogs->header_image);
                }
            }
            if (count($images) < 2) {
                return response()->failedResponse(microtime_float(), 'Please select more items'); 
            } else if ($blogs->destroy($id_can_be_destroy)) {
                Storage::cloud()->delete($images);
                return response()->successResponse(microtime_float(), [], 'Blogs deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete blogs');
        }
    }
}
