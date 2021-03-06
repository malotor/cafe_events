<?php

namespace malotor\EventsCafe\Application\Query;

use Doctrine\ORM\EntityRepository;
use malotor\EventsCafe\Application\DataTransformer\DataTranformer;

class OneTabQueryHandler
{
    private $tabsRepository;
    private $dataTransformer;

    public function __construct(
        EntityRepository $tabsRepostiory,
        DataTranformer $dataTransformer
    ) {
        $this->tabsRepository = $tabsRepostiory;
        $this->dataTransformer = $dataTransformer;
    }

    public function handle(OneTabQuery $query)
    {
        $tab = $this->tabsRepository->find($query->id);
        $this->dataTransformer->write($tab);
        return $this->dataTransformer->read();
    }
}