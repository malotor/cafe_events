<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\EventStore;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IdentifiesAggregate;
use malotor\EventsCafe\Infrastructure\Serialize\Serializer;
use Predis\Client;

class RedisEventStore implements EventStore
{
    /**
     * @var Client
     */
    private $predis;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct($predis, Serializer $serializer)
    {
        $this->predis = $predis;
        $this->serializer = $serializer;
    }

    public function commit(DomainEvents $events)
    {
        foreach ($events as $event) {
            $eventType = get_class($event);
            $data = $this->serializer->serialize($event);

            $this->predis->rpush($this->computeHashFor($event->getAggregateId()),
                $this->serializer->serialize([
                    'type'       => $eventType,
                    'created_on' => (new \DateTimeImmutable())->format('YmdHis'),
                    'data'       => $data
                ]));
        }
    }

    private function computeHashFor(IdentifiesAggregate $anAggregateId)
    {
        return sprintf('events:%s', $anAggregateId);
    }

    public function getAggregateHistoryFor(IdentifiesAggregate $id)
    {
        $serializedEvents = $this->predis->lrange($this->computeHashFor($id), 0,
            -1);

        $eventStream = [];

        foreach ($serializedEvents as $serializedEvent) {
            $eventData = $this->serializer->deserialize($serializedEvent,
                'array');
            $eventStream[] = $this->serializer->deserialize($eventData['data'],
                $eventData['type']);
        }

        return new AggregateHistory($id, $eventStream);
    }
}