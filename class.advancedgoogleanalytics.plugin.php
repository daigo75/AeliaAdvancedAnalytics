<?php if (!defined('APPLICATION')) exit();

$PluginInfo['AdvancedGoogleAnalytics'] = array(
	'Name' => 'Advanced Google Analytics',
	'Description' => 'Google Analytics Plugin. Avoid tracking some users.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.1a1'),
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'SettingsUrl' => 'settings/advancedgoogleanalytics',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => "Alessandro Miliucci",
	'AuthorEmail' => 'lifeisfoo@gmail.com',
	'AuthorUrl' => 'http://forkwait.net',
	'License' => 'GPL v3'
);

class AdvancedGoogleAnalyticsPlugin implements Gdn_IPlugin {
	
	public function SettingsController_AdvancedGoogleAnalytics_Create($Sender) {
		$Sender->Permission('Garden.Plugins.Manage');
		$Sender->AddSideMenu();
		$Sender->Title('Advanced Google Analytics');
		$ConfigurationModule = new ConfigurationModule($Sender);
		$ConfigurationModule->RenderAll = True;
		$Schema = array( 'Plugins.AdvancedGoogleAnalytics.PageTrackerID' => 
				 array('LabelCode' => 'TrackerID', 
				       'Control' => 'TextBox', 
				       'Default' => C('Plugins.AdvancedGoogleAnalytics.PageTrackerID', 'UA-00000000-0')
				       ),
				 'Plugins.AdvancedGoogleAnalytics.DoNoTrackUsers' => 
				 array('LabelCode' => 'Comma separated usernames', 
				       'Control' => 'TextBox', 
				       'Default' => C('Plugins.AdvancedGoogleAnalytics.DoNoTrackUsers', '')
				       )
		);
		$ConfigurationModule->Schema($Schema);
		$ConfigurationModule->Initialize();
		$Sender->View = dirname(__FILE__) . DS . 'views' . DS . 'agasettings.php';
		$Sender->ConfigurationModule = $ConfigurationModule;
		$Sender->Render();
	}
	
	
	public function Base_AfterBody_Handler($Sender) {
		if ($Sender->MasterView == 'admin') return;
		$ArrUsers = explode(',', C('Plugins.AdvancedGoogleAnalytics.DoNoTrackUsers'));
		if(in_array("admin", $ArrUsers)) return;
		$PageTrackerID = C('Plugins.AdvancedGoogleAnalytics.PageTrackerID');
		if ($PageTrackerID) echo <<<ANALYTICS
<!-- Google Analytics -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$PageTrackerID']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
ANALYTICS;
	}
	
	public function Setup() {}
}