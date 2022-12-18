<?php

namespace PaymentGatewayJson\Client\Transaction\Base;
use PaymentGatewayJson\Client\Data\Customer;

/**
 * Interface CustomerInterface
 *
 * @package PaymentGatewayJson\Client\Transaction\Base
 */
interface CustomerInterface {

    /**
     * @return Customer
     */
    public function getCustomer();

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer);

}