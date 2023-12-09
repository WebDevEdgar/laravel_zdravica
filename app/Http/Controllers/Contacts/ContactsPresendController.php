<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PrepareEntityController;

class ContactsPresendController extends Controller
{

    /**
     * @param $client
     * @param $contactDB
     * @return int
     * Description: return the AmoCRM contact ID
     */
    public function getAmoID($client, $contactDB) : int{
        $contactID = $this->checkExists($client, $contactDB);
        if (!$contactID){
            $contactPrepared = ContactsPrepareController::prepare($contactDB);
            $contactID = $this->createAmo($client, $contactPrepared);
        }
        return $contactID;
    }

    /**
     * @param $client
     * @param $contact
     * @return string|object
     * Description: Get the Contact ID using the request
     */
    private function checkExists($client, $contact) : string|object{
        $query = '?query='.$contact['NOM'].' '.$contact['EMAIL'];
        $result = ContactsRequestController::get($client, $query);
        if ($result && $result['_embedded']){
            return $result['_embedded']['contacts'][0]['id'];
        }
        return '';
    }

    /**
     * @param $client
     * @param $preparedContact
     * @return string|int
     * Description: returns the ID of AmoCRM contact
     */
    private function createAmo($client, $preparedContact) : string|int{
        $res = ContactsRequestController::create($client, $preparedContact);
        $result = $res->getBody() !== '' ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';;
        if (isset($result) && $result['_embedded']){
            return $result['_embedded']['contacts'][0]['id'];
        }
        return '';
    }
}
