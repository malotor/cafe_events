<?php

require_once __DIR__ . "/../bootstrap.php";

// unidirectional many to many
$repository = $entityManager->getRepository('malotor\EventsCafe\Domain\ReadModel\Tabs');

/** @var \malotor\EventsCafe\Domain\ReadModel\Tabs $tab */
$tab = $repository->find('eeb7f45a-b206-444b-adc9-ebd8ca3f40a8');

var_dump($tab->getTabId());

$drinks = $tab->getOutstandingDrinks();

foreach ($drinks as $d)
{
    /** @var \malotor\EventsCafe\Domain\ReadModel\Items $d */
    var_dump($d->getDescription());
}

$foods = $tab->getOutstandingFoods();

foreach ($foods as $f)
{
    /** @var \malotor\EventsCafe\Domain\ReadModel\Items $f */
    var_dump($f->getDescription());
}