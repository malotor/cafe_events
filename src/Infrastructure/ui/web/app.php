<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use malotor\EventsCafe\Application\Command;
use malotor\EventsCafe\Application\Query;
use malotor\EventsCafe\Infrastructure\Persistence\Domain\Model\TabEventSourcingRepository;
use malotor\EventsCafe\Infrastructure\Persistence\EventStore\RedisEventStore;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

use malotor\EventsCafe\Infrastructure\Persistence\Projection;

$app = new Silex\Application();

$app->register(new Silex\Provider\ValidatorServiceProvider());
$app['env'] = 'dev';
$app['base_path'] = __DIR__ . '/../../../..';

// APPLICATION REST

$app->error(function (\Exception $e, $code) use ($app) {
    $response = [
        'message' => $e->getMessage(),
    ];

    return $app->json($response, 500);
});

// SERVICE CONTAINER

$app['pdo.sqlite.file'] = function ($app) {
    return new \PDO('sqlite:' . $app['base_path'] . '/resources/db/events_cafe.db',
        null, null, [\PDO::ATTR_PERSISTENT => true]);
};

$app['pdo.sqlite.inmemory'] = function ($app) {
    $pdo = new \PDO('sqlite:::memory:');
    //Provision
    $sql = file_get_contents($app['base_path'] . '/resources/db/events_cafe.sql');
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

$app['serializer'] = function ($app) {
    return new \malotor\EventsCafe\Infrastructure\Serialize\JsonSerializer($app['base_path'] . '/resources/serializer');
};
$app['projector'] = function ($app) {

    $projector = new \malotor\EventsCafe\Infrastructure\Persistence\Projection\Projector();

    $projector->register([
        new Projection\TabOpenedProjection($app['pdo']),
        new Projection\DrinksOrderedProjection($app['pdo']),
        new Projection\DrinksServedProjection($app['pdo']),
        new Projection\FoodOrderedProjection($app['pdo']),
        new Projection\FoodPreparedProjection($app['pdo']),
        new Projection\FoodServedProjection($app['pdo']),
        new Projection\TabClosedProjection($app['pdo']),
    ]);

    return $projector;
};

$app['tab_repository'] = function ($app) {
    $client = new \Predis\Client('tcp://redis:6379');
    $eventStore = new RedisEventStore($client, $app['serializer']);
    return new TabEventSourcingRepository($eventStore, $app['projector']);
};

$app['command_bus'] = function ($app) {

    $locator = new InMemoryLocator();
    $locator->addHandler(new Command\OpenTabHandler($app['tab_repository']),
        Command\OpenTabCommand::class);

    $locator->addHandler(new Command\PlaceOrderHandler($app['tab_repository'],
        $app['ordered_items_repository']), Command\PlaceOrderCommand::class);

    $locator->addHandler(new Command\PrepareFoodHandler($app['tab_repository']),
        Command\PrepareFoodCommand::class);

    $locator->addHandler(new Command\MarkDrinksServedHandler($app['tab_repository']),
        Command\MarkDrinksServedCommand::class);

    $locator->addHandler(new Command\MarkFoodServedHandler($app['tab_repository']),
        Command\MarkFoodServedCommand::class);

    $locator->addHandler(new Command\CloseTabHandler($app['tab_repository']),
        Command\CloseTab::class);

    $handlerMiddleware = new League\Tactician\Handler\CommandHandlerMiddleware(new ClassNameExtractor(),
        $locator,
        new \malotor\EventsCafe\Infrastructure\CommandBus\CustomInflector());

    return new \League\Tactician\CommandBus([$handlerMiddleware]);
};

$app['query_bus'] = function ($app) {

    $locator = new InMemoryLocator();

    $locator->addHandler(new Query\AllTabsQueryHandler($app['tab_view_repository'],
        new \malotor\EventsCafe\Application\DataTransformer\TabToArrayDataTransformer()),
        Query\AllTabsQuery::class);

    $locator->addHandler(new Query\OneTabQueryHandler($app['tab_view_repository'],
        new \malotor\EventsCafe\Application\DataTransformer\TabToArrayDataTransformer()),
        Query\OneTabQuery::class);

    $handlerMiddleware = new League\Tactician\Handler\CommandHandlerMiddleware(new ClassNameExtractor(),
        $locator,
        new \malotor\EventsCafe\Infrastructure\CommandBus\CustomInflector());

    return new \League\Tactician\CommandBus([$handlerMiddleware]);
};



$app['entity_manager'] = function ($app) {

    $isDevMode = true;
    $config = Setup::createYAMLMetadataConfiguration([$app['base_path'] . '/resources/doctrine'],
        $isDevMode);

    switch ($app['env']) {
        case 'test':
            // database configuration parameters
            $conn = [
                'driver' => 'pdo_sqlite',
                'memory' => true
            ];
            break;
        default:
            // database configuration parameters
            $conn = [
                'driver' => 'pdo_sqlite',
                'path'   => $app['base_path'] . '/resources/db/events_cafe.db',
            ];
            break;
    }

    return EntityManager::create($conn, $config);
};

$app['tab_view_repository'] = function ($app) {
    /** @var EntityManager $em */
    $em = $app['entity_manager'];

    return $em->getRepository('malotor\EventsCafe\Domain\ReadModel\Tabs');
};

$app['ordered_items_repository'] = function ($app) {
    /** @var EntityManager $em */
    $em = $app['entity_manager'];

    return new \malotor\EventsCafe\Infrastructure\Persistence\Domain\Model\DoctrineOrderedItemRepository($em);
};


// CONTROLLERS

$app->get('/', function (Request $request) use ($app) {

    return $app->json([
        'message' => 'It Works!'
    ]);

});


$app->post('/tab', function (Request $request) use ($app) {


    $data = [
        'table'  => $request->request->get("table"),
        'waiter' => $request->request->get("waiter")
    ];

    $constraint = new Assert\Collection([
        'table'  => new Assert\NotBlank(),
        'waiter' => new Assert\NotBlank(),
    ]);

    $errors = $app['validator']->validate($data, $constraint);

    if (count($errors) > 0) {
        return $app->json($errors, 500);
    }

    $command = new Command\OpenTabCommand();
    $command->tabId = \Ramsey\Uuid\Uuid::uuid4();
    $command->tableNumber = $data['table'];
    $command->waiterId = $data['waiter'];
    $app['command_bus']->handle($command);

    return $app->json([
        'tab' => [
            'id' => $command->tabId,
        ]
    ]);

});

$app->get('/tab', function (Request $request) use ($app) {

    $response = $app['query_bus']->handle(new \malotor\EventsCafe\Application\Query\AllTabsQuery());

    return $app->json([
        'tabs' => $response
    ]);

});

$app->get('/tab/{id}', function (Request $request, $id) use ($app) {

    $query = new Query\OneTabQuery($id);
    $response = $app['query_bus']->handle($query);

    return $app->json([
        'tab' => $response
    ]);

});

$app->post('/tab/{id}', function (Request $request, $id) use ($app) {

    $items = $request->request->get("orderedItems");

    $command = new Command\PlaceOrderCommand($id, $items);
    $app['command_bus']->handle($command);

    return $app->json([
        'message' => 'Order has placed'
    ]);

});


$app->post('/tab/{id}/prepare', function (Request $request, $id) use ($app) {

    $items = $request->request->get("items");

    $command = new Command\PrepareFoodCommand($id, $items);
    $app['command_bus']->handle($command);

    return $app->json([
        'message' => 'Food has been prepared'
    ]);

});

$app->post('/tab/{id}/mark_drinks_served', function (Request $request, $id) use ($app) {

    $items = $request->request->get("items");

    $command = new Command\MarkDrinksServedCommand($id, $items);
    $app['command_bus']->handle($command);

    return $app->json([
        'message' => 'Drinks has been served'
    ]);

});

$app->post('/tab/{id}/mark_food_served', function (Request $request, $id) use ($app) {

    $items = $request->request->get("items");

    $command = new Command\MarkFoodServedCommand($id, $items);
    $app['command_bus']->handle($command);

    return $app->json([
        'message' => 'Food has been served'
    ]);

});

$app->post('/tab/{id}/paid', function (Request $request, $id) use ($app) {

    $amount = $request->request->get("amount");

    $command = new Command\CloseTab($id, $amount);
    $app['command_bus']->handle($command);

    return $app->json([
        'message' => 'Food has been served'
    ]);

});

return $app;