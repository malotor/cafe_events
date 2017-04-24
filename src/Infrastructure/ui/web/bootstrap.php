<?php

use Symfony\Component\HttpFoundation\Request;
use malotor\EventsCafe\Domain\Model\Command;
use Symfony\Component\Validator\Constraints as Assert;

use malotor\EventsCafe\Infrastructure\Persistence\EventStore\PDOEventStore;
use malotor\EventsCafe\Infrastructure\Persistence\Projection\TabProjection;
use malotor\EventsCafe\Infrastructure\Persistence\Domain\Model\TabEventSourcingRepository;

use JMS\Serializer\SerializerBuilder;

use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;


$app = new Silex\Application();

$app->register(new Silex\Provider\ValidatorServiceProvider());
$app['env'] = 'dev';


$app['pdo.sqlite.file'] = function () {
   return new \PDO(
        'sqlite:' . __DIR__ . '/../../../../resources/db/events_cafe.db',
        null,
        null,
        array(\PDO::ATTR_PERSISTENT => true)
    );
};

$app['pdo.sqlite.inmemory'] = function () {
    $pdo = new \PDO(
        'sqlite:::memory:',
        null,
        null,
        array(\PDO::ATTR_PERSISTENT => true)
    );
    //Provision
    $sql = file_get_contents(__DIR__ . '/../../../../resources/db/events_cafe.sql');
    $pdo->exec($sql);
    return $pdo;
};

$app['pdo'] = function ($app) {
    switch ($app['env']) {
        case 'test':
            return $app['pdo.sqlite.inmemory'];
            break;
        default:
            return $app['pdo.sqlite.file'];
            break;
    }
};

$app['serializer'] = function () {
    return SerializerBuilder::create()
        ->addMetadataDir(__DIR__ . '/../../../../resources/serializer')
        ->build();
};

$app['tab_repository'] = function($app) {

    $tabProjection = new TabProjection($app['pdo']);
    $eventStore = new PDOEventStore($app['pdo'], $app['serializer']);
    return new TabEventSourcingRepository($eventStore, $tabProjection);
};



$app['command_bus'] = function($app) {

    $locator = new InMemoryLocator();
    $locator->addHandler(
        new Command\OpenTabHandler($app['tab_repository']),
        Command\OpenTabCommand::class)
    ;

    $handlerMiddleware = new League\Tactician\Handler\CommandHandlerMiddleware(
        new ClassNameExtractor(),
        $locator,
        new HandleClassNameInflector()
    );
    return new \League\Tactician\CommandBus([$handlerMiddleware]);
};

$app['entity_manager'] =  function ($app) {

    $isDevMode = true;
    $config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/../../../../resources/doctrine"), $isDevMode);

    // database configuration parameters
    $conn = array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../../../../resources/db/events_cafe.db',
    );

    // obtaining the entity manager
    return EntityManager::create($conn, $config);
};



// APPLICATION REST

$app->error(function (\Exception $e, $code) use ($app) {
    $response = [
        'code' => $code,
        'message' => $e->getMessage(),
    ];
    return $app->json($response,500);
});

$app->get('/', function(Request $request) use($app) {

    return $app->json([
        'message' => 'It Works!'
    ]);

});

// CONTROLLERS

$app->post('/tab', function(Request $request) use($app) {


    $data = [
        'table' =>  $request->request->get("table"),
        'waiter' => $request->request->get("table")
    ];

    $constraint = new Assert\Collection(array(
        'table' => new Assert\NotBlank(),
        'waiter' => new Assert\NotBlank(),
    ));

    $errors = $app['validator']->validate($data, $constraint);

    $command = new Command\OpenTabCommand();
    $command->tableNumber = $data['table'];
    $command->waiterId = $data['waiter'];
    $app['command_bus']->handle($command);

    if (count($errors) > 0)
        return $app->json($errors, 500);

    return   $app->json([
        'message' => 'Tab has been opened'
    ]);

});

return $app;