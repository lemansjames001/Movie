jQuery(document).ready(function () {
	// This is needed by the handsontable-chosen library, we added it explicitly here to prevent errors
	window.$ = jQuery;

	// Adjust the top offset of the fixed column names during scroll
	var topOffset = jQuery('.sheet-header').height();
	jQuery('head').append('<style>#vgse-wrapper .handsontable .ht_master .wtHider {padding-top: ' + topOffset + 'px !important; top: -' + topOffset + 'px !important;}</style>');

	// Restrict the spreadsheet to the width of the container
	if (typeof hot !== 'undefined') {
		hot.updateSettings({
			width: jQuery('#post-data').width(),
			height: jQuery(window).height() - 100
		});
	}
});