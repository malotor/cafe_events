<?php

namespace malotor\EventsCafe\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\HttpKernel\Client;


class ApplicationTest extends TestCase
{

    /**
     * HttpKernelInterface instance.
     *
     * @var HttpKernelInterface
     */
    protected $app;

    /**
     * PHPUnit setUp for setting up the application.
     *
     * Note: Child classes that define a setUp method must call
     * parent::setUp().
     */
    protected function setUp()
    {
        $this->app = $this->createApplication();
        $em = $this->app['entity_manager'];

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        //$tool->createSchema($classes);

        $tool->dropDatabase();

        $classes = array(
            $em->getClassMetadata('malotor\EventsCafe\Domain\ReadModel\Tabs'),
            $em->getClassMetadata('malotor\EventsCafe\Domain\ReadModel\Items'),
        );
        $tool->createSchema($classes);


        $client = new \Predis\Client('tcp://redis:6379');
        $client->flushall();
    }



    /**
     * Creates a Client.
     *
     * @param array $server Server parameters
     *
     * @return Client A Client instance
     */
    public function createClient(array $server = array())
    {
        if (!class_exists('Symfony\Component\BrowserKit\Client')) {
            throw new \LogicException('Component "symfony/browser-kit" is required by WebTestCase.'.PHP_EOL.'Run composer require symfony/browser-kit');
        }

        return new Client($this->app, $server);
    }

    public function createApplication()
    {
        // TODO: Implement createApplication() method.

        $app = require __DIR__.'/../../src/Infrastructure/ui/web/app.php';
        $app['debug'] = true;
        //$app['env'] = 'test';
        unset($app['exception_handler']);

        return $app;

    }

    /**
     * @test
     */
    public function basic_page()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
    }

    /**
     * @test
     */
    public function open_new_tab_require_valid_parameters()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/tab', [
            'foo' => 'bar'
        ]);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function open_new_tab()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/tab', [
            'table' => '1',
            'waiter' => 'John Doe'
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }



    /**
     * @test
     */
    public function list_open_tabs()
    {
        $app = $this->app;

        $client = $this->createClient();

        $client->request('POST', '/tab', [
            'table' => '1',
            'waiter' => 'John Doe'
        ]);

        $crawler = $client->request('GET', '/tab');

        $this->assertTrue($client->getResponse()->isOk());
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(1, $response['tabs'][0]['table']);
        $this->assertEquals('John Doe', $response['tabs'][0]['waiter']);

    }

    /**
     * @test
     */
    public function only_order_items_that_are_in_the_menu()
    {
        $app = $this->app;

        $client = $this->createClient();

        $client->request('POST', '/tab', [
            'table' => '1',
            'waiter' => 'John Doe'
        ]);

        $crawler = $client->request('GET', '/tab');
        $response = json_decode($client->getResponse()->getContent(), true);
        $tabId = $response['tabs'][0]['id'];

        $crawler = $client->request('POST', "/tab/$tabId",[
            'orderedItems' => [5]
        ]);

        $this->assertFalse($client->getResponse()->isOk());

    }
}
