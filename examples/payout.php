<?php

// include the autoloader
require_once('../autoload.php');

use PaymentGatewayJson\Client\Client;
use PaymentGatewayJson\Client\Data\Customer;
use  PaymentGatewayJson\Client\Data\PaymentData\IbanData;
use PaymentGatewayJson\Client\Transaction\Payout;
use PaymentGatewayJson\Client\Transaction\Result;


$ini_array = parse_ini_file("config.ini", true);

//$client = new Client('username', 'password', 'apiKey', 'sharedSecret', 'language');
$client = new Client($ini_array['Credentials']['apiUsername'], $ini_array['Credentials']['apiPassword'], $ini_array['Credentials']['apiKey'], $ini_array['Credentials']['sharedSecret']);

// define your transaction ID: e.g. 'myId-'.date('Y-m-d').'-'.uniqid()
$merchantTransactionId = 'PO-'.date('Y-m-d').'-'.uniqid(); // must be unique

$payout = new Payout();

//Payout for BIPS (Instant payments)
/*
$pymD = new IbanData();
$pymD->setIban("IBAN");
$pymD->setBic("BIC");

$customer = new Customer();
$customer->setPaymentData($pymD);
$payout->setCustomer($customer);
*/
//END Payout for BIPS (Instant payments)

$payout
 ->setMerchantTransactionId($merchantTransactionId)
 ->setAmount((float)$_POST["Amount"])
 ->setCurrency($_POST["Currency"])
 ->setCallbackUrl($ini_array['Domain']['myDomainContent'].'/examples/Callback.php');

//Payout with alias for flik payment
if(isset($_POST["flikAlias"])){
    $payout->addExtraData('alias', $_POST["flikAlias"]);
}
//END Payout with alias for flik payment


$result = $client->payout($payout);

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
    //(Flik) payment is pending, wait for callback to complete
    echo("<html><body style=\"background-color: #CCAACC;\">");
    echo("<br><p style=\"text-align:center\">Your payment has been submitted for authorization.</p>");
    echo("<p style=\"text-align:center\">The result will be send on callbackURL as soon as will be processed.</p>"); 
    echo("<p style=\"text-align:center\">The transaction UUID number is:  <span style= \"color:red\">". $result->getUuid()."</span></p>");
    echo("</body></html>");
    //setCartToPending();

} elseif ($result->getReturnType() == Result::RETURN_TYPE_FINISHED) {
    //payment is finished, update your cart/payment transaction
    
    header("Location: " . $ini_array['Domain']['myDomainContent'] ."/examples/PaymentOK.php?" . http_build_query($result->toArray()));
    die;
    //finishCart();
}      