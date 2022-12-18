<?php

namespace PaymentGatewayJson\Client\Transaction;

use PaymentGatewayJson\Client\Transaction\Base\AbstractTransactionWithReference;
use PaymentGatewayJson\Client\Transaction\Base\AddToCustomerProfileInterface;
use PaymentGatewayJson\Client\Transaction\Base\AddToCustomerProfileTrait;
use PaymentGatewayJson\Client\Transaction\Base\AmountableInterface;
use PaymentGatewayJson\Client\Transaction\Base\AmountableTrait;
use PaymentGatewayJson\Client\Transaction\Base\CustomerInterface;
use PaymentGatewayJson\Client\Transaction\Base\CustomerTrait;
use PaymentGatewayJson\Client\Transaction\Base\ItemsInterface;
use PaymentGatewayJson\Client\Transaction\Base\ItemsTrait;
use PaymentGatewayJson\Client\Transaction\Base\OffsiteInterface;
use PaymentGatewayJson\Client\Transaction\Base\OffsiteTrait;
use PaymentGatewayJson\Client\Transaction\Base\ScheduleInterface;
use PaymentGatewayJson\Client\Transaction\Base\ScheduleTrait;
use PaymentGatewayJson\Client\Transaction\Base\ThreeDSecureInterface;
use PaymentGatewayJson\Client\Transaction\Base\ThreeDSecureTrait;

/**
 * Debit: Charge the customer for a certain amount of money. This could be once, but also recurring.
 *
 * @package PaymentGatewayJson\Client\Transaction
 */
class Debit extends AbstractTransactionWithReference
            implements AddToCustomerProfileInterface,
                       AmountableInterface,
                       CustomerInterface,
                       ItemsInterface,
                       OffsiteInterface,
                       ScheduleInterface,
                       ThreeDSecureInterface
{

    use AddToCustomerProfileTrait;
    use AmountableTrait;
    use CustomerTrait;
    use ItemsTrait;
    use OffsiteTrait;
    use ScheduleTrait;
    use ThreeDSecureTrait;

    const TRANSACTION_INDICATOR_SINGLE = 'SINGLE';
    const TRANSACTION_INDICATOR_INITIAL = 'INITIAL';
    const TRANSACTION_INDICATOR_RECURRING = 'RECURRING';
    const TRANSACTION_INDICATOR_CARDONFILE = 'CARDONFILE';
    const TRANSACTION_INDICATOR_CARDONFILE_MERCHANT = 'CARDONFILE_MERCHANT';

    /** @var string */
    protected $transactionToken;

    /** @var bool */
    protected $withRegister = false;

    /** @var string */
    protected $transactionIndicator;

    /** @var string */
    protected $language;

    /**
     * @return string
     */
    public function getTransactionToken()
    {
        return $this->transactionToken;
    }

    /**
     * @param string $transactionToken
     */
    public function setTransactionToken($transactionToken)
    {
        $this->transactionToken = $transactionToken;
    }

    /**
     * @return boolean
     */
    public function isWithRegister() {
        return $this->withRegister;
    }

    /**
     * set true if you want to register a user vault together with the debit
     *
     * @param boolean $withRegister
     *
     * @return $this
     */
    public function setWithRegister($withRegister) {
        $this->withRegister = $withRegister;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionIndicator() {
        return $this->transactionIndicator;
    }

    /**
     * @param string $transactionIndicator
     *
     * @return $this
     */
    public function setTransactionIndicator($transactionIndicator) {
        $this->transactionIndicator = $transactionIndicator;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }
}