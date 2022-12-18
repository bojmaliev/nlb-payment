<?php

namespace PaymentGatewayJson\Client\Transaction\Base;
use PaymentGatewayJson\Client\Data\ThreeDSecureData;

/**
 * Interface ThreeDSecureInterface
 *
 * @package PaymentGatewayJson\Client\Transaction\Base
 */
interface ThreeDSecureInterface {

    /**
     * @return ThreeDSecureData
     */
    public function getThreeDSecureData();

    /**
     * @param ThreeDSecureData $threeDSecureData
     *
     * @return mixed
     */
    public function setThreeDSecureData($threeDSecureData);

}