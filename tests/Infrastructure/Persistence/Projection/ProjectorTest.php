<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\IdentifiesAggregate;
use PHPUnit\Framework\TestCase;
use Buttercup\Protects\DomainEvents;

class ProjectorTest extends TestCase
{



    /**
     * @test
     */
    public function it_should_project_several_events()
    {

        $projector = new Projector();
        $projection = new MockProjection();
        $otherProjection = new OtherMockProjection();
        $projector->register([
            $projection,
            $otherProjection
        ]);

        $domainEvents = new DomainEvents([
            new MockEvent(1),
            new OtherMockEvent(2),
        ]);

        $projector->project($domainEvents);

        $this->assertTrue($projection->isEventProjected(1));
        $this->assertTrue($otherProjection->isEventProjected(2));

    }



    /**
     * @test
     */
    public function it_should_fail_if_no_projections_is_registered()
    {
        $this->expectException(NoProjectionExists::class);

        $projector = new Projector();
        //$projection = new MockProjection();
        //$projector->register();

        $domainEvents = new DomainEvents([
            new MockEvent(1),
        ]);

        $projector->project($domainEvents);
    }

    /**
     * @test
     */
    public function it_should_fail_if_no_projections_is_registered_for_current_event()
    {
        $this->expectException(NoProjectionExists::class);

        $projector = new Projector();
        $projection = new MockProjection();
        $projector->register([$projection]);

        $domainEvents = new DomainEvents([
            new OtherMockEvent(1),
        ]);

        $projector->project($domainEvents);
    }
}

class MockEvent implements DomainEvent {

    private  $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getAggregateId()
    {
        return $this->id;
    }
}
class OtherMockEvent extends MockEvent
{

}

class MockProjection implements Projection
{

    private $projectedEvents = [];

    public function eventType()
    {
        return MockEvent::class;
    }

    public function project($event)
    {
        $this->projectedEvents[] = $event->getAggregateId();
    }

    public function isEventProjected($eventId)
    {
        return in_array($eventId, $this->projectedEvents);
    }
}

class OtherMockProjection extends MockProjection
{

    public function eventType()
    {
        return OtherMockEvent::class;
    }
}