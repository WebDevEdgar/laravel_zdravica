<?php

namespace Tests\Feature;

use App\Http\Controllers\Product\ProductController;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
//    public function testCheckTheProductAmoID(): void
//    {
//        AmoProducts::create([
//            'name'  => 'Примерка колес',
//            'amoID'  => '451691',
//            'DBId'  => '',
//        ]);
//        AmoProducts::create([
//            'name'  => 'Эпидурография',
//            'amoID'  => '460763',
//            'DBId'  => '',
//        ]);
//
//        $client = new Client(['verify'=>false]);
//        $Product = new ProductController($client);
//
//        $ids = $Product->getAmoIDs(['Примерка колес','Эпидурография']);
//        $this->assertEquals(451691,$ids[0]);
//        $this->assertEquals(460763,$ids[1]);
//    }
//
//    /**
//     * A basic feature test example.
//     */
//    public function testUploadTheProduct(): void
//    {
//        $client = new Client(['verify'=>false]);
//        $Product = new ProductController($client);
//
//        $prepared = $Product->prepare(['Новая услуга']);
//        $newIds = $Product->create($prepared);
//        $this->assertGreaterThan(0, $newIds[0]);
//    }

    /**
     * A basic feature test example.
     */
    public function testLinkTheProductToLead(): void
    {
        $offersData = [
            'offerNames'    => [
                'Новая услуга',
                'Эстеразный ингибитор С1 комплемента - функциональный',
            ],
            'offerPrices'    => [
                5000,
                6000
            ],
        ];
        $client = new Client(['verify'=>false]);
        $Product = new ProductController($client);
        $response = $Product->setProducts(13983829,$offersData);
        $this->assertEquals(200,$response->getStatusCode());
    }

    public function testUnLinkTheProductToLead(): void
    {

        $offersData = [
            'offerNames'    => [
                'Новая услуга'
            ],
            'offerPrices'    => [
                5000
            ],
        ];
        $client = new Client(['verify'=>false]);
        $Product = new ProductController($client);
        $response = $Product->unsetProducts(13983829,$offersData);
        $this->assertEquals(204,$response->getStatusCode());
    }
}
