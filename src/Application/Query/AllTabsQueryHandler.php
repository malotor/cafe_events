<?php

namespace malotor\EventsCafe\Application\Query;

use malotor\EventsCafe\Application\DataTransformer\DataTranformer;

class AllTabsQueryHandler
{
    private $tabsRepository;
    private $dataTransformer;

    public function __construct(
        $tabsRepostiory,
        DataTranformer $dataTransformer
    ) {
        $this->tabsRepository = $tabsRepostiory;
        $this->dataTransformer = $dataTransformer;
    }

    public function handle(AllTabsQuery $query)
    {
        $tabs = $this->tabsRepository->findAll();
        $this->dataTransformer->write($tabs);

        return $this->dataTransformer->read();
    }
}