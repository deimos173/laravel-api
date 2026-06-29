<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use Illuminate\Console\Command;

class SyncApiData extends Command
{
    protected $signature = 'api:sync';
    protected $description = 'Беру данные из внешнего API и сохраняю в БД';

    public function handle()
    {
        $host = 'http://109.73.206.144:6969';
        $token = env('SECRET_API_KEY');
        
        $endpoints = ['sales', 'orders', 'incomes', 'stocks'];

        foreach ($endpoints as $endpoint) {
            $this->info("Данные для: {$endpoint}");
            $page = 1;
            $hasMoreData = true;

            while ($hasMoreData) {
                $queryParams = [
                    'page' => $page,
                    'limit' => 500,
                    'key' => $token
                ];

                if ($endpoint === 'stocks') {
                    $queryParams['dateFrom'] = now()->toDateString();
                } elseif ($endpoint === 'incomes') {
                    $queryParams['dateFrom'] = '2026-06-01';
                    $queryParams['dateTo'] = now()->toDateString();
                } else {
                    $queryParams['dateFrom'] = '2026-06-01';
                    $queryParams['dateTo'] = now()->toDateString();
                }

                $response = Http::get("{$host}/api/{$endpoint}", $queryParams);

                if ($response->failed()) {
                    $this->error("Ошибка запроса к эндпоинту {$endpoint} на странице {$page}");
                    $this->line("Ответ сервера: " . $response->body());
                    break;
                }

                $json = $response->json();
                    
                $records = $json['data'] ?? $json; 

                if (empty($records)) {
                    $hasMoreData = false;
                    continue;
                }

                $uniqueKey = 'id';
                if ($endpoint === 'sales') $uniqueKey = 'sale_id';
                if ($endpoint === 'orders') $uniqueKey = 'g_number';
                if ($endpoint === 'incomes') $uniqueKey = 'income_id';

                foreach ($records as $record) {
                    $dataToInsert = [];
                    
                    if ($endpoint === 'sales') {
                        if (!isset($record['sale_id'])) continue;
                        $dataToInsert = [
                            'sale_id'      => $record['sale_id'],
                            'product_name' => $record['subject'] ?? $record['category'] ?? 'Товар',
                            'quantity'     => 1,
                            'price'        => (int) ($record['finished_price'] ?? 0),
                            'total_amount' => (int) ($record['for_pay'] ?? 0),
                            'created_at'   => isset($record['date']) ? \Illuminate\Support\Carbon::parse($record['date'])->toDateTimeString() : now()->toDateTimeString(),
                            'updated_at'   => now()->toDateTimeString(),
                        ];
                    }

                    elseif ($endpoint === 'orders') {
                        if (!isset($record['g_number'])) continue;
                        $dataToInsert = [
                            'g_number'     => $record['g_number'],
                            'product_name' => $record['subject'] ?? $record['category'] ?? 'Товар',
                            'quantity'     => 1,
                            'price'        => (int) ($record['price_with_disc'] ?? 0),
                            'created_at'   => isset($record['date']) ? \Illuminate\Support\Carbon::parse($record['date'])->toDateTimeString() : now()->toDateTimeString(),
                            'updated_at'   => now()->toDateTimeString(),
                        ];
                    }

                    elseif ($endpoint === 'incomes') {
                        if (!isset($record['income_id'])) continue;
                        $dataToInsert = [
                            'income_id'  => $record['income_id'],
                            'source'     => $record['delivery_num'] ?? 'Поставка №' . $record['income_id'],
                            'price'      => (int) ($record['total_price'] ?? 0),
                            'created_at' => isset($record['date']) ? \Illuminate\Support\Carbon::parse($record['date'])->toDateTimeString() : now()->toDateTimeString(),
                            'updated_at' => now()->toDateTimeString(),
                        ];
                    }

                    elseif ($endpoint === 'stocks') {
                        $dataToInsert = [
                            'warehouse_name' => $record['warehouse_name'] ?? 'Основной склад',
                            'product_name'   => $record['subject'] ?? $record['category'] ?? 'Товар',
                            'quantity'       => (int) ($record['quantity'] ?? 0),
                            'created_at'     => now()->toDateTimeString(),
                            'updated_at'     => now()->toDateTimeString(),
                        ];
                        
                        DB::table('stocks')->insert($dataToInsert);
                        continue;
                    }

                    DB::table($endpoint)->updateOrInsert(
                        [$uniqueKey => $record[$uniqueKey]], 
                        $dataToInsert
                    );
                }

                $this->info("Успешно загружена страница {$page} для {$endpoint}");
                    
                if (isset($json['next_page_url']) && $json['next_page_url'] !== null) {
                    $page++;
                } else {
                    $hasMoreData = false;
                }
            }
        }

        $this->info('Синхронизация 100% завершена.');
    }
}

// Array
// (
//     [g_number] => 95945430031540908852
//     [date] => 2026-06-01
//     [last_change_date] => 2026-06-01
//     [supplier_article] => af32ab21bdf0fc85
//     [tech_size] => 66e7dff9f98764da
//     [barcode] => 166633770
//     [total_price] => 23768.25
//     [discount_percent] => 10
//     [is_supply] => 
//     [is_realization] => 1
//     [promo_code_discount] => 
//     [warehouse_name] => Чехов 1
//     [country_name] => Россия
//     [oblast_okrug_name] => Северо-Кавказский федеральный округ
//     [region_name] => Ставропольский край
//     [income_id] => 0
//     [sale_id] => S23618300353
//     [odid] => 
//     [spp] => 43
//     [for_pay] => 7810.75
//     [finished_price] => 5529
//     [price_with_disc] => 9950
//     [nm_id] => 418724377
//     [subject] => 716cae14263ef4b7
//     [category] => 9f463620982b6cc9
//     [brand] => a66c77274e96b48c
//     [is_storno] => 
// )