<?php


namespace PaymentGatewayJson\Client\Data\Result;

/**
 * Class ResultData
 *
 * @package PaymentGatewayJson\Client\Data\Result
 */
abstract class ResultData {

    /**
     * @return array
     */
    abstract public function toArray();

}