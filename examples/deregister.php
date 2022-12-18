<?php

// include the autoloader
require_once('../autoload.php');

use PaymentGatewayJson\Client\Client;
use PaymentGatewayJson\Client\Data\Customer;
use PaymentGatewayJson\Client\Transaction\Deregister;
use PaymentGatewayJson\Client\Transaction\Result;


//$client = new Client('username', 'password', 'apiKey', 'sharedSecret');
$ini_array = parse_ini_file("config.ini", true);
$client = new Client($ini_array['Credentials']['apiUsername'], $ini_array['Credentials']['apiPassword'], $ini_array['Credentials']['apiKey'], $ini_array['Credentials']['sharedSecret']);

// define your transaction ID: e.g. 'myId-'.date('Y-m-d').'-'.uniqid()
$merchantTransactionId = 'DRG-Test-'.date('Y-m-d').'-'.uniqid(); // must be unique

$deregister = new Deregister();
$deregister->setMerchantTransactionId($merchantTransactionId)
    ->setReferenceUuid($_POST["refTranId"]);

$result = $client->deregister($deregister);
$gatewayReferenceId = $result->getUuid(); //store it in your database

if ($result->getReturnType() == Result::RETURN_TYPE_ERROR) {
    //error handling Sample
    $error = $result->getFirstError();
    $outError = array();
    $outError ["message"] = $error->getMessage();
    $outError ["code"] = $error->getCode();
    $outError ["adapterCode"] = $error->getAdapterCode();
    $outError ["adapterMessage"] = $error->getAdapterMessage();
    header("Location: " . $ini_array['Domain']['myDomainContent'] ."/examples/PaymentNOK.php?" . http_build_query($outError));
    die;
} elseif ($result->getReturnType() == Result::RETURN_TYPE_REDIRECT) { 
    //redirect the user
    header('Location: '.$result->getRedirectUrl());
    die;
} elseif ($result->getReturnType() == Result::RETURN_TYPE_PENDING) {
    //payment is pending, wait for callback to complete

    //setCartToPending();

} elseif ($result->getReturnType() == Result::RETURN_TYPE_FINISHED) {
    //payment is finished, update your cart/payment transaction
    
    header("Location: " . $ini_array['Domain']['myDomainContent'] ."/examples/PaymentOK.php?" . http_build_query($result->toArray()));
    die;
    //finishCart();
}      