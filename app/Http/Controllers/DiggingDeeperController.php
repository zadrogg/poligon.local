<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCatalog\GenerateCatalogMainJob;
use App\Jobs\ProcessVideoJob;
use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class DiggingDeeperController extends Controller
{
    public function collections()
    {
        $result = [];

        /**
         * @var Illuminate\Database\Eloquent\Collection $eloquentCollection
         */
        $eloquentCollection = BlogPost::withTrashed()->get();
        // return view('layouts.app');
        // dd($eloquentCollection->count()); //count чтобы посмотреть кол-во общее

        // dd(__METHOD__, $eloquentCollection, $eloquentCollection->toArray());

        /**
         * @var Illuminate\Support\Collection $collection
         */
        // $collection = collect();
        // dd($collection);
        $collection = collect($eloquentCollection->toArray());

        // dd(
        //     get_class($eloquentCollection),
        //     get_class($collection),
        //     $collection
        // );

        // $result['first'] = $collection->first();
        // $result['last'] = $collection->last();
        // dd($result);

        $result['where']['data'] = $collection
            ->where('category_id', 10)
            ->values()
            ->keyBy('id')
            ;
        
        // $result['where']['count'] = $result['where']['data']->count();
        // $result['where']['isEmpty'] = $result['where']['data']->isEmpty();
        // $result['where']['isNotEmpty'] = $result['where']['data']->isNotEmpty();

        // $result['where_first'] = $collection
            // ->firstwhere('created_at', '>', '2019-01-07 01:35:17');
        
        // dd($result);

        // базовая переменная не изменится. Вернется измененная версия
        // $result['map']['all'] = $collection->map(function (array $item)
        // {
        //     // dd($item);
        //     $newItem = new \stdClass();
        //     $newItem->item_id = $item['id'];
        //     $newItem->item_name = $item['title'];
        //     $newItem->exists = is_null($item['deleted_at']);

        //     return $newItem;
        // });

        // $result['map']['not_exists'] = $result['map']['all']
        //     ->where('exists', '=', false)
        //     ->values()
        //     ->keyBy('item_id');

        // dd($result);
        
        //Базовая переменная изменится (трасформируется)
        $collection->transform(function (array $item)
        {
            $newItem = new \stdClass();
            $newItem->item_id = $item['id'];
            $newItem->item_name = $item['title'];
            $newItem->exists = is_null($item['deleted_at']);
            $newItem->created_at = Carbon::parse($item['created_at']);
        
            return $newItem;
        });
        
        // dd($collection);

        // $newItem = new \stdClass();
        // $newItem->id = 9999;

        // $newItem2 = new \stdClass();
        // $newItem2->id = 8888;

        // dd($newItem, $newItem2);

        // $collection->prepend($newItem);
        // $collection->push($newItem2);
        // dd($newItem, $newItem2, $collection);
        
        // Установить элемент в начало коллекции

        // $newItemFirst = $collection->prepend($newItem)->first();
        // $newItemLast = $collection->push($newItem2)->last();
        // $pulledItem = $collection->pull(10);

        // dd(compact('collection', 'newItemFirst', 'newItemLast', 'pulledItem'));

        // Фильтрация. Замена orWhere

        // $filtered = $collection->filter(function ($item) 
        // {
        //     // return $item->created_at->isFriday() && ($item->created_at->day == 11); //не читабельно
        //     $byDay = $item->created_at->isFriday();
        //     $byDate = $item->created_at->day == 9;

        //     // $result = $item->created_at->isFriday() && ($item->created_at->day == 11); //не читабельно

        //     $result = $byDay && $byDate;

        //     return $result;
        // });

        // dd(compact('filtered'));

        $sortedSimpleCollection = collect([5, 3, 1, 2, 4])->sort()->values();
        $sortedAscCollection = $collection->sortBy('created_at');
        $sortDescCollection = $collection->sortByDesc('created_at');

        dd(compact('sortedSimpleCollection', 'sortedAscCollection', 'sortDescCollection'));
    }

    public function ProcessVideo()
    {
        ProcessVideoJob::dispatch()
        // Отсрочка выполнения задания от момента помещения в очередь
        // Не влияет на паузу между попытками выполнять задачу
        // ->delay(10)
        // ->onQueue('name_of_queue')
        ;
    }

    public function prepareCatalog()
    {
        GenerateCatalogMainJob::dispatch();
    }
}
