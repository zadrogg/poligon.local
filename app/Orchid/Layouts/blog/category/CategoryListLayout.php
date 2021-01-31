<?php

namespace App\Orchid\Layouts\blog\category;

use App\Models\BlogCategory;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CategoryListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'categories';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('id', '#'),
            TD::make('title', 'Title')
                ->render(function (BlogCategory $category) {
                    return Link::make($category->title)
                        ->route('platform.category.edit', $category);
                }),
            TD::make('parent_id', 'Parent')
                ->render(function (BlogCategory $category) {
                    return Link::make($category->parentTitle)
                        ->route('platform.category.edit', $category);
                }),
        ];
    }
}
