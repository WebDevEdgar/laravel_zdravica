<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPresendController;
use App\Http\Controllers\Leads\LeadBuilderController;
use App\Http\Controllers\Leads\LeadPrepareController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmoCrmLead;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendToAmoCRM extends Controller
{

    /**
     * @param $DBleadId
     * @return bool
     * Description: The main method, that manage the requests and main stack
     * @throws \JsonException
     */
    public function sendDealToAmoCRM(int $DBleadId) : bool{
        $buildLead = LeadBuilderController::getRow($DBleadId);
        $buildContact = ContactsBuilderController::getRow($buildLead['patID']);
        if ($buildLead && $buildContact){
            $leadRaw=AmoCrmLead::find($DBleadId);
            $client = new Client(['verify' => false]);

            if (!$buildLead['amoContactID']){
                $PresendContact = new ContactsPresendController();
                $contactAmoId = $PresendContact->getAmoID($client, $buildContact);
                $leadRaw->amoContactID = $contactAmoId;
                $leadRaw->save();
            }else{
                $contactAmoId = $buildLead['amoContactID'];
            }


            if (!$buildLead['amoLeadID']){
                $buildLead['amoContactID'] = $contactAmoId;
                $PresendLead = new LeadPresendController();
                $AmoLeadId = $PresendLead->getAmoID($client, $buildLead);
                $leadRaw->amoLeadID  = $AmoLeadId;
                $leadRaw->save();
            }else{
                $AmoLeadId = $buildLead['amoLeadID'];
                $leadPrepared = LeadPrepareController::prepare($buildLead, $contactAmoId);
                $this->sendLead($client, $AmoLeadId, $leadPrepared);
            }
            return true;
            // Эти поля необходимо добавить в JOIN
            //•	Дата визита (дата и время)
            //•	Визит не состоялся (чекбокс/флаг)

            // responsibleFIO - необходимо добавить в карточку сделки
            // С помощью responsible_user_id достаем:
            // ID этого пользователя в AmoCRM
            // FIO этого пользователя в СУБД

            // триггеры
        }
        return false;
    }

    /**
     * @param $client
     * @param $AmoLeadId
     * @param $leadPrepared
     * @return bool
     */
    private function sendLead($client, $AmoLeadId, $leadPrepared) : bool{
        $isUpdate = $AmoLeadId > 0;
        if ($isUpdate){
            $leadPrepared['AmoLeadId'] = $AmoLeadId;
            $res = LeadRequestController::update($client, $leadPrepared);
        }else{
            $res = LeadRequestController::create($client, $leadPrepared);
        }

        try {
            $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
        }catch (\JsonException $e){
            dd($e);
        }
        return true;
    }

    public function closeLead($client, $leadID){
        $leadRaw = AmoCrmLead::find($leadID);
        if ($leadRaw && $leadRaw->amoLeadID){
            $leadArray = [
                'AmoLeadId' => $leadRaw->amoLeadID,
                "name" => "1",
                "closed_at"=> time() + 5,
                "status_id"=> 143,
                "updated_by"=> 0
            ];
            $req = LeadRequestController::update($client, $leadArray);
//            $leadRaw->delete();
        }
    }
}