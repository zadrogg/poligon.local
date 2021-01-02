<?php

namespace App\Repositories;

use App\Models\BlogPost as Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class BlogCategoryRepository
 *
 * @package App\Repositories
 */
class BlogPostRepository extends CoreRepository
{
    /**
     * @return string
     */
    public function getModelClass()
    {
        return Model::class;
    }

    /**
     * Получить список статей для вывода в списке
     * (Админка)
     * 
     * @return LengthAwarePaginator
     */

    public function getAllWithPaginate()
    {
        $columns = [
            'id',
            'title',
            'slug',
            'is_published',
            'published_at',
            'user_id',
            'category_id',
        ];
        // dd($result); // проверка
        $result = $this->startConditions()
            ->select($columns)
            ->orderBy('id', 'DESC')
            // ->with(['category', 'user'])
            ->with([
                // Можно так
                'category' => function ($query) {
                    $query->select(['id', 'title']);
                },
                // Или так
                'user:id,name',
            ])

            ->paginate(25);
            // ->get(); //dd

            // $post = $result->first();
            // $post->user->id;
            // $post->category->id;

            // dd($post);

        return $result;
    }
}
