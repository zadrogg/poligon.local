<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Requests\BlogCategoryCreateRequest;
use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$dsd = BlogCategory::all();
        $paginator = BlogCategory::paginate(15);
        //dd($paginator);
        //dd($dsd, $paginator);

        return view('blog.admin.categories.index', compact('paginator'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = new BlogCategory();
        //dd($item); //посмотреть что в $item
        $categoryList = BlogCategory::all();

        return view('blog.admin.categories.edit', compact('item', 'categoryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogCategoryCreateRequest $request)
    {
        $data = $request->input();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        
        // Создаст объект, но не добавит в БД
        // $item = new BlogCategory($data);
        // dd($item);
        // $item->save();

        // Создаст объект и добавит в БД
        $item = (new BlogCategory())->create($data);

        if($item) {
            return redirect()->route('blog.admin.categories.edit', [$item->id])->with(['success' => 'Успешно сохранено']);
        } else {
            return back()->withErrors(['msg' => 'Ошибка сохранения'])->withInput();
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
        //$item = BlogCategory::find($id);
        $item = BlogCategory::findOrFail($id); //недопустимо
        //$item[] = BlogCategory::where('id', $id)->first(); //get-collection
         
        //dd(collect($item)->pluck('id'));
        $categoryList = BlogCategory::all();

        return view('blog.admin.categories.edit', compact('item', 'categoryList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlogCategoryUpdateRequest $request, $id)
    {
        // $rules = [
        //     'title'         => 'required|min:5|max:200',
        //     'slug'          => 'max:200',
        //     'description'   => 'string|max:500|min:3',
        //     'parent_id'     => 'required|integer|exists:blog_categories,id',
        // ];

        //$validatedData = $this->validate($request, $rules); //Обращение к контроллеру
        
        // $validatedData = $request->validate($rules); //Обращение к requrest

        // $validator = \Validator::make($request->all(), $rules); //Валидация вручную
        // $validatedData[] = $validator->passes();
        // $validatedData[] = $validator->validate();
        // $validatedData[] = $validator->valid();
        // $validatedData[] = $validator->failed();
        // $validatedData[] = $validator->errors();
        // $validatedData[] = $validator->fails();

        // dd($validatedData);

        $item = BlogCategory::find($id);
    
        if (empty($item)) {
            return back()
                ->withErrors(['msg' => "Запись id=[$id] не найдена"])
                ->withInput();
        }

        $data = $request->all(); //$validatedData вместо all

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        
        $result = $item->fill($data)->save();

        if ($result) {
            return redirect()
                ->route('blog.admin.categories.edit', $item->id)
                ->with(['success' => 'Успешно сохранено']);
        } else {
            return back()
                ->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }
    }
}
