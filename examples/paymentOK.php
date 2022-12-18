<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Bill payment</TITLE>
<style>
.container {
   text-align: center;
   color:black;
 }

.container pre {
  display: inline-block;
  text-align: left;
 }
</style>

</HEAD>
<BODY style="background-color: #CCAACC">
	<div style="text-align: center">
		<h1>Bill payment</h1>
	</div>
	<div style="font-size:25px; color:green; text-align: center" >
		The transaction has been approved. Thank you for your purchase.<BR>
    </div>

	<div class="container">
		<pre>
		<?php
			$buf = extract($_GET);
			if(isset($buf) and $buf != ""){
				print_r($_GET);
			}
		?>
		</pre>
	</div>

	<div style="text-align: center">
		<p>Copyright Bankart d.o.o.</p>
	</div>
</BODY>
</HTML>