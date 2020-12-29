<?php

namespace App\Repositories;

use App\Models\BlogCategory as Model;

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
}
