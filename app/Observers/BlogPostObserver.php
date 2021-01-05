<?php

namespace App\Observers;

use App\Models\BlogPost;
use Carbon\Carbon;

class BlogPostObserver
{
    
    /**
     * Обработка ПЕРЕД созданием записи
     *
     * @param  \App\Models\BlogPost  $blogPost
     * @return void
     */
    public function creating(BlogPost $blogPost)
    {
        $this->setPublishedAt($blogPost);

        $this->setSlug($blogPost);
        
        $this->setHtml($blogPost);

        $this->setUser($blogPost);
    }

    /**
     * Обработка ПЕРЕД обновлением записи
     *
     * @param  \App\Models\BlogPost  $blogPost
     * @return void
     */
    public function updating(BlogPost $blogPost)
    {
        // $test[] = $blogPost->isDirty();
        // $test[] = $blogPost->isDirty('is_published');
        // $test[] = $blogPost->isDirty('user_id');
        // $test[] = $blogPost->getAttribute('is_published');
        // $test[] = $blogPost->is_published;
        // $test[] = $blogPost->getOriginal('is_published');
        // dd($test);

        $this->setPublishedAt($blogPost);
        // dd($blogPost);
        $this->setSlug($blogPost);

        // return false;
    }

    /**
     * Если дата публикации не установлена, происходит установка флага - Опубликовано,
     * то устанавливаем дату публикации на текущую
     *
     * @param BlogPost $blogPost
     * @return void
     */
    public function setPublishedAt(BlogPost $blogPost)
    {
        $needSetPublished = empty($blogPost->published_at) && $blogPost->is_published;
        // dd($needSetPublished);

        if ($needSetPublished) {
            $blogPost->published_at = Carbon::now();
        }
    }

    /**
     * Если поле слаг пустое, заполняем его конвертацией заголовка
     *
     * @param BlogPost $blogPost
     * @return void
     */
    public function setSlug(BlogPost $blogPost)
    {
        // dd(__METHOD__, empty($blogPost->slug));
        if (empty($blogPost->slug)) {
            $blogPost->slug = \Str::slug($blogPost->title);
        }
    }

    /**
     * Установка значения по полю content_html относительно поля content_raw.
     * 
     * @param BlogPost $blogPost
     */
    public function setHtml(BlogPost $blogPost)
    {
        if ($blogPost->isDirty('content_raw')) {
            // TODO: тут должна быть генерация markdown -> html
            $blogPost->content_html = $blogPost->content_raw;
        }
    }

    /**
     * Если не указан user_id, то устанавливаем пользователя по умолчанию
     * 
     * @param BlogPost $blogPost
     */
    public function setUser(BlogPost $blogPost)
    {
        $blogPost->user_id = auth()->id() ?? BlogPost::UNKNOWN_USER;
    }

    /**
     * Handle the BlogPost "deleted" event.
     *
     * @param  \App\Models\BlogPost  $blogPost
     * @return void
     */
    public function deleted(BlogPost $blogPost)
    {
        //
    }

    /**
     * Handle the BlogPost "restored" event.
     *
     * @param  \App\Models\BlogPost  $blogPost
     * @return void
     */
    public function restored(BlogPost $blogPost)
    {
        //
    }

    /**
     * Handle the BlogPost "force deleted" event.
     *
     * @param  \App\Models\BlogPost  $blogPost
     * @return void
     */
    public function forceDeleted(BlogPost $blogPost)
    {
        //
    }
}
