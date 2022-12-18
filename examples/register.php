<?php
// include the autoloader
require_once('../autoload.php');

use PaymentGatewayJson\Client\Client;
use PaymentGatewayJson\Client\Data\Customer;
use PaymentGatewayJson\Client\Transaction\Register;
use PaymentGatewayJson\Client\Transaction\Result;
use PaymentGatewayJson\Client\Data\ThreeDSecureData;


//get customers IP
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//PajmentJS indicator
$token = null;
if(isset($_POST["transaction_token"])){
    $token = $_POST["transaction_token"];
}

$ini_array = parse_ini_file("config.ini", true);

//$client = new Client('username', 'password', 'apiKey', 'sharedSecret', 'language');
$client = new Client($ini_array['Credentials']['apiUsername'], $ini_array['Credentials']['apiPassword'], $ini_array['Credentials']['apiKey'], $ini_array['Credentials']['sharedSecret'], $_POST["Language"]);

$customer = new Customer(); 
$customer
    ->setFirstName($_POST["first_name"])
    ->setLastName($_POST["last_name"])
    ->setEmail($_POST["email"])
    ->setIpAddress(getRealIpAddr())
    ->setBillingAddress1('Street 1')
    ->setBillingCity('City')
    ->setBillingPostcode('1000')    
    ->setBillingCountry('SI');  

// define your transaction ID: e.g. 'myId-'.date('Y-m-d').'-'.uniqid()
$merchantTransactionId = 'RG-'.date('Y-m-d').'-'.uniqid(); // must be unique

//Add 3-D Secure elements
$threeDSdata= new ThreeDSecureData(); 
$threeDSdata->setAuthenticationIndicator('04'); //04-add card

$register = new Register();
$register->setMerchantTransactionId($merchantTransactionId)
    ->setCallbackUrl($ini_array['Domain']['myDomainContent'].'/examples/Callback.php?MID='.$merchantTransactionId)
    ->setSuccessUrl($ini_array['Domain']['myDomainContent'].'/examples/PaymentOK.php?MID='.$merchantTransactionId)
    ->setErrorUrl($ini_array['Domain']['myDomainContent'].'/examples/PaymentNOK.php?MID='.$merchantTransactionId)
    ->setCancelUrl($ini_array['Domain']['myDomainContent'].'/examples/PaymentCancel.php?MID='.$merchantTransactionId)
    ->setDescription('Register transaction')
    ->setMerchantMetaData("Stranka:Janez;naslov=Kr neki")
    ->setCustomer($customer)
    ->setThreeDSecureData($threeDSdata);

//if token acquired via payment.js   
if (isset($token)){
    $register ->setTransactionToken($token);
}

$result = $client->register($register);
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