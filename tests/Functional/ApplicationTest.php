<?php

namespace malotor\EventsCafe\Tests\Functional;

use Doctrine\ORM\EntityManager;


class ApplicationTest extends Acceptance
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
        $this->request('POST', '/tab', [
            'table'  => '1',
            'waiter' => 'John Doe'
        ]);

        $this->assertEquals(200, $this->getResponseStatusCode());

        $this->request('POST', '/tab', [
            'table'  => '2',
            'waiter' => 'Jane Doe'
        ]);

        $this->assertEquals(200, $this->getResponseStatusCode());
    }

    /**
     * @test
     */
    public function get_tab_info()
    {
        $this->request('POST', '/tab', [
            'table'  => '2',
            'waiter' => 'Jane Doe'
        ]);

        $this->assertTrue($this->client->getResponse()->isOk());


        //$tabId = $response['tab']['id'];

        //$response = $this->request('GET', "/tab/$tabId");

        //$this->assertTrue($this->client->getResponse()->isOk());

        //$this->assertEquals(2, $response['tab']['table']);
        //$this->assertEquals('Jane Doe', $response['tab']['waiter']);
        //$this->assertEquals('open', $response['tab']['status']);
    }

    /**
     * @test
     */
    public function list_all_tabs()
    {
        $this->request('POST', '/tab', [
            'table'  => '1',
            'waiter' => 'John Doe'
        ]);


        $response = $this->request('GET', '/tab');

        $this->assertTrue($this->client->getResponse()->isOk());

        $this->assertEquals(1, $response['tabs'][0]['table']);
        $this->assertEquals('John Doe', $response['tabs'][0]['waiter']);
        $this->assertEquals('open', $response['tabs'][0]['status']);
    }




    /**
     * @test
     */
    public function only_order_items_that_are_in_the_menu()
    {

        $response = $this->request('POST', '/tab', [
            'table'  => '1',
            'waiter' => 'John Doe'
        ]);

        //$response = $this->request('GET', '/tab');
        $tabId = $response['tab']['id'];

        $response = $this->request('POST', "/tab/$tabId", [
            'orderedItems' => [9999]
        ]);

        $this->assertFalse($this->client->getResponse()->isOk());

    }

    /**
     * @test
     */
    public function items_in_the_menu_could_be_ordered()
    {

        $response = $this->request('POST', '/tab', [
            'table'  => '1',
            'waiter' => 'John Doe'
        ]);

        $tabId = $response['tab']['id'];

        $response = $this->request('POST', "/tab/$tabId", [
            'orderedItems' => [1, 2, 5, 6]
        ]);

        $this->assertTrue($this->client->getResponse()->isOk());

        $response = $this->request('GET', '/tab');

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

        $response = $this->request('POST', '/tab', [
            'table'  => '1',
            'waiter' => 'John Doe'
        ]);

        $tabId = $response['tab']['id'];

        $response = $this->request('POST', "/tab/$tabId", [
            'orderedItems' => [1, 2, 5, 6]
        ]);

        $response = $this->request("POST", "/tab/$tabId/prepare", [
            'items' => [1, 2]
        ]);

        $this->assertTrue($this->client->getResponse()->isOk());

        $response = $this->request("GET", "/tab/$tabId");

        $this->assertEquals('Pizza', $response['tab']['outstanding_foods'][0]);
        $this->assertEquals('Pizza', $response['tab']['prepared_foods'][0]);

    }

    /**
     * @test
     */
    public function drinks_could_be_served()
    {

        $response = $this->request('POST', '/tab', [
            'table'  => '1',
            'waiter' => 'John Doe'
        ]);

        $tabId = $response['tab']['id'];

        $response = $this->request('POST', "/tab/$tabId", [
            'orderedItems' => [1, 2, 5, 6]
        ]);
        $response = $this->request("POST", "/tab/$tabId/prepare", [
            'items' => [1, 2]
        ]);
        $response = $this->request("GET", "/tab/$tabId/serve", [1,2,5,6]);

    }

}
