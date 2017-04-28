<?php

namespace malotor\EventsCafe\Tests\Infrastructure\Persistence\Projection;

use Buttercup\Protects\DomainEvents;
use malotor\EventsCafe\Domain\Model\Aggregate\TabId;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;
use malotor\EventsCafe\Infrastructure\Persistence\Projection\TabProjection;
use PHPUnit\Framework\TestCase;

class TabProjectionTest extends TestCase
{
    private $pdo;

    public function setUp()
    {
        $this->pdo = new \PDO(
            'sqlite::memory:',
            null,
            null,
            array(\PDO::ATTR_PERSISTENT => true)
        );

        $sql = file_get_contents(__DIR__ . '/../../../../resources/db/events_cafe.sql');
        $this->pdo->exec($sql);


    }

    /**
     * @test
     */
    public function it_should_project_tab_opened()
    {
        $projection = new TabProjection($this->pdo);

        $eventStream = new DomainEvents([
            new TabOpened(TabId::fromString("e247721a-b70c-4431-9ba5-1867340ae241"),1,'John')
        ]);

        $projection->project($eventStream);


        $stm = $this->pdo->query("SELECT * FROM tabs where tab_id = 'e247721a-b70c-4431-9ba5-1867340ae241'");
        $result = $stm->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals('e247721a-b70c-4431-9ba5-1867340ae241', $result['tab_id']);
        $this->assertEquals(1, $result['tableNumber']);
        $this->assertEquals('John', $result['waiter']);


    }
}
