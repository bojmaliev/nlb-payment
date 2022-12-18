<?php

// include the autoloader
require_once('../autoload.php');

use PaymentGatewayJson\Client\Client;
use PaymentGatewayJson\Client\Data\Customer;
use PaymentGatewayJson\Client\Transaction\Preauthorize;
use PaymentGatewayJson\Client\Transaction\Result;
use PaymentGatewayJson\Client\Schedule\ScheduleData;
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
// Use gateway schadule or merchant send a sub sequent transaction in period.
//true-use gateway schadule
//false-merchant create own schadule
$gatewaySchadule = 'off';
if(isset($_POST["gatewaySchadule"])){
    $gatewaySchadule = $_POST["gatewaySchadule"]=== 'on'? 'on': 'off';
}
//Initial transaction with stored card(prewious or now)
$initialStoreTrans = 'No';
if(isset($_POST["initialStoreTrans"])){
    $initialStoreTrans = $_POST["initialStoreTrans"];
}
//Sub-sequent transaction with stored card
$subSeqentTrans = 'No';
if(isset($_POST["subSeqentTrans"])){
    $subSeqentTrans = $_POST["subSeqentTrans"];
}
// Schadule in case of Recurring transaction
$scheduleUnit = 'DAY';
if(isset($_POST["scheduleUnit"])){
    $scheduleUnit = $_POST["scheduleUnit"];
}
$schedulePeriod = '1';
if(isset($_POST["schedulePeriod"])){
    $schedulePeriod = $_POST["schedulePeriod"];
}
$scheduleDelay = '';
if(isset($_POST["scheduleDelay"])){
    $scheduleDelay = $_POST["scheduleDelay"];
}
$refTranId='';
if(isset($_POST["refTranId"])){
    $refTranId = $_POST["refTranId"];
}
$amount = '0';
if(isset($_POST["Amount"])){
    $amount = $_POST["Amount"];
}
$currency = 'EUR';
if(isset($_POST["Currency"])){
    $currency = $_POST["Currency"];
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
    
//add further customer details if necessary

// define your transaction ID: e.g. 'myId-'.date('Y-m-d').'-'.uniqid()
$merchantTransactionId = 'P-'.date('Y-m-d').'-'.uniqid(); // must be unique

$preauth = new Preauthorize();
$preauth->setMerchantTransactionId($merchantTransactionId)
    ->setAmount($amount)
    ->setCurrency($currency)
    ->setCallbackUrl($ini_array['Domain']['myDomainContent'].'/examples/Callback.php?MID='.$merchantTransactionId)
    ->setSuccessUrl($ini_array['Domain']['myDomainContent'].'/examples/PaymentOK.php?MID='.$merchantTransactionId)
    ->setErrorUrl($ini_array['Domain']['myDomainContent'].'/examples/PaymentNOK.php?MID='.$merchantTransactionId)
    ->setCancelUrl($ini_array['Domain']['myDomainContent'].'/examples/PaymentCancel.php?MID='.$merchantTransactionId)
    ->setDescription('One pair of shoes')
    ->setMerchantMetaData("Transaction:Preauthorize;Description:test")
    ->setCustomer($customer);

// Add Extra data 
if(isset($_POST["numInstalment"])){
    $preauth->addExtraData('userField1',$_POST["numInstalment"]);  //  If you have an agreement with your acquiring banks to offer payments in installments, 
                                      //userField1 is used and becomes mandatory. In such cases send 00 or 01 when no installments are selected. 
                                      //In case of an invalid value, the payment will be declined.
}

//Add 3-D Secure elements
$threeDSdata= new ThreeDSecureData(); 

//if token acquired via payment.js
if (isset($token)) {
    $preauth->setTransactionToken($token);
}

switch ($initialStoreTrans){
    case "No":
        switch($subSeqentTrans){
            case "No":  //normal Preauthorize
                $preauth->setWithRegister (false)
                    ->setTransactionIndicator('SINGLE');
                break;
            case "subSeqentCoF": //subsequent CoF - normal transaction with stored card
                $preauth->setReferenceUuid($refTranId)
                    ->setTransactionIndicator('CARDONFILE');
                break;

            case "subSeqentRec":    //subsequent Recurring - Note: If jou send schedule on initialization
                //you don’t need to do that.
                echo("Sub-sequent Recurring with 'Preauthorize' is not possible! Instead Preauthorize us Sub-sequent 'Debit'");
                die;
                break;

            case "subSeqentMIT": //subsequent MIT
                echo("Sub-sequent MIT with 'Preauthorize' is not possible! Instead Preauthorize us Sub-sequent 'Debit'");
                die;
                break;
        }
        break;

    case "initialCoF": // Preauthorize & store card for future use
        $threeDSdata->setAuthenticationIndicator('04');//04-add card
        $preauth->setWithRegister (true)
            ->setTransactionIndicator('SINGLE')
            ->setThreeDSecureData($threeDSdata);
        break;

    case "initialRec":
        if(strlen($refTranId) > 0){ //Recurring establish with already stored card
            $preauth->setReferenceUuid($refTranId);
        }
        $threeDSdata->setAuthenticationIndicator('02') //02-recurring+MIT
            ->setRecurringFrequency(2); //!1->Recurring; no connections with $schedulePeriod
        $preauth->setWithRegister (true)
            ->setTransactionIndicator('INITIAL')
            ->setThreeDSecureData($threeDSdata);
        //if gateway do a sub-sequent transactions
        if($gatewaySchadule == 'on'){
            //create schedular
            $myScheduleData = new ScheduleData();
            $myScheduleData -> setPeriodUnit($scheduleUnit) // The units are 'DAY','WEEK','MONTH','YEAR'  
                -> setPeriodLength($schedulePeriod)
                -> setAmount($amount)
                -> setCurrency($currency);
                
            //Delay for first sub sequent transaction
            if (strlen($scheduleDelay) > 0){ //if dellay for first sub-sequent transaction is set
                $date =new DateTime("now", new DateTimeZone('UTC'));
                $date ->modify($_POST["scheduleDelay"]);
                $myScheduleData -> setStartDateTime($date);
            }

            //add Schedular to debit transaction
            $preauth -> setSchedule($myScheduleData);
        }

        break;

    case "initialMIT": //Preauthorize with MIT establishe
        if(strlen($refTranId) > 0){ //MIT establish with already stored card
            $preauth->setReferenceUuid($refTranId);
        }
        $threeDSdata->setAuthenticationIndicator('02') //02-recurring+MIT
            ->setRecurringFrequency(1);//1->MIT
        $preauth->setWithRegister (true)
            ->setTransactionIndicator('INITIAL')
            ->setThreeDSecureData($threeDSdata);
        break;

}


$result = $client->preauthorize($preauth);

// handle the result
if ($result->isSuccess()) {

    // store the uuid you receive from the gateway for future references
    $gatewayReferenceId = $result->getUuid();

    if ($result->getReturnType() == Result::RETURN_TYPE_ERROR) {
        //error handling Sample
        // - In a case of usage of payment.JS without 3DS (frictionless transactions) the ERROR/DECLINE result for end customer will posted here as response on request »$result = $client->preauthorize($preauth);«
        // merchant need to redirect end-user on merchant's error page to show result. Also other method to show result are allowed.
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
        // -  in a case of usage of payment.JS + 3DS or HPP end customer is redirected to the card issuer's site/HPP.
        // -  in a case of usage of payment.JS without 3DS this never happen
        header('Location: '.$result->getRedirectUrl());
        die;
    } elseif ($result->getReturnType() == Result::RETURN_TYPE_PENDING) {
        //payment is pending, wait for callback to complete
        // not for credit card transactions
        //setCartToPending();
    } elseif ($result->getReturnType() == Result::RETURN_TYPE_FINISHED) {
        //payment is finished, update your cart/payment transaction
        // - In a case of usage of payment.JS without 3DS (frictionless transactions) the SUCCESS result for end customer will posted here as response on request »$result = $client->debit($debit);«
        // merchant need to redirect end-user on merchant's succes page to show result. Also other method to show result are allowed.
        // - In a case of usage of payment.JS + 3DS or HPP this never happen. We (payment gateway) redirect the end-user to merchant's error/success/cancel URL sent in initial API call

        header("Location: " . $ini_array['Domain']['myDomainContent'] . "/examples/PaymentOK.php?" . http_build_query($result->toArray()));
        die;
        //finishCart();
    }   
} else{
    // handle the error
    // e.g. cancelCart();
    //error handling Sample
    
    // -This part of code will happens at generic errors(invalid credentials,..)
    // merchant need to redirect end-user on merchant's error page to show result. Also other method to show result are allowed.
    $error = $result->getFirstError();
    $outError = array();  
    $outError ["message"] = $error->getMessage();
    $outError ["code"] = $error->getCode();
    $outError ["adapterCode"] = $error->getAdapterCode();
    $outError ["adapterMessage"] = $error->getAdapterMessage();
    header("Location: " . $ini_array['Domain']['myDomainContent'] ."/examples/PaymentNOK.php?" . http_build_query($outError));
    die;

}    