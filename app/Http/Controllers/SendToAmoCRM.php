<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Bill\BillGeneralController;
use App\Http\Controllers\Contacts\ContactsGeneralController;
use App\Http\Controllers\Leads\LeadPrepareController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmocrmIDs;
use App\Models\PLANNING;
use GuzzleHttp\Client;

class SendToAmoCRM extends Controller
{
    private ContactsGeneralController $ContactsGeneralController;

    public function __construct($DBlead)
    {
        $client = new Client(['verify'=>false]);
        $this->ContactsGeneralController = new ContactsGeneralController($client);
        $this->DBlead = $DBlead;
    }

    /**
     * @param $DBlead
     * @return array
     */
    public function sendDealToAmoCRM(): array
    {
        $buildLead = $this->DBlead;
        $buildLead['FIO'] = $this->getPlanningFIO($this->DBlead);
        ksort($buildLead, SORT_NATURAL);
        $client = new Client(['verify' => false]);

        $buildContact = $this->getPatData($buildLead);
        if (isset($buildContact['NE_LE'])){
            $buildLead['agePat'] = $buildContact['agePat'] = $this->getAge($buildContact['NE_LE']);
        }

        if ($buildLead && $buildContact) {
            $buildLead['amoContactID'] = $this->ContactsGeneralController->getAmoID($buildContact);
            $buildLead['amoLeadID'] = (new LeadPresendController())->getAmoID($client, $buildLead);

            $this->updateLead($buildLead, $client);
            $buildLead['amoLeadID'] = (int)$buildLead['amoLeadID'];
            $amoData = $this->prepareDataForAmoCRMIds($buildLead);

            AmocrmIDs::create($amoData);
            return $buildLead;
        }
        return [];
    }

    /**
     * @param array $dbLead
     * @return string
     */
    protected function getPlanningFIO(array &$dbLead): string
    {
        $PLANNING = PLANNING::find($dbLead['leadDBId']);
        if ($PLANNING && $PLANNING->count() > 0) {
            $planningFirst = $PLANNING->first();
            return $planningFirst->NOM . ' ' . $planningFirst?->PRENOM . ' ' . $planningFirst?->PATRONYME;
        }
        return '';
    }

    protected function updateLead($buildLead, $client)
    {
        $leadPrepared = LeadPrepareController::prepare($buildLead, $buildLead['amoContactID']);
        $leadPrepared['amoLeadID'] = (int)$buildLead['amoLeadID'];
        $leadPrepared['pipeline_id'] = 7332486;
        $leadPrepared['status_id'] = 61034286;
        LeadRequestController::update($client, $leadPrepared);
    }

    protected function prepareDataForAmoCRMIds($buildLead)
    {
        $amoData = array_intersect_key($buildLead, [
            'amoContactID' => '',
            'amoLeadID' => '',
            'amoBillID' => '',
            'offers' => '',
            'leadDBId' => ''
        ]);
        foreach ($amoData as $k => &$IdsName) {
            if ($buildLead[$k] !== 'null') {
                if ($k === 'offers') {
                    $amoData[$k] = (int)$buildLead[$k];
                } else {
                    $amoData[$k] = $buildLead[$k];
                }
            } else {
                unset($amoData[$k]);
            }
        }
        unset($IdsName);
        return $amoData;
    }

    protected function getPatData($buildLead)
    {
        if (isset($buildLead['patID']) && (int)$buildLead['patID'] > 0) {
            $buildContact = $this->ContactsGeneralController->getRow(
                (int)$buildLead['patID'],
                (int)$buildLead['declareCall'] === 1
            );
            $buildContact['FIO'] = $buildLead['fioPat'];
        } else {
            $buildContact = ['FIO' => $buildLead['FIO'] ?? ''];
        }

        return $buildContact;
    }

    private function getAge($birthday) : int{
        $time = strtotime($birthday);
        $timeDataTime = date('Y',$time);
        $timeDataNow = date('Y');
        return $timeDataNow - $timeDataTime;
    }
}
