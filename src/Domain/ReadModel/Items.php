<?php

namespace malotor\EventsCafe\Domain\ReadModel;

/**
 * Items
 */
class Items
{
    /**
     * @var integer
     */
    private $itemId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $price;

    /**
     * @var boolean
     */
    private $is_drink = true;


    /**
     * Set itemId
     *
     * @param integer $itemId
     *
     * @return Items
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return integer
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Items
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Items
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set isDrink
     *
     * @param boolean $isDrink
     *
     * @return Items
     */
    public function setIsDrink($isDrink)
    {
        $this->is_drink = $isDrink;

        return $this;
    }

    /**
     * Get isDrink
     *
     * @return boolean
     */
    public function getIsDrink()
    {
        return $this->is_drink;
    }
}

