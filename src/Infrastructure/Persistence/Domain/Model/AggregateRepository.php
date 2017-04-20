<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Domain\Model;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\IsEventSourced;
use Buttercup\Protects\RecordsEvents;

use CQRSBlog\BlogEngine\DomainModel\Post;
use CQRSBlog\BlogEngine\DomainModel\PostProjection;
use CQRSBlog\BlogEngine\DomainModel\PostRepository as BasePostRepository;
use malotor\EventsCafe\Domain\Model\Aggregate\Tab;

class AggregateRepository
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var PostProjection
     */
    private $projection;

    public function __construct($eventStore, $postProjection)
    {
        $this->eventStore = $eventStore;
        $this->postProjection = $postProjection;
    }

    /**
     * @param IdentifiesAggregate $aggregateId
     * @return IsEventSourced
     */
    public function get(IdentifiesAggregate $aggregateId)
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return Tab::reconstituteFrom($eventStream);
    }

    /**
     * @param RecordsEvents $aggregate
     * @return void
     */
    public function add(RecordsEvents $aggregate)
    {
        $events = $aggregate->getRecordedEvents();
        $this->eventStore->commit($events);
        $this->postProjection->project($events);

        $aggregate->clearRecordedEvents();
    }
}