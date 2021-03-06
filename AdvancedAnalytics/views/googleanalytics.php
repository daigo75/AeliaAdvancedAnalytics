<?php if (!defined('APPLICATION')) die();
	// Render Google Analytics tracking code

	$GoogleAnalyticsTrackingID = GetValue('GoogleAnalyticsTrackingID', $this->Data);
?>
<!-- Aelia Advanced Analytics - Google Analytics -->
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php echo $GoogleAnalyticsTrackingID; ?>']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
