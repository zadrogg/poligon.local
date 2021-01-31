<?php

namespace App\Orchid\Layouts\blog;

use App\Models\BlogPost;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PostListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'posts';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('id', '#'),
            TD::make('user_id', 'Author'),
            TD::make('category_id', 'Category'),
            TD::make('title', 'Title')
                ->render(function (BlogPost $post) {
                    return Link::make($post->title)
                        ->route('platform.post.edit', $post);
                }),

            TD::make('created_at', 'Created'),
            TD::make('updated_at', 'Last edit'),

        ];
    }
}
