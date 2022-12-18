<?php

// include the autoloader
require_once('../autoload.php');

use PaymentGatewayJson\Client\Client;
use PaymentGatewayJson\Client\StatusApi\StatusRequestData;

$ini_array = parse_ini_file("config.ini", true);

//$client = new Client('username', 'password', 'apiKey', 'sharedSecret', 'language');
$client = new Client($ini_array['Credentials']['apiUsername'], $ini_array['Credentials']['apiPassword'], $ini_array['Credentials']['apiKey'], $ini_array['Credentials']['sharedSecret']);


// create StatusRequestData
$statusRequestData = new StatusRequestData();

$transactionUuid = $_POST["refTranId"]; // the gatewayReferenceId you get by Result->getUuid();

// use either the UUID or your merchantTransactionId but not both
$statusRequestData->setUuid($transactionUuid);

//or
//$merchantTransactionId = 'your_transaction_id';
//$statusRequestData->setMerchantTransactionId($merchantTransactionId);

  $statusResult = $client->sendStatusRequest($statusRequestData);

// handle result
if($statusResult->isSuccess()){
    var_dump($statusResult);

} else{
    echo("Error Message: " . $statusResult->getErrorMessage() . "</BR>");
    echo("Error Code: " . $statusResult->getErrorCode());
}


die();

