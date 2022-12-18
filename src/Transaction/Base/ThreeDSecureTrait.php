<?php

namespace PaymentGatewayJson\Client\Transaction\Base;

use PaymentGatewayJson\Client\Data\ThreeDSecureData;

/**
 * Class ThreeDSecureTrait
 *
 * @package PaymentGatewayJson\Client\Transaction\Base
 */
trait ThreeDSecureTrait {

    /** @var ThreeDSecureData */
    protected $threeDSecureData;

    /**
     * @return ThreeDSecureData
     */
    public function getThreeDSecureData()
    {
        return $this->threeDSecureData;
    }

    /**
     * @param ThreeDSecureData $threeDSecureData
     *
     * @return $this
     */
    public function setThreeDSecureData($threeDSecureData)
    {
        $this->threeDSecureData = $threeDSecureData;
        return $this;
    }

}