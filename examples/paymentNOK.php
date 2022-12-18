<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Račun o plačilu</TITLE>

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

<BODY style="background-color:#CCAACC" >
	<div style="text-align: center">
		<h1>Error notification</h1>
	</div>
	<div style="font-size:25px; color:red; text-align: center" >
		An error has occurred during order processing.<BR>
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

	<div style="text-align: center; color:black">
		<p>Copyright Bankart d.o.o.</p>
	</div>

</BODY>
</HTML>