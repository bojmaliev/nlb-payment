<?php

namespace PaymentGatewayJson\Client\Transaction;

use PaymentGatewayJson\Client\Transaction\Base\AbstractTransactionWithReference;
use PaymentGatewayJson\Client\Transaction\Base\AmountableInterface;
use PaymentGatewayJson\Client\Transaction\Base\AmountableTrait;
use PaymentGatewayJson\Client\Transaction\Base\ItemsInterface;
use PaymentGatewayJson\Client\Transaction\Base\ItemsTrait;

/**
 * Capture: Charge a previously preauthorized transaction.
 *
 * @package PaymentGatewayJson\Client\Transaction
 */
class Capture extends AbstractTransactionWithReference implements AmountableInterface, ItemsInterface {
    use AmountableTrait;
    use ItemsTrait;
}