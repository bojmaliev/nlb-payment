<?php

// include the autoloader
require_once('../autoload.php');

use PaymentGatewayJson\Client\Client;
use PaymentGatewayJson\Client\Data\Customer;
use PaymentGatewayJson\Client\Transaction\Debit;
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


// define relevant objects
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
       
// define your transaction ID
// must be unique! e.g.
$merchantTransactionId = 'D-'.date('Y-m-d').'-'.uniqid();

// define transaction relevant object
$debit = new Debit();
$debit->setMerchantTransactionId($merchantTransactionId)
    ->setAmount($amount)
    ->setCurrency($currency)
    ->setCallbackUrl($ini_array['Domain']['myDomainContent'].'/examples/callback.php?MID='.$merchantTransactionId)
    ->setSuccessUrl($ini_array['Domain']['myDomainContent'].'/examples/paymentOK.php?MID='.$merchantTransactionId)
    ->setErrorUrl($ini_array['Domain']['myDomainContent'].'/examples/paymentNOK.php?MID='.$merchantTransactionId)
    ->setCancelUrl($ini_array['Domain']['myDomainContent'].'/examples/paymentCancel.php?MID='.$merchantTransactionId)
    ->setDescription($_POST["descr"])
    ->setMerchantMetaData("Transaction:Debit;Description:test")
    ->setCustomer($customer);

// Add Extra data 
if(isset($_POST["numInstalment"])){
    $debit->addExtraData('userField1',$_POST["numInstalment"]);  //  If you have an agreement with your acquiring banks to offer payments in installments, 
                                       //userField1 is used and becomes mandatory. In such cases send 00 or 01 when no installments are selected. 
                                       //In case of an invalid value, the payment will be declined.
 }

//alias for flik payment
 if(isset($_POST["flikAlias"])){
    $debit->addExtraData('alias', $_POST["flikAlias"]);
 }

//Add 3-D Secure elements
$threeDSdata= new ThreeDSecureData(); 

//if token acquired via payment.js   
if (isset($token)){
    $debit ->setTransactionToken($token);
}
switch ($initialStoreTrans){
    case "No":
        switch($subSeqentTrans){
            case "No":  //normal debit
                $debit->setWithRegister (false)
                    ->setTransactionIndicator('SINGLE');
                break;
            case "subSeqentCoF": //subsequent CoF - normal transaction with stored card
                $debit->setReferenceUuid($refTranId)
                    ->setTransactionIndicator('CARDONFILE');
                break;
            case "subSeqentRec":    //subsequent Recurring - Note: If jou send schedule on initialization
                //you don’t need to do that.
                $debit->setReferenceUuid($refTranId)
                    ->setTransactionIndicator('RECURRING');
                break;

            case "subSeqentMIT": //subsequent MIT
                if (strlen($refTranId) == 0)
                {
                    echo("For Sub-sequent MIT you need enter ReferenceTransactionID of Initial MIT transaction!");
                    return;
                }
                $debit->setReferenceUuid($refTranId)
                    ->setTransactionIndicator('CARDONFILE-MERCHANT-INITIATED');
                break;
        }
        break;

    case "initialCoF": // debit & store card for future use
        $threeDSdata->setAuthenticationIndicator('04');//04-add card
        $debit->setWithRegister (true)
            ->setTransactionIndicator('SINGLE')
            ->setThreeDSecureData($threeDSdata);
        break;

    case "initialRec":
        if(floatval($amount)== 0){
            echo("Initial recurring is not possible with amount:".$amount);
            die;
        }
        if(strlen($refTranId) > 0){ //Recurring establish with already stored card
            $debit->setReferenceUuid($refTranId);
        }
        $threeDSdata->setAuthenticationIndicator('02') //02-recurring+MIT
            ->setRecurringFrequency(2); //!1->Recurring; no connections with $schedulePeriod
        $debit->setWithRegister (true)
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
            $debit -> setSchedule($myScheduleData);
        }
        break;

    case "initialMIT": //debit with MIT establishe
        if(floatval($amount)== 0){
            echo("Initial MIT is not possible with amount:".$amount);
            die;
        }
        if(strlen($refTranId) > 0){ //MIT establish with already stored card
            $debit->setReferenceUuid($refTranId);
        }
        $threeDSdata->setAuthenticationIndicator('02') //02-recurring+MIT
            ->setRecurringFrequency(1);//1->MIT
        $debit->setWithRegister (true)
            ->setTransactionIndicator('INITIAL')
            ->setThreeDSecureData($threeDSdata);
        break;
}

