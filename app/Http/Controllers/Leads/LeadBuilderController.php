<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\BuilderEntityController;
use App\Models\AmoCrmLead;
use App\Models\PLANNING;
use Illuminate\Support\Facades\DB;

class LeadBuilderController extends BuilderEntityController
{
    /**
     * @param int $id
     * @return array
     * Description: get Lead row from the DB
     */
    public static function getRow(int $id) : array{
        return AmoCrmLead::find($id)->toArray() ?? [];
    }
    public static function closeLead($amoLeadID){
        return [
            'id' => (integer)$amoLeadID,
            "name" => "1",
            "closed_at"=> time() + 5,
            "status_id"=> 143,
            "updated_by"=> 0
        ];
    }

    public static function finishLead($amoLeadID){
        return [
            'id' => (integer)$amoLeadID,
            "status_id"=> 142,
        ];
    }
}
