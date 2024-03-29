<?php

namespace Tests\Feature;

use App\Http\Controllers\Contacts\ContactsController;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testCreateContactWithCalled(): void
    {
        $client = new Client(['verify'=>false]);
        $ContactController = new ContactsController($client);
        $patient = $ContactController->getRow(1, true);
        $preparedContact = $ContactController->prepare($patient);
        $response = $ContactController->create($preparedContact);
        $this->assertEquals(200,$response->getStatusCode());
    }

    public function testCreateContactWithUnCalled(): void
    {
        $client = new Client(['verify'=>false]);
        $ContactController = new ContactsController($client);
        $patient = $ContactController->getRow(1);
        $preparedContact = $ContactController->prepare($patient);
        $response = $ContactController->create($preparedContact);
        $this->assertEquals(200,$response->getStatusCode());
    }
}
