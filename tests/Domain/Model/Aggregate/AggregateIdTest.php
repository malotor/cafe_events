<?php

namespace malotor\EventsCafe\Tests\Domain\Model\Aggregate;

use malotor\EventsCafe\Domain\Model\Aggregate\AggregateId;
use PHPUnit\Framework\TestCase;

class AggregateIdTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_be_generated()
    {
        $id = CustomAggreateId::create();
        $this->assertInstanceOf(CustomAggreateId::class, $id);
    }

    /**
     * @test
     */
    public function it_shoudl_be_created_from_an_uuid()
    {
        $anId = CustomAggreateId::fromString("adb40bf1-e79c-442e-ae7c-3c9cfcdd38f1");

        $this->assertEquals("adb40bf1-e79c-442e-ae7c-3c9cfcdd38f1", (string) $anId);
    }

    /**
     * @tedst
     */
    public function it_should_be_a_value_object()
    {
        $anId = CustomAggreateId::fromString("adb40bf1-e79c-442e-ae7c-3c9cfcdd38f1");
        $otherId = CustomAggreateId::fromString("adb40bf1-e79c-442e-ae7c-3c9cfcdd38f1");

        $this->assertTrue($anId->equals($otherId));
        $this->assertFalse($anId->equals(CustomAggreateId::create()));
    }
}

class CustomAggreateId extends AggregateId {}