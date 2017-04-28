<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = include __DIR__ . '/../src/Infrastructure/ui/web/app.php';

$app->run();