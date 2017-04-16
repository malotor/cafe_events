<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

class OrderedItem
{
    private $menuNumber;
    private $description;
    private $IsDrink;
    private $price;


    public function __construct(int $menuNumber, $description, bool $IsDrink, float $price)
    {
        $this->menuNumber = $menuNumber;
        $this->description = $description;
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
     * @return mixed
     */
    public function getDescription(): string 
    {
        return $this->description;
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