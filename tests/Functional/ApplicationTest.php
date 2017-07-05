<?php

namespace malotor\EventsCafe\Tests\Functional;

use Doctrine\ORM\EntityManager;
use malotor\EventsCafe\Tests\Common\BaseTestCase;
use Ramsey\Uuid\Uuid;

class ApplicationTest extends BaseTestCase
{


    protected function setUp()
    {
        $this->app = $this->createApplication();
        /** @var EntityManager $em */
        $em = $this->app['entity_manager'];

        $connection = $em->getConnection();
        $schemaManager = $connection->getSchemaManager();
        $tables = $schemaManager->listTables();

        $platform   = $connection->getDatabasePlatform();

        foreach($tables as $table) {
            $name = $table->getName();
            $connection->executeUpdate($platform->getTruncateTableSQL($name, true ));
        }

        $connection->exec(file_get_contents(__DIR__ . '/../../resources/db/events_cafe_fixtures.sql'));

        $predisClient = new \Predis\Client('tcp://redis:6379');
        $predisClient->flushall();

        $this->client = $this->createClient();

    }


    /**
     * @test
     */
    public function tab_should_be_openned_by_a_waiter_for_a_table()
    {
        $this->request('POST', '/tab', [
            'foo' => 'bar'
        ]);

        $this->assertEquals(500, $this->getResponseStatusCode());
    }

    /**
     * @test
     */
    public function a_waiter_could_open_new_tab_for_a_table()
    {
        $this->openTab(1,'John Doe');
        $this->assertEquals(200, $this->getResponseStatusCode());
        $this->openTab(2,'Jane Doe');
        $this->assertEquals(200, $this->getResponseStatusCode());
    }

    /**
     * @test
     */
    public function get_tab_info()
    {
        $tabId = $this->openTab(1,'John Doe');
        $this->assertTrue($this->client->getResponse()->isOk());
        $response = $this->getTab($tabId);
        $this->assertTrue($this->client->getResponse()->isOk());

        $this->assertEquals(1, $response['tab']['table']);
        $this->assertEquals('John Doe', $response['tab']['waiter']);
        $this->assertEquals('open', $response['tab']['status']);
    }

    /**
     * @test
     */
    public function list_all_tabs()
    {
        $tabId = $this->openTab(1,'John Doe');
        $response = $this->getTabs();

        $this->assertEquals(1, $response['tabs'][0]['table']);
        $this->assertEquals('John Doe', $response['tabs'][0]['waiter']);
        $this->assertEquals('open', $response['tabs'][0]['status']);
    }




    /**
     * @test
     */
    public function only_order_items_that_are_in_the_menu()
    {

        $tabId = $this->openTab(1,'John Doe');
        $this->request('POST', "/tab/$tabId", [
            'orderedItems' => [9999]
        ]);
        $this->assertFalse($this->client->getResponse()->isOk());

    }

    /**
     * @test
     */
    public function items_in_the_menu_could_be_ordered()
    {

        $tabId = $this->openTab(1,'John Doe');

        $this->placeOrder($tabId, [1, 2, 5, 6]);

        $response = $this->getTabs();

        //var_dump($response['tabs'][0]);
        $this->assertEquals('Beer', $response['tabs'][0]['outstanding_drinks'][0]);
        $this->assertEquals('Ice tea', $response['tabs'][0]['outstanding_drinks'][1]);

        $this->assertEquals('Pizza', $response['tabs'][0]['outstanding_foods'][0]);
        $this->assertEquals('Hamburger', $response['tabs'][0]['outstanding_foods'][1]);

    }


    /**
     * @test
     */
    public function outstanding_foods_could_be_prepared()
    {

        $tabId = $this->openTab(1,'John Doe');

        $this->placeOrder($tabId, [1, 2, 5, 6]);

        $this->prepareFood($tabId, [1,2]);

        $response = $this->getTab($tabId);

        $this->assertEquals('Pizza', $response['tab']['outstanding_foods'][0]);
        $this->assertEquals('Pizza', $response['tab']['prepared_foods'][0]);

    }

    /**
     * @test
     */
    public function outstanding_drinks_could_be_served()
    {

        $tabId = $this->openTab(1,'John Doe');

        $this->placeOrder($tabId, [1, 2, 5, 6]);
        $this->markDrinksServed($tabId, [5,6]);

        $response = $this->getTab($tabId);

        $this->assertEquals('Beer', $response['tab']['served_items'][0]);
        $this->assertEquals('Ice tea', $response['tab']['served_items'][1]);
    }



    /**
     * @test
     */
    public function prepared_food_could_be_served()
    {

        $tabId = $this->openTab(1,'John Doe');

        $this->placeOrder($tabId, [1, 2, 5, 6]);
        $this->prepareFood($tabId, [2]);
        $this->markFoodServed($tabId,[2]);
        $response = $this->getTab($tabId);

        $this->assertEquals('Hamburger', $response['tab']['served_items'][0]);
        //$this->assertEquals('Hamburger', $response['tab']['served_items'][1]);
    }

    /**
     * @test
     */
    public function tab_without_standing_items_could_be_paid()
    {
        $tabId = $this->openTab(1,'John Doe');

        $this->placeOrder($tabId, [1, 2, 5, 6]);
        $this->markDrinksServed($tabId, [5,6]);
        $this->prepareFood($tabId, [1, 2]);
        $this->markFoodServed($tabId, [1,2]);
        $this->closeTab($tabId, 23);

        $response = $this->getTab($tabId);

        $this->assertEquals(23, $response['tab']['amountPaid']);
        $this->assertEquals(21, $response['tab']['orderValue']);
        $this->assertEquals(2, $response['tab']['tipValue']);
    }


    protected function openTab($table, $waiter) : string
    {

        $response = $this->request('POST', '/tab', [
            'table'  => '1',
            'waiter' => 'John Doe'
        ]);

        return $response['tab']['id'];

    }

    protected function placeOrder($tabId, $items)
    {
        $this->request('POST', "/tab/$tabId", [
            'orderedItems' => $items
        ]);
        $this->assertTrue($this->client->getResponse()->isOk());

    }

    protected function prepareFood($tabId, $items)
    {
        $this->request("POST", "/tab/$tabId/prepare", [ 'items' => $items ]);
        $this->assertTrue($this->client->getResponse()->isOk());

    }

    protected function markFoodServed($tabId, $items)
    {
        $this->request("POST", "/tab/$tabId/mark_food_served", [ 'items' => $items ]);
        $this->assertTrue($this->client->getResponse()->isOk());

    }

    protected function markDrinksServed($tabId, $items)
    {
        $this->request("POST", "/tab/$tabId/mark_drinks_served", [ 'items' => $items ]);
        $this->assertTrue($this->client->getResponse()->isOk());

    }

    protected function closeTab($tabId, $amount)
    {
        $this->request("POST", "/tab/$tabId/paid", [ 'amount' => $amount ]);
        $this->assertTrue($this->client->getResponse()->isOk());

    }

    protected function getTab($tabId)
    {
        $response = $this->request("GET", "/tab/$tabId");
        $this->assertTrue($this->client->getResponse()->isOk());

        return $response;
    }

    protected function getTabs()
    {
        $response = $this->request("GET", "/tab");
        $this->assertTrue($this->client->getResponse()->isOk());

        return $response;
    }
}
