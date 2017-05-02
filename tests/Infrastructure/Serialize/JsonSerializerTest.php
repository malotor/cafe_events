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
     * @dataProvider events
     */
    public function it_should_serialize_events($anEvent)
    {
        /*
        $tabId = TabId::create();
        $anEvent = new Events\FoodServed($tabId,[1,10]);
        */
        $serializedEvent = $this->serializer->serialize($anEvent);
        $unserializedEvent = $this->serializer->deserialize($serializedEvent, get_class($anEvent) );
        $this->assertEquals($anEvent, $unserializedEvent);

    }

    public function events()
    {
        $tabId = TabId::create();
        return [
            [ new Events\TabOpened($tabId,1,'John Doe') ],
            [ new Events\DrinksOrdered($tabId,
                [
                    new OrderedItem(1,true,10),
                    new OrderedItem(2,true,11),
                ]
            )],
            [ new Events\FoodOrdered($tabId,
                [
                    new OrderedItem(1,false,10)
                ]
            )],
            [ new Events\FoodPrepared($tabId,[1,10]) ],
            [ new Events\DrinksServed($tabId,[1,10]) ],
            [ new Events\FoodServed($tabId,[1,10]) ],
            [ new Events\TabClosed($tabId,10,11,1)]
        ];
    }
}
