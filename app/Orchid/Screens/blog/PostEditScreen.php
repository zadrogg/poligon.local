<?php

namespace App\Orchid\Screens\blog;

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

class PostEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Creating a new post';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Blog posts';

    /**
     * @var bool
     */
    public $exists = false;

    /**
     * Query data.
     *
     * @param BlogPost $post
     *
     * @return array
     */
    public function query(BlogPost $post): array
    {
        $this->exists = $post->exists;

        if ($this->exists) {
            $this->name = 'Edit post';
        }

        return [
            'post' => $post,
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
            Button::make('Create post')
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
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('post.title')
                    ->title('Title')
                    ->placeholder('Attractive but mysterious title')
                    ->help('Specify a short descriptive title for this post.'),

                TextArea::make('post.slug')
                    ->title('Identifier')
                    ->rows(3)
                    ->maxlength(200)
                    ->placeholder('Brief description for preview'),

                Relation::make('post.author')
                    ->title('Author')
                    ->fromModel(User::class, 'name'),

                Relation::make('post.category')
                    ->title('Category')
                    ->fromModel(BlogCategory::class, 'title'),

                Quill::make('post.content_raw')
                    ->title('Main text'),
            ]),
        ];
    }

    /**
     * @param BlogPost $post
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function CreateOrUpdate(BlogPost $post, Request $request)
    {
        $post->fill($request->get('post'))->save();

        Alert::info('You have successfully created an post.');

        return redirect()->route('platform.post.list');
    }

    /**
     * @param BlogPost $post
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function remove(BlogPost $post)
    {
        $post->delete();

        Alert::info('You have successfully deleted an post.');

        return redirect()->route('platform.post.list');
    }
}
