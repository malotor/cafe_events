<?php

namespace malotor\EventsCafe\Tests\Common;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Client;

class BaseTestCase extends TestCase
{
    protected $crawler;
    protected $client;

    /**
     * HttpKernelInterface instance.
     *
     * @var HttpKernelInterface
     */
    protected $app;

    /**
     * Creates a Client.
     *
     * @param array $server Server parameters
     *
     * @return Client A Client instance
     */
    public function createClient(array $server = [])
    {
        if (!class_exists('Symfony\Component\BrowserKit\Client')) {
            throw new \LogicException('Component "symfony/browser-kit" is required by WebTestCase.' . PHP_EOL . 'Run composer require symfony/browser-kit');
        }

        return new Client($this->app, $server);
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/Infrastructure/ui/web/app.php';
        $app['debug'] = TRUE;
        //$app['env'] = 'test';
        unset($app['exception_handler']);

        return $app;

    }


    protected function request($method, $uri, $params = [])
    {
        $this->crawler = $this->client->request($method, $uri, $params);

        return $this->getResponse();
    }

    protected function getResponse()
    {
        return json_decode($this->client->getResponse()->getContent(), TRUE);
    }

    protected function getResponseStatusCode()
    {
        return $this->client->getResponse()->getStatusCode();
    }

}