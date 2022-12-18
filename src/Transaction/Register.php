<?php

namespace PaymentGatewayJson\Client\Transaction;

use PaymentGatewayJson\Client\Transaction\Base\AbstractTransaction;
use PaymentGatewayJson\Client\Transaction\Base\AddToCustomerProfileInterface;
use PaymentGatewayJson\Client\Transaction\Base\AddToCustomerProfileTrait;
use PaymentGatewayJson\Client\Transaction\Base\CustomerInterface;
use PaymentGatewayJson\Client\Transaction\Base\CustomerTrait;
use PaymentGatewayJson\Client\Transaction\Base\OffsiteInterface;
use PaymentGatewayJson\Client\Transaction\Base\OffsiteTrait;
use PaymentGatewayJson\Client\Transaction\Base\ScheduleInterface;
use PaymentGatewayJson\Client\Transaction\Base\ScheduleTrait;
use PaymentGatewayJson\Client\Transaction\Base\ThreeDSecureInterface;
use PaymentGatewayJson\Client\Transaction\Base\ThreeDSecureTrait;

/**
 * Register: Register the customer's payment data for recurring charges.
 *
 * The registered customer payment data will be available for recurring transaction without user interaction.
 *
 * @package PaymentGatewayJson\Client\Transaction
 */
class Register extends AbstractTransaction
               implements AddToCustomerProfileInterface,
                          CustomerInterface,
                          OffsiteInterface,
                          ScheduleInterface,
                          ThreeDSecureInterface
{

    use AddToCustomerProfileTrait;
    use CustomerTrait;
    use OffsiteTrait;
    use ScheduleTrait;
    use ThreeDSecureTrait;

    /** @var string */
    protected $language;

    /** @var string */
    protected $transactionToken;

    /**
     * @var string
     */
    protected $transactionIndicator;

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

    
    /**
     * @return string
     */
    public function getTransactionIndicator() {
        return $this->transactionIndicator;
    }

    /**
     * @param string $transactionIndicator
     */
    public function setTransactionIndicator($transactionIndicator) {
        $this->transactionIndicator = $transactionIndicator;
    }
}