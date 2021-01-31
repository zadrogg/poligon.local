<?php

namespace App\Orchid\Screens\blog\category;

use App\Models\BlogCategory;
use App\Orchid\Layouts\blog\category\CategoryListLayout;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;

class CategoryListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Blog categories';

    /**
     * Display header description.
     *
     * @var string|null
     */
    public $description = 'All categories';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'categories' => BlogCategory::paginate()
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Create new')
                ->icon('pencil')
                ->route('platform.category.edit')
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
            CategoryListLayout::class
        ];
    }
}
