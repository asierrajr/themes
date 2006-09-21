<?php
$themeSettings = array(
	'feature_jscalendar' => array(
		'label' => 'Enable JSCalendar',
		'note' => 'JSCalendar is a javascript calendar popup that allows you to easily select a date using an easy to use and appealing interface.',
	),
	'themes_edit_css' => array(
		'label' => 'Edit Css',
		'note' => 'Enables you to edit CSS files from within your browser to customise your site style according to your desires.',
	),
	'site_disable_fat' => array(
		'label' => "Disable Fading",
		'note' => "Disable the fading effect used when displaying any success, warning or error messages.",
	),
	'site_disable_jstabs' => array(
		'label' => "Disable Javascript Tabs",
		'note' =>"If you have difficulties with the javascript tabs, of you don't like them, you can disable them here.",
	),
);
$gBitSmarty->assign( 'themeSettings', $themeSettings );

if( !empty( $_REQUEST['change_prefs'] ) ) {
	$pref_simple_values = array(
		"site_biticon_display_style",
	);

	foreach( $pref_simple_values as $svitem ) {
		simple_set_value( $svitem, THEMES_PKG_NAME );
	}

	foreach( $themeSettings as $toggle ) {
		simple_set_toggle( key( $toggle ), THEMES_PKG_NAME );
	}
}

// set the options biticon takes
$biticon_display_options = array(
	'icon' => tra( 'icon' ),
	'text' => tra( 'text' ),
	'icon_text' => tra( 'icon and text' )
);
$gBitSmarty->assign( "biticon_display_options", $biticon_display_options );
?>