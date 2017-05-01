<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

class OrderedItem
{
    private $menuNumber;
    private $IsDrink;
    private $price;

    public function __construct(int $menuNumber, bool $IsDrink, float $price)
    {
        $this->menuNumber = $menuNumber;
        $this->IsDrink = $IsDrink;
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getMenuNumber(): int
    {
        return $this->menuNumber;
    }

    /**
     * @return bool
     */
    public function isDrink(): bool
    {
        return $this->IsDrink;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }


}