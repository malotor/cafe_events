<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

use Buttercup\Protects\AggregateRoot;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\RecordsEvents;

use malotor\Common\Events\DomainEventPublisher;
use Ramsey\Uuid\Uuid;
use Verraes\ClassFunctions\ClassFunctions;

/**
 * @codeCoverageIgnore
 */
abstract class Aggregate implements AggregateRoot
{

    protected $id;

    /**
     * @var DomainEvent[]
     */
    private $recordedEvents = [];

    public function getAggregateId() : Uuid
    {
        return $this->id;
    }

    public function hasChanges() : bool
    {
        // TODO: Implement hasChanges() method.
    }

    /**
     * Get all the Domain Events that were recorded since the last time it was cleared, or since it was
     * restored from persistence. This does not include events that were recorded prior.
     *
     * @return DomainEvents
     */
    public function getRecordedEvents() : DomainEvents
    {
        return new DomainEvents($this->recordedEvents);
    }

    /**
     * Clears the record of new Domain Events. This doesn't clear the history of the object.
     * @return void
     */
    public function clearRecordedEvents()
    {
        $this->recordedEvents = [];
    }

    protected function recordThat(DomainEvent $aDomainEvent)
    {
        $this->recordedEvents[] = $aDomainEvent;
    }

    protected function applyAndRecordThat(DomainEvent $aDomainEvent)
    {
        $this->recordThat($aDomainEvent);

        $this->apply($aDomainEvent);
    }

    /**
     * Allow to reconstitute an aggregate from an aggregate events history and an initial state
     *
     * @param AggregateHistory $anAggregateHistory
     *
     * @return RecordsEvents
     */
    public static function reconstituteFrom(AggregateHistory $anAggregateHistory)
    {
        // TODO: Implement
        /*
        $aPost = static::createEmptyPostWith($anAggregateHistory->getAggregateId());

        foreach ($anAggregateHistory as $anEvent) {
            $aPost->apply($anEvent);
        }

        return $aPost;
        */
    }

    private function apply($anEvent)
    {
        $method = 'apply' . ClassFunctions::short($anEvent);
        $this->$method($anEvent);
    }

    protected function publishThat( $domainEvent) {
        DomainEventPublisher::instance()->publish($domainEvent);
    }


}