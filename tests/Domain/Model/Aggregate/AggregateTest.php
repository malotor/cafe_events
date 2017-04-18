<?php

namespace malotor\EventsCafe\Tests\Domain\Model\Aggregate;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\IdentifiesAggregate;
use malotor\EventsCafe\Domain\Model\Aggregate\Aggregate;
use malotor\EventsCafe\Domain\Model\Aggregate\AggregateId;
use malotor\EventsCafe\Domain\Model\Aggregate\tab;
use PHPUnit\Framework\TestCase;

class AggregateTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_be_created_with_have_an_id()
    {
        $anAggregate = FooAggreate::createEmptyWithId(FooAggregateID::create());
        $this->assertInstanceOf(AggregateId::class, $anAggregate->getAggregateId());
    }

    /**
     * @test
     */
    public function it_should_be_created_from_an_existing_id()
    {
        $anAggregate = FooAggreate::createEmptyWithId(FooAggregateID::create());
        $this->assertInstanceOf(AggregateId::class, $anAggregate->getAggregateId());
    }


    /**
     * @test
     */
    public function it_should_record_events()
    {

        $anAggregate = FooAggreate::createEmptyWithId(FooAggregateID::create());

        $anAggregate->applyAndRecordThat(new BarIncremented($anAggregate->getAggregateId(), 1) );

        $events = $anAggregate->getRecordedEvents();

        $this->assertCount(1,$events);
        $this->assertInstanceOf(BarIncremented::class, $events[0]);
        $this->assertEquals($anAggregate->getAggregateId(), ($events[0])->getAggregateId());

    }

    /**
     * @tests
     */
    public function it_should_be_reconstituted_from_events()
    {
        $aggregateId = FooAggregateID::fromString("adb40bf1-e79c-442e-ae7c-3c9cfcdd38f1");

        $eventHistory = new AggregateHistory($aggregateId, [
            new BarIncremented($aggregateId, 1),
            new BarIncremented($aggregateId, 3)
        ]);


        $anAggregate = FooAggreate::reconstituteFrom($eventHistory);

        $this->assertEquals("adb40bf1-e79c-442e-ae7c-3c9cfcdd38f1",(string) $anAggregate->getAggregateId());
        $this->assertEquals(4, $anAggregate->getBar());
    }

}


class FooAggreate extends Aggregate
{

    private $bar = 0;

    public function __construct(FooAggregateID $id)
    {
        $this->setAggregateId($id);
    }

    static public function createEmptyWithId( $id): Aggregate
    {
        return new FooAggreate($id);
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function applyAndRecordThat(DomainEvent $event)
    {
        return parent::applyAndRecordThat($event);
    }

    public function applyBarIncremented(BarIncremented $barIncremented)
    {
        $this->bar += $barIncremented->getBarNewValue();
    }
}

class FooAggregateID  extends AggregateId {}

class BarIncremented implements DomainEvent
{

    private $id;
    private $barNewValue;

    /**
     * CustomEvent constructor.
     *
     * @param $id
     */
    public function __construct(FooAggregateID $id, $barNewValue)
    {
        $this->id = $id;
        $this->barNewValue = $barNewValue;
    }


    public function getAggregateId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getBarNewValue()
    {
        return $this->barNewValue;
    }


}