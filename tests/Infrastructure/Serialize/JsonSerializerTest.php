<?php

namespace malotor\EventsCafe\Infrastructure\Serialize;

use JMS\Serializer\EventDispatcher\Event;
use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItem;
use PHPUnit\Framework\TestCase;

use malotor\EventsCafe\Domain\Model\Events;
use malotor\EventsCafe\Domain\Model\Aggregate\TabId;
use Symfony\Component\EventDispatcher\Tests\EventTest;

class JsonSerializerTest extends TestCase
{

    private $serializer;

    public function setUp()
    {
        $this->serializer = new JsonSerializer(__DIR__ . '/../../../resources/serializer');
    }

    /**
     * @test
     */
    public function it_should_serialize_tab_opened_event()
    {

        $tabId = TabId::create();
        $anEvent = new Events\TabOpened($tabId,1,'John Doe');

        $serializedEvent = $this->serializer->serialize($anEvent);

        /** @var Events\TabOpened $unserializedEvent */
        $unserializedEvent = $this->serializer->deserialize($serializedEvent, 'malotor\EventsCafe\Domain\Model\Events\TabOpened' );

        $this->assertEquals($tabId, $unserializedEvent->getAggregateId());
        $this->assertEquals('John Doe', $unserializedEvent->getWaiterId());
        $this->assertEquals(1, $unserializedEvent->getTableNumber());

    }

    /**
     * @test
     */
    public function it_should_serialize_drinks_ordered_event()
    {

        $tabId = TabId::create();
        $anEvent = new Events\DrinksOrdered($tabId,
        [
            new OrderedItem(1,true,10),
            new OrderedItem(2,true,11),
        ]
        );

        $serializedEvent = $this->serializer->serialize($anEvent);

        /** @var Events\DrinksOrdered $unserializedEvent */
        $unserializedEvent = $this->serializer->deserialize($serializedEvent, 'malotor\EventsCafe\Domain\Model\Events\DrinksOrdered' );

        $this->assertEquals($tabId, $unserializedEvent->getAggregateId());
        $items = $unserializedEvent->getItems();
        $this->assertEquals(1, $items[0]->getMenuNumber());
        $this->assertTrue($items[0]->isDrink());
        $this->assertEquals(10, $items[0]->getPrice());

    }

    /**
     * @test
     */
    public function it_should_serialize_foods_ordered_event()
    {

        $tabId = TabId::create();
        $anEvent = new Events\FoodOrdered($tabId,
            [
                new OrderedItem(1,false,10)
            ]
        );

        $serializedEvent = $this->serializer->serialize($anEvent);

        /** @var Events\FoodOrdered $unserializedEvent */
        $unserializedEvent = $this->serializer->deserialize($serializedEvent, 'malotor\EventsCafe\Domain\Model\Events\FoodOrdered' );

        $this->assertEquals($tabId, $unserializedEvent->getAggregateId());
        $items = $unserializedEvent->getItems();
        $this->assertEquals(1, $items[0]->getMenuNumber());
        $this->assertFalse($items[0]->isDrink());
        $this->assertEquals(10, $items[0]->getPrice());

    }

    /**
     * @test
     */
    public function it_should_serialize_food_prepared_event()
    {

        $tabId = TabId::create();
        $anEvent = new Events\FoodPrepared($tabId,[1,10]);

        $serializedEvent = $this->serializer->serialize($anEvent);

        $unserializedEvent = $this->serializer->deserialize($serializedEvent, 'malotor\EventsCafe\Domain\Model\Events\FoodPrepared' );

        $this->assertEquals($tabId, $unserializedEvent->getAggregateId());
        $items = $unserializedEvent->getItems();
        $this->assertEquals(1, $items[0]);
        $this->assertEquals(10, $items[1]);

    }
}
