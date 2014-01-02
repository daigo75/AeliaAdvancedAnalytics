<?php if (!defined('APPLICATION')) die();

?>
<div id="GeneralSettings">
<?php
	// TODO Implement a proper view
	$this->ConfigurationModule->Render();
?>
</div>
<div id="Credits">
	<span class="Title"><?php echo T('Credits:'); ?></span>
	<span><?php
		$OriginalPluginLink = Anchor('Advanced Google Analytics', 'http://vanillaforums.org/addon/advancedgoogleanalytics-plugin');
		$OriginalAuthorLink = Anchor('A.Miliucci', 'http://forkwait.net');
		echo sprintf(T('This plugin was inspired by %s plugin, by %s.'),
								 $OriginalPluginLink,
								 $OriginalAuthorLink);
	?></span>
</div>
