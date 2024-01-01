<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\BuilderEntityController;
use App\Models\amocrmIDs;
use App\Models\AmoCrmLead;
use App\Models\PLANNING;
use Illuminate\Support\Facades\DB;

class LeadLinksBuilderController extends BuilderEntityController
{
    /**
     * @param int $amoLeadID
     * @return array
     * Description: get Lead row from the DB
     */
    public static function getRow(int $amoLeadID) : array{
        return amocrmIDs::all()->where('amoLeadID', '=', $amoLeadID)->first()?->amoBillID ?? [];
    }
}
