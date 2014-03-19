<?php if (!defined('APPLICATION')) die();
	// Render StatCounter tracking code

	$StatCounterProjectID = GetValue('StatCounterProjectID', $this->Data);
	$StatCounterSecurityCode = GetValue('StatCounterSecurityCode', $this->Data);
?>
<!-- Aelia Advanced Analytics - StatCounter -->
<script type="text/javascript">
	var sc_project=<?php echo $StatCounterProjectID; ?>;
	var sc_invisible=1;
	var sc_security="<?php echo $StatCounterSecurityCode; ?>";
	var scJsHost = (("https:" == document.location.protocol) ? "https://secure." : "http://www.");
	document.write("<sc"+"ript type='text/javascript' src='" + scJsHost + "statcounter.com/counter/counter.js'></"+"script>");
</script>
<noscript><div class="statcounter">
	<a title="hits counter" href="http://statcounter.com/" target="_blank">
		<img class="statcounter" src="http://c.statcounter.com/<?php echo $StatCounterProjectID; ?>/0/<?php echo $StatCounterSecurityCode; ?>/1/" alt="hits counter">
	</a>
</div>
</noscript>
