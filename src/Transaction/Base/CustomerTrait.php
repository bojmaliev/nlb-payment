<?php

namespace PaymentGatewayJson\Client\Transaction\Base;

use PaymentGatewayJson\Client\Data\CreditCardCustomer;
use PaymentGatewayJson\Client\Data\Customer;
use PaymentGatewayJson\Client\Data\IbanCustomer;

/**
 * Class ThreeDSecureTrait
 *
 * @package PaymentGatewayJson\Client\Transaction\Base
 */
trait CustomerTrait {

    /** @var Customer */
    protected $customer;

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * with backward compatibility for IbanCustomer/CreditCardCustomer
     * @param IbanCustomer|CreditCardCustomer|Customer $customer
     *
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

}