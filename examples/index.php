<!DOCTYPE HTML>
<HTML>
<HEAD>
	<TITLE>Nastavitve transakcije - PHP:</TITLE>
    <script src="./js/alias.js"></script>
</HEAD>   
<BODY style="background-color:#CCAACC" >

<form id="payment_form" method="POST" onsubmit="return selectedTransaction();">
    <div style="text-align: center">
        <P><h1>Test mask - PHP:</h1></P>
    </div>
    <div>
        <label for="TransactionType" style="color:green; font-size: 20px">Transaction type:</label><br/>
        <select name="TranType" id="TranType" onchange="loadRadio()">
            <option value="debit" selected="selected">Debit</option>
            <option value="preauth">Preauthorize</option>
            <option value="capture">Capture</option>
            <option value="void">Void</option>
            <option value="refund">Refund</option>
            <option value="status">Status</option>
            <option value="register">Register</option>
            <option value="deregister">Deregister</option>
            <option value="payout">Payout</option>
        </select> 
        <br/><br/>
    </div>
    <div>
        <label style="color:green; font-size: 20px">Language:</label><br/>
        <select name="Language" id="Language">
            <option value="bg" >Bulgarian</option>
            <option value="bs">Bosnian</option>
            <option value="cs">Czech</option>
            <option value="de">German</option>
            <option value="en">English</option>
            <option value="es">Spanish</option>
            <option value="hr">Croatian</option>
            <option value="hu">Hungarian</option>
            <option value="it">Italian</option>
            <option value="me">Montenegrin</option>
            <option value="mk">Macedonian</option>
            <option value="pl">Polish</option>
            <option value="ro">Romunian</option>
            <option value="ru">Russian</option>
            <option value="sk">Slovak</option>
            <option value="sl" selected="selected">Slovenian</option>
            <option value="sq">Albanian</option>
            <option value="sr">Serbian</option>
            <option value="tr">Turkish</option>
        </select> 
        <br/><br/>
    </div>
  
    <div id="divAmount" style="display: inline;">
        <label style="color:green; font-size: 20px">Amount</label><br/>
        <input type="number" name = "Amount" id="Amount" min="0" value='1.99' step=".01"/><input type="text" name = "Currency" id="Currency" value='EUR'/>
        <br/><br/>
    </div>
    <div id="divInstallments" style="display: inline;">
        <label style="color:green; font-size: 20px">Number of installments</label><br/>
        <select name="numInstalment" id="numInstalment">
            <?php
                foreach(range(0, 36) as $number){
                  printf('<option value="%1$02d">%1$02d</option>', $number);
                }
              ?>
        </select>
        <br/><br/>
    </div>

    <div id="IntegrationTypeCheckbox" style="display: inline;">
        <label for="IntegrationType" style="color:green; font-size: 20px">Integration type:</label><br/>       
        <input type="radio" name="IntType" id="Redirect" value="Redirect" checked="checked" />Redirect to HPP<br/>
        <input type="radio" name="IntType" id="iframe" value="iframe"/>Redirect to HPP entire iFrame<br/>
        <input type="radio" name="IntType" id="paymentJS" value="paymentJS"/>paymentJS<br/>
        <br/>
    </div>
    
    <div style="display: inline;">
        <label style="color:green; font-size: 20px">Flik payment:</label><br/>
        <input type="checkBox" name="flikPayment" id="flikPayment" onClick="setFlikAlias()"/>
        <label name="lblFlik" id="lblFlik" >Set Flik alias</label> 
        <br/><br/>
    </div>
    <div id="flikAliasTran" style="display: none;">
        <label>Alias</label> 
        <input type="text" name="flikAlias" id="flikAlias"/>
        <br/><br/>
    </div>

    <div id="Initial" style="display: inline;">
        <label style="color:green; font-size: 20px">Initial transaction to store a card:</label><br/>
        <select name="initialStoreTrans" id="initialStoreTrans" onchange="loadRadio()">
            <option value="No" selected="selected">No</option>
            <option value="initialCoF">Initial CardOnFile (CoF)</option>
            <option value="initialRec">Initial recurring transaction</option>
            <option value="initialMIT">Initial Merchant initiated transaction (MIT)</option>
        </select>
        <br/><br/>
    </div>
    <div id="subSeqent" style="display: inline;">
        <label style="color:green; font-size: 20px">Sub-sequent transaction:</label><br/>
        <select name="subSeqentTrans" id="subSeqentTrans" onchange="loadRadio()">
            <option value="No" selected="selected">No</option>
            <option value="subSeqentCoF" >Sub-seqent CardOnFile (CoF)</option>
            <option value="subSeqentRec">Sub-seqent recurring transaction</option>
            <option value="subSeqentMIT">Sub-seqent Merchant initiated transaction (MIT)</option>
        </select>
        <br/><br/>
    </div>

    <div id="schedul" style="display: none;">
        <input type="checkbox" id="gatewaySchadule" name="gatewaySchadule" checked="checked" />
        <label for="gatewaySchadule" style="color:black; font-size: 16px" >Set a Schadule on a gateway. If you leave this unchecked you must send a sub-sequent transactions on own schadule</label><br/><br/>
        <label style="color:green; font-size: 20px">Schedule intervals:</label><!--<label>(The period between the initial and second transaction must be greater than 24 hours.)</label> --><br/>
        <label style="font-size: 16px">Schedul unit:</label>
        <select style="margin: 1px;" name="scheduleUnit" id="scheduleUnit">
            <option value="DAY" selected="selected">DAY</option>
            <option value="WEEK">WEEK</option>
            <option value="MONTH">MONTH</option>
            <option value="YEAR">YEAR</option>
        </select><br/>
        <label style="font-size: 16px">Schedule period:</label><input style="margin: 1px;" id="schedulePeriod" name="schedulePeriod" > Numbers only!<br/>
        <label style="font-size: 16px;">Schedule delay: Optional.</label><input style="margin: 1px;" id="scheduleDelay" name="scheduleDelay"> It can be used to set a differing length for the first period in format f.e.: '1 days 1 hour'. Min 24 hours<br/>
        <br/><br/>
    </div>
    
    <div id="ReferenceTransactionId" style="display: none;">
        <label style="color:green; font-size: 20px">ReferenceTransactionId:</label><label id="lblDescrrefTranId" style="font-size: 16px; display: none">Use referenceTransactionID if you doing initial transaction with stored card!</label><br/>
        <input type="text" name="refTranId" id="refTranId" />
        <br/><br/>
    </div>    
    <div>
        <label>First name</label>
        <input type="text" style="margin: 1px;"id="first_name" name="first_name"  value='Janez'/>
    </div>
    <div>
        <label>Last name</label>
        <input type="text" style="margin: 1px;"id="last_name" name="last_name" value='Novak'/>
    </div>
    <div>
        <label>Email</label>
        <input type="text" style="margin: 1px;"id="email" name="email" value='test@email.com' />
    </div>
    <div>
        <label>Description</label>
        <input type="text" style="margin: 1px;" id="descr" name="descr" value='Test Payment' />
        (It is shown in Flik app at Flik payment) 
    </div>
    <br/>
    <div>
        <input type="submit" value="Submit" id="Pay" /><br/>
    </div>
