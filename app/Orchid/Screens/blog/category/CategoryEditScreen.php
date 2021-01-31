<?php

namespace App\Orchid\Screens\blog\category;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class CategoryEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Creating a new category';

    /**
     * Display header description.
     *
     * @var string|null
     */
    public $description = 'Blog categories';

    /**
     * @var bool
     */
    public $exists = false;

    /**
     * Query data.
     *
     * @param BlogCategory $category
     *
     * @return array
     */
    public function query(BlogCategory $category): array
    {
        $this->exists = $category->exists;

        if ($this->exists) {
            $this->name = 'Edit category';
        }

        return [
            'category' => $category,
        ];
    }

    /**
     * Button commands.
     *
     * @return link[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Create category')
                ->icon('pencil')
                ->method('CreateOrUpdate')
                ->canSee(!$this->exists),

            Button::make('Update')
                ->icon('note')
                ->method('CreateOrUpdate')
                ->canSee($this->exists),

            Button::make('Remove')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->exists),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('category.title')
                    ->title('Title')
                    ->placeholder('Заголовок'),

                TextArea::make('category.slug')
                    ->title('Identifier')
                    ->rows(3)
                    ->maxlength(200),

                Relation::make('category.parent')
                    ->title('Parent')
                    ->fromModel(BlogCategory::class, 'title'),

                Quill::make('post.description')
                    ->title('Описание'),
            ]),
        ];
    }

    /**
     * @param BlogCategory $category
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function CreateOrUpdate(BlogCategory $category, Request $request)
    {
        $category->fill($request->get('category'))->save();

        Alert::info('You have successfully created an category.');

        return redirect()->route('platform.category.list');
    }

    /**
     * @param BlogCategory $category
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function remove(BlogCategory $category)
    {
        $category->delete();

        Alert::info('You have successfully deleted an category.');

        return redirect()->route('platform.category.list');
    }
}
