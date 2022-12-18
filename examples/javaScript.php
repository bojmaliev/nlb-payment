<?php header('Access-Control-Allow-Origin:*'); ?>
<HTML>
<HEAD>
<script data-main="payment-js" src="https://gateway.bankart.si/js/integrated/payment.1.3.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<TITLE>payment.JS - PHP:</TITLE>
</HEAD>   
<BODY style="background-color:#CCAACC" >


<form id="payment_form" method="POST" action="<?php echo($_POST['TranType']. '.php'); ?>"  onsubmit="interceptSubmit(); return false;">
    <input type="hidden" name="transaction_token" id="transaction_token" />
    <input type="hidden" name="Language" value="<?php echo($_POST["Language"]); ?>"/>
    <input type="hidden" name="numInstalment" value="<?php echo($_POST["numInstalment"]); ?>"/>
    <input type="hidden" name="initialStoreTrans" value="<?php echo($_POST["initialStoreTrans"]); ?>"/>
    <input type="hidden" name="subSeqentTrans" value="<?php echo($_POST["subSeqentTrans"]); ?>"/>
    <input type="hidden" name="gatewaySchadule" value="<?php echo($_POST["gatewaySchadule"]); ?>"/>
    <input type="hidden" name="scheduleUnit" value="<?php echo($_POST["scheduleUnit"]); ?>"/>
    <input type="hidden" name="schedulePeriod" value="<?php echo($_POST["schedulePeriod"]); ?>"/>
    <input type="hidden" name="scheduleDelay" value="<?php echo($_POST["scheduleDelay"]); ?>"/>
    <input type="hidden" name="refTranId" value="<?php echo($_POST["refTranId"]); ?>"/>
    <input type="hidden" name="first_name" value="<?php echo($_POST["first_name"]); ?>"/>
    <input type="hidden" name="last_name" value="<?php echo($_POST["last_name"]); ?>"/> 
    <input type="hidden" name="email" value="<?php echo($_POST["email"]); ?>"/>
    <input type="hidden" name="descr" value="<?php echo($_POST["descr"]); ?>"/>
    <div>
    <div>
        <?php if(isset($_POST["TranType"])){
            echo("<p style='color:green; font-size: 20px'>". $_POST["TranType"]."</p><br>");
            }
        ?>
    </div>
    <div>
        <label style="color:green; font-size: 20px">Amount</label><br>
        <input type="text" name = "Amount" id="Amount" value="<?php echo $_POST["Amount"];?>" readonly style="font-family: Comic Sans MS, cursive, sans-serif; color: #555; border: 1px solid #ccc; border-radius: 4px; transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s; width:50px; outline:none " /> 
        <input type="text" name = "Currency" id="Currency" value="<?php echo $_POST["Currency"];?>" readonly style="font-family: Comic Sans MS, cursive, sans-serif; color: #555; border: 1px solid #ccc; border-radius: 4px; transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s; width:50px; outline:none" />
        <br><br>
    </div>

    <div>
        <label for="card_holder"  style="color:green; font-size: 20px">Card holder:</label><br>
        <input type="text" id="card_holder" name="card_holder" value="Test User" onfocus="myFocus(document.getElementById('card_holder').id)" onblur="myBlure(document.getElementById('card_holder').id)" style="font-family: Comic Sans MS, cursive, sans-serif; color: #555; border: 1px solid #ccc; border-radius: 4px; transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s"/>
        <br><br>
    </div>
    <div>
        <label for="number_div" style="color:green; font-size: 20px">Card number</label>
        <div id="number_div" style="height: 35px; width: 200px;"></div>
    </div>
    <div>
        <label for="cvv_div" style="color:green; font-size: 20px">CVV</label>
        <div id="cvv_div" style="height: 35px; width: 200px;"></div>
    </div>
    <div>
        <label style="color:green; font-size: 20px">Expiration Date</label><br>
        <input type="text" id="exp_month" name="exp_month" placeholder="MM" pattern="0[1-9]|1[0-2]" onfocus="myFocus(document.getElementById('exp_month').id)" onblur="myBlure(document.getElementById('exp_month').id)" style="font-family: Comic Sans MS, cursive, sans-serif; color: #555; border: 1px solid #ccc; border-radius: 4px; transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s; width:50px" />
        <input type="text" id="exp_year" name="exp_year" placeholder="YYYY" pattern="(?:20)[2-9]{1}[0-9]{1}" onfocus="myFocus(document.getElementById('exp_year').id)" onblur="myBlure(document.getElementById('exp_year').id)" style="font-family: Comic Sans MS, cursive, sans-serif; color: #555; border: 1px solid #ccc; border-radius: 4px; transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s; width:50px "/>
        <br><br>
    </div>
    <div>
        <input type="submit" value="Pay" id="btnPay" onfocus="myFocus(document.getElementById('btnPay').id)" onblur="myBlure(document.getElementById('btnPay').id)" style="font-family: Comic Sans MS, cursive, sans-serif; color: #555; border: 1px solid #ccc; border-radius: 4px; transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s; width:50px " />
    </div>
    <div id="divError" style="color:red; font-size: 12px"></div>