// send the transaction
$result = $client->debit($debit);

// handle the result
if ($result->isSuccess()) {

    // store the uuid you receive from the gateway for future references
    $gatewayReferenceId = $result->getUuid();

    // handle result based on it's returnType
    if ($result->getReturnType() === Result::RETURN_TYPE_ERROR) {
        //error handling Sample
        // - In a case of usage of payment.JS without 3DS (frictionless transactions) the ERROR/DECLINE result for end customer will posted here as response on request »$result = $client->debit($debit);«
        // merchant need to redirect end-user on merchant's error page to show result. Also other method to show result are allowed.
        // - Also this part of code will happens at generic errors(invalid credentials,..)
        $error = $result->getFirstError();
        $outError = array();  
        $outError ["message"] = $error->getMessage();
        $outError ["code"] = $error->getCode();
        $outError ["adapterCode"] = $error->getAdapterCode();
        $outError ["adapterMessage"] = $error->getAdapterMessage();
        header("Location: " . $ini_array['Domain']['myDomainContent'] ."/examples/paymentNOK.php?" . http_build_query($outError));
        die;

    } elseif ($result->getReturnType() === Result::RETURN_TYPE_REDIRECT) {
        //redirect the user                                                               
        // -  in a case of usage of payment.JS + 3DS or HPP end customer is redirected to the card issuer's site/HPP.
        // -  in a case of usage of payment.JS without 3DS this never happen
        //print_r($result->getRedirectUrl());
        header('Location: '.$result->getRedirectUrl());
        die;

    } elseif ($result->getReturnType() === Result::RETURN_TYPE_PENDING) {

        // payment is pending: wait for callback to complete
        // not for credit card transactions
        // e.g. setCartToPending();
        
        //Flik payment when merchant show loading screen 
        $extradata = $result->getExtraData();
        header("Location: " . $ini_array['Domain']['myDomainContent'] ."/examples/loading.php?". "amount=". $debit->getAmount() . "&currency=" . $debit->getCurrency() . "&expiresDateTime=". $extradata['expiresDateTime'] . "&uuid=". $result->getUuid());

    } elseif ($result->getReturnType() === Result::RETURN_TYPE_FINISHED) {
        //payment is finished, update your cart/payment transaction
        // - In a case of usage of payment.JS without 3DS (frictionless transactions) the SUCCESS result for end customer will posted here as response on request »$result = $client->debit($debit);«
        // merchant need to redirect end-user on merchant's succes page to show result. Also other method to show result are allowed.
        // - In a case of usage of payment.JS + 3DS or HPP this never happen. We (payment gateway) redirect the end-user to merchant's error/success/cancel URL sent in initial API call
        
        header("Location: " . $ini_array['Domain']['myDomainContent'] ."/examples/paymentOK.php?" . http_build_query($result->toArray()));
        die;
        //finishCart();
    }
} else{
    // handle the error
    // e.g. cancelCart();
    //error handling Sample
        
    // - In a case of usage of payment.JS without 3DS (frictionless transactions) the ERROR/DECLINE result for end customer will posted here as response on request »$result = $client->debit($debit);«
    // merchant need to redirect end-user on merchant's error page to show result. Also other method to show result are allowed.
    $error = $result->getFirstError();
    $outError = array();  
    $outError ["message"] = $error->getMessage();
    $outError ["code"] = $error->getCode();
    $outError ["adapterCode"] = $error->getAdapterCode();
    $outError ["adapterMessage"] = $error->getAdapterMessage();
    header("Location: " . $ini_array['Domain']['myDomainContent'] ."/examples/paymentNOK.php?" . http_build_query($outError));
    die;

}