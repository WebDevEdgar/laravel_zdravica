<?php

namespace App\Console\Commands;

use App\Http\Controllers\Product\ProductController;
use App\Models\AmoProducts;
use App\Models\OffersDB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class BulkProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:bulkProducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $offers = OffersDB::all(['LABEL','FM_SERV_ID','CODE'])->toArray();
        dd($offers[0]['CODE']);
        $offersChunks = array_chunk($offers, 40);
        $client = new Client(['verify' => false]);

        $ProductController = new ProductController($client);
        dd(count($offersChunks));
        foreach ($offersChunks as $offersChunk) {
            $preparedProducts = $ProductController->prepare($offersChunk);
            $proids = $ProductController->create($preparedProducts);
            foreach ($offersChunk as $k => $product) {
                $product = (array)$product;
                if (isset($proids[$k])
                && $product['LABEL'] !== ''
                    && $product['FM_SERV_ID'] > 0
                    && $proids[$k] > 0
                ){
                    AmoProducts::create([
                        'name' => (string)$product['LABEL'],
                        'DBId' => (integer)$product['FM_SERV_ID'],
                        'sku' => (string)$product['CODE'],
                        'amoID' => (integer)$proids[$k],
                    ]);
                }
            }
        }
    }
}
