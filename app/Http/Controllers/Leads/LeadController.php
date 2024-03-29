<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class LeadController extends Controller
{
    protected LeadPrepareController $LeadPrepareController;
    protected LeadPresendController $LeadPresendController;
    protected LeadRequestController $LeadRequestController;
    protected LeadBuilderController $LeadBuilderController;
    private Client $client;

    public function __construct(Client $client){
        $this->LeadPrepareController = new LeadPrepareController();
        $this->LeadBuilderController = new LeadBuilderController();
        $this->LeadPresendController = new LeadPresendController();
        $this->LeadRequestController = new LeadRequestController();
        $this->client = $client;
    }

    public function closeLead(int $amoLeadID) : array{
        return $this->LeadBuilderController->closeLead($amoLeadID);
    }

    public function finishLead(int $amoLeadID) : array{
        return $this->LeadBuilderController->finishLead($amoLeadID);
    }

    public function prepare(array $amoLeadDB, int $contactID) : array{
        return $this->LeadPrepareController->prepare($amoLeadDB,$contactID);
    }

    public function getAmoID(array $DBLead){
        $leadID = $this->LeadPresendController->getAmoID($this->client,$DBLead);
        if (!$leadID){
            $leadID = $this->LeadRequestController->create(
                $this->client,
                $this->prepare($DBLead,$DBLead['amoContactID'])
            );
        }
        return $leadID;
    }

    public function create(array $preparedData, int $status_id = 61034286) : int{
        return $this->LeadRequestController->create($this->client,$preparedData,$status_id);
    }

    public function update(array $preparedData): Response|array
    {
        return $this->LeadRequestController->update($this->client,$preparedData);
    }

    public function get(string $query){
        return $this->LeadRequestController::get($this->client,$query);
    }

}
