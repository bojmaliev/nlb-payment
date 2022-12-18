<!DOCTYPE HTML>
<html>
<head>
    <title>Waiting</title>
    <link rel="stylesheet" href="./css/flik_loading.css">
    <script src="./js/script.js"></script>
    <script src="./js/dayjs.min.js"></script>
    <script src="./js/customParseFormat.js"></script>
    <script src="./js/utc.js"></script>
</head>
<body class="body">
<header id="containerHeader">
	<div id="logo">
		<img src="./logo/bankart.svg" id="bankartPicture">
	</div>
</header>
<script>
    dayjs.extend(window.dayjs_plugin_customParseFormat);
    dayjs.extend(window.dayjs_plugin_utc);
</script>
<div id="containerBody">
		<div id="loadingBar">
			<svg width="200" height="200">
			  <circle id="circle2" cx="100" cy="100" r="70" />
			</svg>
			<svg width="200" height="200">
			  <circle id="circle" cx="100" cy="100" r="70" />
			</svg>
			<span id="procent"></span>
		</div>
			
		<div id="bodyText">
			<p>V svoji aplikaciji za Flik plačila potrdi&nbsp;zahtevek&nbsp;za&nbsp;plačilo.</p>
			<div>
				<p>Ime trgovca</p>
				<p><?php 
                echo($_GET["amount"]); 
                echo(" ");
                echo($_GET["currency"]); 
                ?> </p>
                <p id=startTime></p>
			</div>
		</div>
	</div>
    <script>
		// creating loading screen with countdown timers
        var start = dayjs.utc("<?php Print($_GET['expiresDateTime']); ?>", 'YYYY-MM-DD hh:mm:ss').add(-5,'minute');
        var end = dayjs.utc("<?php Print($_GET['expiresDateTime']); ?>", 'YYYY-MM-DD hh:mm:ss');
        document.getElementById('startTime').innerText = start.local().format('DD. MM. YYYY, H:mm'); 
        setTimeout(function(){
            document.getElementById('procent').style.color = 'red';
            document.getElementById('circle2').style.stroke = 'rgba(255, 99, 71, 0.2)';
            var circleOld = document.getElementById('circle')
			var circleNew = circleOld.cloneNode(true);
			circleNew.style.stroke = 'rgba(255, 0, 0, 1)';
			circleOld.parentNode.replaceChild(circleNew,circleOld);
			document.getElementById('bodyText').innerHTML = "<p style='color:red;'>Čas za potrditev je potekel.<br>Sistem preverja status plačila.</p>";
            startTimer(end.add(90, 'second'),end);
		}, (end.diff(dayjs(), 'ms')));

		startTimer(end, start);
	
		
		//Check every 10000ms (10s) if the transaction result was send on callbackURL  	
		var interval = setInterval(checkCallback, 10000, "<?php Print($_GET['uuid']); ?>",end.add(90, 'second'));
		
		function checkCallback(uuid, endTime){
			var myNow= dayjs();
			if (myNow < endTime){
				//get to check in DB if result for UUID exist
				// if exist then:
				// clearInterval(interval);
				// Show customers a status of Transaction (error/success page)
			
			} else{
				//timer is expired
				clearInterval(interval);
				window.location =  "/PHPPaymentgatewayJson/examples/paymentNOK.php?" + "&Error=The status of transaction " + uuid + " is unknown" ;
			}
		}
			

		//you need to add setInterval() for 6:30 min to check if result was send to callbackURL for status of transaction. After that redirect user on error page


    </script>
</body>
<footer id="containerFooter">
		<div id="footerIcons">
        <img id="flikIcon"  src="./logo/flik.png" >
		</div>
		<p id="footerText">Copyright @2021 <a href="http://bankart.si/" class="bankartLink" target="_blank">Bankart d.o.o.</a></p>
	</footer>
</html>

