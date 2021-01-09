<?php

namespace App\Jobs\GenerateCatalog;

class GenerateCatalogMainJob extends AbstractJob
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */

    public function handle()
    {
        $this->debug('start');

        // Сначала кэшируем продукты
        GenerateCatalogCacheJob::dispatchNow();

        // Затем создаем цепочку заданий формирования файлов с ценами
        $chainPrices = $this->getChainPrices();

        // Основные подзадачи
        $chainMain = [
            new GenerateCategoriesJob, // Генерация категорий
            new GenerateDeliveriesJob, // Генерация способов доставок
            new GeneratePointsJob, // Генерация пунктов выдачи
        ];

        // Подзадачи которые должны выполниться самыми последними
        $chainLast = [
            // Архивирование файлов и перенос архива в публичную папку
            new ArchiveUploadsJob,
            // Отправка уведомления стороннему сервису о том, что можно скачать новый файл каталога товаров
            new SendPriceRequestJob,
        ];

        $chain = array_merge($chainPrices, $chainMain, $chainLast);

        GenerateGoodsFileJob::withChain($chain)->dispatch();
        // GenerateGoodsFileJob::dispatch()->chain($chain);

        $this->debug('finish');

    }

    /**
     * Формирование цепочек подзадач по генерации файлов с ценами
     * 
     * @return array
     */
    private function getChainPrices()
    {
        $result = [];
        $product = collect([1, 2, 3, 4, 5]);
        $fileNum = 1;

        foreach ($product->chunk(1) as $chunk) {
            $result[] = new GeneratePricesFileChunkJob($chunk, $fileNum);
            $fileNum++;
        }

        return $result;
    }
}
