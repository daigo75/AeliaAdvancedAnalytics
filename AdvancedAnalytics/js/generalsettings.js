// JavaScript for general settings page
jQuery(document).ready(function($) {
	// Enable user name autocomplete on selected inputs
	$('.User.Autocomplete').livequery(function() {
		$(this).autocomplete(
			gdn.url('/dashboard/user/autocomplete/'),
			{
				minChars: 2,
				multiple: true,
				selectFirst: true
			}
		);
	});

	$SettingsForm = $('#GeneralSettings form');
	// Use Chosen plugin to replace select boxes
	if(jQuery().chosen) {
		$SettingsForm
			.find('select')
			.chosen();
	}
});
