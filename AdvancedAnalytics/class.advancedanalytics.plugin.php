<?php if (!defined('APPLICATION')) exit();

$PluginInfo['AdvancedAnalytics'] = array(
	'Name' => 'Aelia Advanced Analytics',
	'Description' => 'Analytics Plugin. Adds tracking code, such as Google Analytics, allowing to ignore specific users or roles.',
	'Version' => '1.0.0.140102',
	'RequiredApplications' => array('Vanilla' => '2.0.10'),
	'RequiredTheme' => false,
	'RequiredPlugins' => array('AeliaFoundationClasses' => '13.12.09.001',),
	'MobileFriendly' => true,
	'SettingsUrl' => 'plugin/advancedanalytics',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => 'Diego Zanella',
	'AuthorEmail' => 'diego@pathtoenlightenment.net',
	'AuthorUrl' => 'http://dev.pathtoenlightenment.net',
	'License' => 'GPLv3'
);

class AdvancedAnalyticsPlugin extends Gdn_Plugin {
	/**
	 * Loads some common JavaScript files and related CSS.
	 *
	 * @param Gdn_Controller Sender
	 */
	protected function AddCommonJsFiles($Sender) {
		$Sender->Head->AddCss('https://cdnjs.cloudflare.com/ajax/libs/chosen/1.0/chosen.css');
		$Sender->AddJsFile('https://cdnjs.cloudflare.com/ajax/libs/chosen/1.0/chosen.jquery.min.js');
	}

	/**
	 * Adds the AdvancedAnalytics method to PluginController and performs some
	 * initialisation steps.
	 *
	 * @param Gdn_Controller Sender
	 */
	public function PluginController_AdvancedAnalytics_Create($Sender) {
		$Sender->Permission('Garden.Plugins.Manage');
		$Sender->AddSideMenu();
		$Sender->Title('Advanced Analytics');

		$this->Dispatch($Sender, $Sender->RequestArgs);
	}

	/**
	 * Renders the Plugin's default (index) page.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_Index($Sender) {
		$this->Controller_Settings($Sender);
	}

	/**
	 * Renders the Settings page.
	 *
	 * @param object Sender Sending controller instance
	 */
	public function Controller_Settings($Sender) {
		// Use the Aelia Form to take advantage of more flexible fields
		$Form = new \Aelia\Form();

		$ConfigurationModule = new ConfigurationModule($Sender);
		$ConfigurationModule->Form($Form);
		$ConfigurationModule->RenderAll = true;

		$RoleModel = new RoleModel();
		$AvailableRoles = $RoleModel->GetArray();

		$Schema = array(
			'Plugins.AdvancedAnalytics.GoogleAnalyticsTrackingID' =>
			array('LabelCode' => T('Google Analytics Tracking ID'),
						'Description' => T('You can find the tracker ID on your Google Analytics ' .
															 'control panel. It is an alphanumeric value that looks ' .
															 'like "UA-XXXXXXXX-X". If you leave it empty, Google ' .
															 'Analytics code will not be added to the pages.'),
						'Control' => 'TextBox',
						'Default' => C('Plugins.AdvancedAnalytics.GoogleAnalyticsTrackingID',
													 'UA-00000000-0'),
						'Options' => array(
						)),
			'Plugins.AdvancedAnalytics.DoNoTrackUsers' =>
			array('LabelCode' => T('Do not track these users'),
						'Description' => T('Enter a comma-separated list of users who will not be ' .
															 'tracked. Start typing a user name to be presented with a ' .
															 'list of matches.'),
						'Control' => 'TextBox',
						'Default' => C('Plugins.AdvancedAnalytics.DoNoTrackUsers', ''),
						'Options' => array(
							'class' => 'User Autocomplete InputBox',
						)),
			'Plugins.AdvancedAnalytics.DoNoTrackRoles' =>
			array('LabelCode' => T('Do not these roles'),
						'Description' => T('Select which roles should not be tracked. Users who have ' .
															 'ANY of the selected roles will not be tracked. Start ' .
															 'typing a role name to be presented with a list of matches.'),
						'Control' => 'DropDown',
						'Default' => C('Plugins.AdvancedAnalytics.DoNoTrackRoles', ''),
						'Items' => $AvailableRoles,
						'Options' => array(
							'multiple' => true,
						)),
		);
		$ConfigurationModule->Schema($Schema);
		$ConfigurationModule->Initialize();

		$Sender->ConfigurationModule = $ConfigurationModule;

		$this->AddCommonJsFiles($Sender);
		$Sender->AddJsFile('jquery.autocomplete.js');
		$Sender->AddJsFile('generalsettings.js', 'plugins/AdvancedAnalytics/js');
		$Sender->AddCssFile('advancedanalytics.css', 'plugins/AdvancedAnalytics/design');

		$Sender->Render($this->GetView('generalsettings.php'));
	}

	/**
	 * Determines if current user has any of the roles that should not be tracked.
	 *
	 * @return bool
	 */
	protected function UserHasExcludedRole() {
		$UserID = Gdn::Session()->UserID;
		$ExcludedRoles = C('Plugins.AdvancedAnalytics.DoNoTrackRoles');
		$UserRoles = Gdn::UserModel()->GetRoles($UserID)->ResultArray();

		foreach($UserRoles as $Role) {
			if(in_array(GetValue('RoleID', $Role), $ExcludedRoles)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines if tracking should be skipped, depending on factors such as
	 * excluded users and roles.
	 *
	 * @return bool
	 */
	protected function SkipTracking() {
		// Do not track visits when in Admin section
		if($Sender->MasterView == 'admin') {
			return true;
		}

		// Do not track excluded users
		$UserName = GetValue('Name', Gdn::Session()->User);
		if(!empty($UserName) && in_array($UserName, $ExcludedUsers)) {
			$ExcludedUsers = array_map('trim', explode(',', C('Plugins.AdvancedAnalytics.DoNoTrackUsers')));
			return true;
		}

		// Do not track excluded roles
		if($this->UserHasExcludedRole()) {
			return true;
		}

		return false;
	}

	/**
	 * Adds the tracking code to the page.
	 *
	 * @param Gdn_Controller Sender
	 */
	protected function RenderTrackingCode($Sender) {
		// Render Google Analytics tracking code, if needed
		$GoogleAnalyticsTrackingID = C('Plugins.AdvancedAnalytics.GoogleAnalyticsTrackingID');
		if(!empty($GoogleAnalyticsTrackingID)) {
			$Sender->SetData('GoogleAnalyticsTrackingID', $GoogleAnalyticsTrackingID);
			echo $Sender->FetchView($this->GetView('googleanalytics.php'));
		}
	}

	/**
	 * Event handler, fired immediately after the page body is rendered.
	 *
	 * @param Gdn_Controller Sender
	 */
	public function Base_AfterBody_Handler($Sender) {
		if($this->SkipTracking()) {
			return;
		}

		$this->RenderTrackingCode($Sender);
	}

	/**
	 * Setup method. Not currently used.
	 *
	 * @param Gdn_Controller Sender
	 */
	public function Setup() {
		// Not used
	}
}
