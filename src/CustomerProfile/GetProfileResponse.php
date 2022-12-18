<?php

namespace PaymentGatewayJson\Client\CustomerProfile;

use PaymentGatewayJson\Client\Json\ResponseObject;

/**
 * Class GetProfileResponse
 *
 * @package PaymentGatewayJson\Client\CustomerProfile
 *
 * @property bool                $profileExists
 * @property string              $profileGuid
 * @property string              $customerIdentification
 * @property string              $preferredMethod
 * @property CustomerData        $customer
 * @property PaymentInstrument[] $paymentInstruments
 */
class GetProfileResponse extends ResponseObject {

}