</form>


<script type="text/javascript">
    var payment = new PaymentJs("1.3");

   // payment.init('PublicIntegrationKey', 'number_div', 'cvv_div', function(payment){    
    <?php $ini_array = parse_ini_file("config.ini", true);  ?>
    payment.init('<?= $ini_array['Credentials']['publicIntegrationKey']?>', 'number_div', 'cvv_div', function(payment){   
    //    payment.init('O5mjuzscJPcLQZS6j3KH', 'number_div', 'cvv_div', function(payment){ 
        var numberFocused = false;
        var cvvFocused = false;
        var style = {
            'font-family': '"Comic Sans MS", cursive, sans-serif',
            'color': '#555',
            'border': '1px solid #ccc',
            'border-radius': '4px',
            'background-color': '#fff',
            'box-shadow': 'inset 0 1px 1px rgba(0, 0, 0, .075)',
            'transition':'border-color ease-in-out .15s, box-shadow ease-in-out .15s'
            
        };

        var focusStyle = {
            'transition': 'all .2s linear',
            'outline': 'none',
            'color': 'inherit',
            'background-color': '#ff0',
            'border':'1px solid rgba(21,141,247,.5)',
            'box-shadow': 'inset 0 0 3px rgba(21,141,247,.5)',
        };

        payment.setNumberStyle(style);
        payment.setCvvStyle(style);
        
        payment.numberOn('focus', function() {

            payment.setNumberStyle(focusStyle);
         });
        payment.cvvOn('focus', function() {

            payment.setCvvStyle(focusStyle);
        });
          // Blur events
        payment.numberOn('blur', function() {

            payment.setNumberStyle(style);
        });
        payment.cvvOn('blur', function() {

            payment.setCvvStyle(style);
        });
        payment.numberOn('input', function(data) {
            console.log(data);
        })
    });



    function interceptSubmit() {

    
        var data = {
        card_holder: $('#card_holder').val(),
        /* OR 
        first_name: $('#first_name').val(),
        last_name: $('#last_name').val(),  
        End or */ 

        month: $('#exp_month').val(),
        year: $('#exp_year').val(),
        email: $('#email').val()
    };
    payment.tokenize(
        data, //additional data, MUST include card_holder (or first_name & last_name), month and year
        function(token, cardData) { //success callback function
            console.log(cardData);
            $('#transaction_token').val(token); //store the transaction token
            console.log(token);
            $('#payment_form').get(0).submit(); //submit the form
        }, 
        function(errors) { //error callback function
            console.log(errors);
            var msg = '';
            var error;
            function print(msg) {
                var outputDiv = document.getElementById('divError')
                outputDiv.innerHTML = msg;
                
            }

            for (var i = 0; i < errors.length; i += 1) {
                error = errors[i];
                msg += '<h3>Error: ' + error.code + "-->"  + error.message + '</h3>';
            }
            print(msg);
            myBlure('btnPay');
            //render error information here
        }
    );

}

</script>
<script type="text/javascript">
function myFocus(elementID){
    
    document.getElementById(elementID).style.transition = "all .2s linear";
    document.getElementById(elementID).style.outline = "none";
    document.getElementById(elementID).style.color = "inherit";
    document.getElementById(elementID).style.backgroundColor = "#ff0";
    document.getElementById(elementID).style.border = "1px solid rgba(21,141,247,.5)";
    document.getElementById(elementID).style.boxShadow = "inset 0 0 3px rgba(21,141,247,.5)";
}
function myBlure(elementID){
    console.log("not");
    document.getElementById(elementID).style.transition = "border-color ease-in-out .15s, box-shadow ease-in-out .15s";
    document.getElementById(elementID).style.color = "#555";
    document.getElementById(elementID).style.backgroundColor = "#fff";
    document.getElementById(elementID).style.border = "1px solid #ccc";
    document.getElementById(elementID).style.boxShadow = "none";
}

</script>
</BODY>
</HTML>