</form>


<script type="text/javascript">
    function selectedTransaction(){
        var e = document.getElementById("TranType");
        var tranType = e.options[e.selectedIndex].value;
        if (tranType=="debit"){
            if (document.getElementById("flikPayment").checked == true){
                var aliasInput = document.getElementById('flikAlias');
                var aliasValidation = validateAlias(aliasInput.value);  
                if( aliasValidation == 'empty') {
                    alert('Enter Alias');
                    return false;
                } else if ( aliasValidation == 'invalid' ) {
                    alert('Enter Alias in correct format');
                    return false;
                } 
            }
            var f = document.getElementById("initialStoreTrans");
            var initialType = f.options[f.selectedIndex].value;
            //initial MIT and recuring are not alowed with amt=0
            if ((initialType == 'initialRec'|| initialType == 'initialMIT')&& parseFloat(document.getElementById('Amount').value)==0){
                alert( f.options[f.selectedIndex].text + " is not allowed with amount " + document.getElementById('Amount').value);
                return false;
            }
            f = document.getElementById("subSeqentTrans");
            var subSeqentTrans = f.options[f.selectedIndex].value;
            var value = document.getElementById('refTranId').value.trim();
            if ((subSeqentTrans != 'No') &&  value.length ==0 ) {
                alert("Enter Reference UUID");
                return false;
            }

            var radios = document.getElementsByName('IntType');
            for (var i = 0, length = radios.length; i < length; i++) {
                if (radios[0].checked) {
                   document.getElementById('payment_form').action = 'debit.php'; 
                   break;
                } else if(radios[1].checked) {
                    document.getElementById('payment_form').action = 'iframe.php'; 
                    break;
                } else if(radios[2].checked) {
                    document.getElementById('payment_form').action = 'javaScript.php'; 
                    break;
                } else {
                    alert("Select Integration Type");
                    return false;
                }
            }
        } else if (tranType=="preauth"){
            var f = document.getElementById("subSeqentTrans");
            var subSeqentTrans = f.options[f.selectedIndex].value;
            //Sub-sequent MIT and recuring are not alowed with preauth
            if (subSeqentTrans == 'subSeqentRec'|| subSeqentTrans == 'subSeqentMIT'){
                alert( 'Preauthorize ' + f.options[f.selectedIndex].text + " is not allowed!");
                return false;
            }

            var radios = document.getElementsByName('IntType');
            for (var i = 0, length = radios.length; i < length; i++) {
                if (radios[0].checked) {
                   document.getElementById('payment_form').action = 'preauth.php'; 
                   break;
                } else if(radios[1].checked) {
                    document.getElementById('payment_form').action = 'iframe.php'; 
                    break;
                } else if(radios[2].checked) {
                    document.getElementById('payment_form').action = 'javaScript.php'; 
                    break;
                } else {
                    alert("Select Integration Type");
                    return false;
                }
            }
        } else if (tranType=="capture"){
            var value= document.getElementById('refTranId').value.trim();
            switch(value){
                case "":
                    alert ("Enter ReferenceTransactionId");
                    return false;
                default:
                    document.getElementById('payment_form').action = 'capture.php';
                    break;
            }
        } else if (tranType=="void"){
            var value= document.getElementById('refTranId').value.trim();
            switch(value){
                case "":
                    alert ("Enter ReferenceTransactionId");
                    return false;
                default:
                    document.getElementById('payment_form').action = 'void.php';
                    break;
            }
        } else if (tranType=="refund"){
            var value= document.getElementById('refTranId').value.trim();
            switch(value){
                case "":
                    alert ("Enter ReferenceTransactionId");
                    return false;
                default:
                    document.getElementById('payment_form').action = 'refund.php';
                    break;
            }
        } else if (tranType=="status"){
            var value= document.getElementById('refTranId').value.trim();
            switch(value){
                case "":
                    alert ("Enter ReferenceTransactionId");
                    return false;
                default:
                    document.getElementById('payment_form').action = 'status.php';
                    break;
            }
        } else if (tranType=="register"){
            var radios = document.getElementsByName('IntType');
            for (var i = 0, length = radios.length; i < length; i++) {
                if (radios[0].checked) {
                   document.getElementById('payment_form').action = 'register.php'; 
                   break;
                } else if(radios[1].checked) {
                    document.getElementById('payment_form').action = 'iframe.php'; 
                    break;
                } else if(radios[2].checked) {
                    document.getElementById('payment_form').action = 'javaScript.php'; 
                    break;
                } else {
                    alert("Select Integration Type");
                    return false;
                }
            }
        } else if (tranType=="deregister"){
            var value= document.getElementById('refTranId').value.trim();
            switch(value){
                case "":
                    alert ("Enter ReferenceTransactionId");
                    return false;
                default:
                    document.getElementById('payment_form').action = 'deregister.php';
                    break;
            }
        } else if (tranType=="payout"){
            document.getElementById('payment_form').action = 'payout.php';      
        } else {
            alert ("Please select Transaction Type");
            return false;
        }
    }

   
    function loadRadio(){
        var e = document.getElementById("TranType");
        var tranType = e.options[e.selectedIndex].value;
        document.getElementsByName("IntType")[2].disabled = false;

        let select = document.getElementById("subSeqentTrans");                   
        let to_hide = select[2];
        to_hide.hidden= false;
        to_hide = select[3];
        to_hide.hidden= false;
        if (tranType == "debit" || tranType == "preauth" ){
            document.getElementById( 'IntegrationTypeCheckbox' ).style.display = 'inline';
            document.getElementById('Initial').style.display = 'inline';
            document.getElementById('subSeqent').style.display = 'inline';
            document.getElementById( 'schedul' ).style.display = 'none';
            document.getElementById( 'lblDescrrefTranId' ).style.display = 'none';
            document.getElementById( 'divAmount' ).style.display = 'inline';
            document.getElementById( 'divInstallments' ).style.display = 'inline';
            
            if (tranType == "preauth"){
                /*let select = document.getElementById("subSeqentTrans")                   
                let to_hide = select[1];
                to_hide.setAttribute('hidden', 'hidden');
                to_hide = select[2];
                to_hide.setAttribute('hidden', 'hidden');
                */
                //hide subsequent MIT & Recurring because they are not allowed
                let to_hide = select[2];
                to_hide.hidden= true;
                to_hide = select[3];
                to_hide.hidden= true;
            }


            var f = document.getElementById("initialStoreTrans");
            var initialType = f.options[f.selectedIndex].value;

            if (initialType == 'No'){
                document.getElementById('subSeqent').style.display = 'inline';
                
                var g = document.getElementById("subSeqentTrans");
                var subSeqType = g.options[g.selectedIndex].value;

                if (subSeqType == 'No'){
                    document.getElementById('ReferenceTransactionId').style.display = 'none';
                }
                else{
                    document.getElementById('IntegrationTypeCheckbox').style.display = 'none';
                    document.getElementById('ReferenceTransactionId').style.display = 'inline';
                    document.getElementById('Redirect').checked = true;
                }
                

            } else if (initialType == 'initialCoF'){
                document.getElementById('subSeqentTrans').value = 'No';
                document.getElementById('subSeqent').style.display = 'none';
                document.getElementById('ReferenceTransactionId').style.display = 'none';

            } else if (initialType == 'initialRec'){
                document.getElementById("subSeqentTrans").value = 'No';
                document.getElementById('subSeqent').style.display = 'none';
                document.getElementById('ReferenceTransactionId').style.display = 'inline';
                document.getElementById( 'schedul' ).style.display = 'inline';
                document.getElementById( 'lblDescrrefTranId' ).style.display = 'inline';
            } else if (initialType == 'initialMIT'){
                document.getElementById('subSeqentTrans').value = 'No';
                document.getElementById('subSeqent').style.display = 'none';
                document.getElementById('ReferenceTransactionId').style.display = 'inline';
                document.getElementById( 'lblDescrrefTranId' ).style.display = 'inline';
            }
        }else if (tranType == "register"){
                document.getElementById('ReferenceTransactionId').style.display = 'none';
                document.getElementById( 'IntegrationTypeCheckbox' ).style.display = 'inline';
                document.getElementById( 'divAmount' ).style.display = 'none';
                document.getElementById( 'divInstallments' ).style.display = 'none';
                document.getElementById('Initial').style.display = 'none';
                document.getElementById('subSeqent').style.display = 'none';
        
        }else if (tranType == "capture" || tranType == "void"|| tranType == "refund"){
            document.getElementById('subSeqentTrans').value = 'No';
            document.getElementById('initialStoreTrans').value = 'No';        
            document.getElementById('IntegrationTypeCheckbox').style.display = 'none';
            document.getElementById('Initial').style.display = 'none';
            document.getElementById('subSeqent').style.display = 'none';
            document.getElementById('lblDescrrefTranId').style.display = 'inline';
            document.getElementById('ReferenceTransactionId').style.display = 'inline';
            document.getElementById('lblDescrrefTranId').style.display = 'none';
            document.getElementById('divInstallments').style.display = 'none';
            document.getElementById( 'divAmount' ).style.display = 'inline';            
        
        }else if (tranType == "deregister"){
            document.getElementById('subSeqentTrans').value = 'No';
            document.getElementById('initialStoreTrans').value = 'No';        
            document.getElementById('IntegrationTypeCheckbox').style.display = 'none';
            document.getElementById('Initial').style.display = 'none';
            document.getElementById('subSeqent').style.display = 'none';
            document.getElementById('lblDescrrefTranId').style.display = 'inline';
            document.getElementById('ReferenceTransactionId').style.display = 'inline';
            document.getElementById('lblDescrrefTranId').style.display = 'none';
            document.getElementById('divInstallments').style.display = 'none';  
            document.getElementById( 'divAmount' ).style.display = 'none';  

        }else if (tranType == "status"){
            document.getElementById('subSeqentTrans').value = 'No';
            document.getElementById('initialStoreTrans').value = 'No';        
            document.getElementById('IntegrationTypeCheckbox').style.display = 'none';
            document.getElementById('Initial').style.display = 'none';
            document.getElementById('subSeqent').style.display = 'none';
            document.getElementById('lblDescrrefTranId').style.display = 'inline';
            document.getElementById('ReferenceTransactionId').style.display = 'inline';
            document.getElementById('lblDescrrefTranId').style.display = 'none';
            document.getElementById('divInstallments').style.display = 'none';     
            document.getElementById( 'divAmount' ).style.display = 'none';       

        } else {
            document.getElementById('subSeqentTrans').value = 'No';
            document.getElementById('initialStoreTrans').value = 'No';            
            document.getElementById('IntegrationTypeCheckbox').style.display = 'none';
            document.getElementById('Initial').style.display = 'none';
            document.getElementById('subSeqent').style.display = 'none';
            document.getElementById( 'lblDescrrefTranId' ).style.display = 'none'; 
            document.getElementById( 'divInstallments' ).style.display = 'none';
        }
    }
    
    function setFlikAlias() {
        if (document.getElementById("flikPayment").checked == true){
            document.getElementById("flikAliasTran").style.display = 'inline';
        } else {
            document.getElementById("flikAliasTran").style.display = 'none';
        }
    }

    function setInputFilter(textbox, inputFilter) {
            ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
                textbox.addEventListener(event, function() {
                if (inputFilter(this.value)) {
                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else if (this.hasOwnProperty("oldValue")) {
                    this.value = this.oldValue;
                    this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                } else {
                    this.value = "";
                }
            });
        });
    }
    setInputFilter(document.getElementById("schedulePeriod"), function(value) {
        return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 31); });

</script>

</BODY>
</HTML>