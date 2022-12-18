<?php

// include the autoloader
require_once('../autoload.php');

use PaymentGatewayJson\Client\Client;
use PaymentGatewayJson\Client\Callback\Result;

$ini_array = parse_ini_file("config.ini", true);

//$client = new Client('username', 'password', 'apiKey', 'sharedSecret', 'language');
$client = new Client($ini_array['Credentials']['apiUsername'], $ini_array['Credentials']['apiPassword'], $ini_array['Credentials']['apiKey'], $ini_array['Credentials']['sharedSecret']);


//if you want to check signature uncoment if statement 
/*
if ($client->validateCallbackWithGlobals() == false){
    //the signature is incorrect. It is your decision what to do
    
}
*/

$callbackResult = $client->readCallback(file_get_contents('php://input'));  
$status = $callbackResult->getResult();
// handle callback data
$myTransactionId = $callbackResult->getMerchantTransactionId();
$gatewayTransactionId = $callbackResult->getUuid();

if ($status === Result::RESULT_OK) {

    // payment ok
    $callbackResult->getResult();
    // finishCart();

} elseif ($status === Result::RESULT_ERROR) {

    //payment failed, handle errors
    // $callbackResult->getErrorMessage();
    // $callbackResult->getErrorCode();
    // $callbackResult->getAdapterMessage();
    // $callbackResult->getAdapterCode();
    $errors = $callbackResult->getErrors();
}

// confirm callback with body "OK"
echo "OK";
die;