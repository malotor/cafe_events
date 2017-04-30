<?php

namespace malotor\EventsCafe\Tests\Functional;

use Doctrine\ORM\EntityManager;


class ApplicationTest extends AcceptanceTest
{


    protected function setUp()
    {
        $this->app = $this->createApplication();
        /** @var EntityManager $em */
        $em = $this->app['entity_manager'];

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $tool->dropDatabase();

        $classes = array(
            $em->getClassMetadata('malotor\EventsCafe\Domain\ReadModel\Tabs'),
            $em->getClassMetadata('malotor\EventsCafe\Domain\ReadModel\Items'),
        );
        $tool->createSchema($classes);

        $connection = $em->getConnection();
        $connection->exec(file_get_contents(__DIR__.'/../../resources/db/events_cafe_fixtures.sql'));

        $predisClient = new \Predis\Client('tcp://redis:6379');
        $predisClient->flushall();

        $this->client = $this->createClient();

    }

    /**
     * @test
     */
    public function basic_page()
    {
        $this->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isOk());
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
            'table' => '1',
            'waiter' => 'John Doe'
        ]);

        $this->assertEquals(200, $this->getResponseStatusCode());
    }



    /**
     * @test
     */
    public function list_all_tabs()
    {
        $this->request('POST', '/tab', [
            'table' => '1',
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

        $this->request('POST', '/tab', [
            'table' => '1',
            'waiter' => 'John Doe'
        ]);

        $response = $this->request('GET', '/tab');
        $tabId = $response['tabs'][0]['id'];

        $response = $this->request('POST', "/tab/$tabId",[
            'orderedItems' => [9999]
        ]);

        $this->assertFalse($this->client->getResponse()->isOk());

    }

    /**
     * @test
     */
    public function items_in_the_menu_could_be_ordered()
    {

        $this->request('POST', '/tab', [
            'table' => '1',
            'waiter' => 'John Doe'
        ]);

        $response = $this->request('GET', '/tab');
        $tabId = $response['tabs'][0]['id'];

        $response = $this->request('POST', "/tab/$tabId",[
            'orderedItems' => [1,2,5,6]
        ]);

        $this->assertTrue($this->client->getResponse()->isOk());

        $response = $this->request('GET', '/tab');

        var_dump($response['tabs'][0]);
        $this->assertEquals('Beer', $response['tabs'][0]['outstanding_drinks'][0]);
        $this->assertEquals('Ice tea', $response['tabs'][0]['outstanding_drinks'][1]);

        $this->assertEquals('Pizza', $response['tabs'][0]['outstanding_foods'][0]);
        $this->assertEquals('Hamburger', $response['tabs'][0]['outstanding_foods'][1]);

    }

}
