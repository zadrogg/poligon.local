<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Controllers\Blog\Admin\BaseController;
use App\Http\Requests\BlogPostCreateRequest;
use App\Http\Requests\BlogPostUpdateRequest;
use App\Jobs\BlogPostAfterCreateJob;
use App\Jobs\BlogPostAfterDeleteJob;
use App\Models\BlogPost;
use App\Repositories\BlogCategoryRepository;
use App\Repositories\BlogPostRepository;
use Illuminate\Http\Request;

/**
 * Управление статьями блога
 */

class blogPostController extends BaseController
{
    // подсказки ide, где искать
    /**
     * @var BlogPostRepository
     */
    private $blogPostRepository;

    /**
     * @var BlogCategoryRepository
     */
    private $blogCategoryRepository;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->blogPostRepository = app(BlogPostRepository::class);
        $this->blogCategoryRepository = app(BlogCategoryRepository::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginator = $this->blogPostRepository->getAllWithPaginate();

        return view('blog.admin.posts.index', compact('paginator'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = new BlogPost();

        $categoryList = $this->blogCategoryRepository->getForComboBox();

        return view('blog.admin.posts.edit', compact('item', 'categoryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogPostCreateRequest $request)
    {
        $data = $request->input();
        $item = (new BlogPost())->create($data);

        if ($item) {
            $job = new BlogPostAfterCreateJob($item);
            // dd($job);
            $this->dispatch($job);
            // $this->dispatch(new BlogPostAfterCreateJob($item));


            return redirect()->route('blog.admin.posts.edit', [$item->id])
                ->with(['success' => 'Успешно сохранено']);
        } else {
            return back()->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = $this->blogPostRepository->getEdit($id);
        if (empty($item)) {
            abort(404);
        }

        $categoryList = $this->blogCategoryRepository->getForComboBox();

        return view('blog.admin.posts.edit', compact('item', 'categoryList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlogPostUpdateRequest $request, $id)
    {
        $item = $this->blogPostRepository->getEdit($id);

        if (empty($item)) {
            return back()
                ->withErrors(['msg' => "Запись id=[$id] не найдена"])
                ->withInput();
        }

        $data = $request->all();

        // Ушло в обсервер
        // if (empty($data['slug'])) {
        //     $data['slug'] = \Str::slug($data['title']);
        // }
        // if (empty($item->published_at) && $data['is_published']) {
        //     $data['published_at'] = Carbon::now();
        // }

        $result = $item->update($data);

        if ($result) {
            return redirect()
                ->route('blog.admin.posts.edit', $item->id)
                ->with(['success' => 'Успешно сохранено']);
        } else {
            return back()
                ->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd(__METHOD__, $id, request()->all());

        //Софт-удаление, в бд остается
        $result = BlogPost::destroy($id);

        //полное удаление из бд
        // $result = BlogPost::find($id)->forceDelete();

        if ($result) {

            BlogPostAfterDeleteJob::dispatch($id)->delay(20);

            //> Варианты запуска

            // BlogPostAfterDeleteJob::dispatchNow($id);

            // dispatch(new BlogPostAfterDeleteJob($id));
            // dispatch_now(new BlogPostAfterDeleteJob($id));

            // $this->dispatch(new BlogPostAfterDeleteJob($id));
            // $this->dispatchNow(new BlogPostAfterDeleteJob($id));




            /* $job = BlogPostAfterDeleteJob($id);
            $job->handle();*/

            //< Варианты запуска

            return redirect()
                ->route('blog.admin.posts.index')
                ->with(['success' => "Запись id[$id] удалена"]);
        } else {
            return back()
                ->withErrors(['msg' => 'Ошибка удаления']);
        }
    }
    
    // /**
    //  * Восстановить удаленный пост
    //  * 
    //  * @param int $id
    //  */
    // public function restore($id)
    // {
    //     $result = BlogPost::withTrashed()->find($id)->restore();

    //     if ($result) {
    //         return redirect()
    //             ->route('blog.admin.posts.index')
    //             ->with(['success' => "Запись id[$id] восстановлена"]);
    //     } else {
    //         return back()
    //             ->withErrors(['msg' => 'Восстановить невозможно']);
    //     }
    // }
}
