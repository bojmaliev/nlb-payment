<?php

namespace PaymentGatewayJson\Client\Transaction\Base;
use PaymentGatewayJson\Client\Data\Item;

/**
 * Interface ItemsInterface
 *
 * @package PaymentGatewayJson\Client\Transaction\Base
 */
interface ItemsInterface {

    /**
     * @param Item[] $items
     */
    public function setItems($items);

    /**
     * @return Item[]
     */
    public function getItems();

    /**
     * @param Item $item
     * @return void
     */
    public function addItem($item);

}