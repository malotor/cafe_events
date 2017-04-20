<?php

use Symfony\Component\HttpFoundation\Request;
use malotor\EventsCafe\Domain\Model\Command;
use Symfony\Component\Validator\Constraints as Assert;

use malotor\EventsCafe\Infrastructure\ServiceBus\CommandBus;

$app = new Silex\Application();

$app->register(new Silex\Provider\ValidatorServiceProvider());

/*
$app->view(function (array $controllerResult) use ($app) {
    return $app->json($controllerResult);
});*/


$app['tab_repository'] = $app->share(function($app) {

    return new \malotor\EventsCafe\Infrastructure\Persistence\Domain\Model\InMemoryTabRepository();

});



$app['command_bus'] = $app->share(function($app) {
    $commandBus = new CommandBus();
    $commandBus->register(new Command\OpenTabHandler($app['tab_repository']));

    return $commandBus;
});


$app->error(function (\Exception $e, $code) use ($app) {
    $response = [
        'code' => $code,
        'message' => $e->getMessage(),
    ];
    return $app->json($response,$code);
});

$app->get('/', function(Request $request) use($app) {

    return $app->json([
        'message' => 'It Works!'
    ]);

});

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

    if (count($errors) > 0)
        return $app->json($errors, 500);


    return  [
        'message' => 'Tab has been opened'
    ];



});

return $app;