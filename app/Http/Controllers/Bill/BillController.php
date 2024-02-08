<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    public function __construct($client){
        $this->BillPrepareController = new BillPrepareController();
        $this->client = $client;
    }

    public function builder($billID){
        return [
            'id' => $billID,
            'custom_fields_values' => [
                [
                    'field_id' => 1550048,
                    'values' => [
                        [
                            'value' => 'Оплачен',
                        ]
                    ]
                ]
            ],
        ];
    }

    public function prepare(array $billDB, int $billStatus): array
    {
        return $this->BillPrepareController->prepare($billDB, $billStatus);
    }

    public function createBill(array $billDB, int $billStatus) : int{
        $prepared = $this->prepare($billDB, $billStatus);
        return BillRequestController::create($this->client, $prepared);
    }

    public function updateBill(array $billDB, int $billStatus) : void{
        if ($billStatus === 0){
            $billDB = $this->prepare($billDB, $billStatus);
            Log::info(print_r($billDB,true));
        }
        BillRequestController::update($this->client, $billDB);
    }
}
