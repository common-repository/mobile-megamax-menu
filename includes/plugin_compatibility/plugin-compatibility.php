<?php
require_once MEGAMAXMENU_DIR . 'includes/plugin_compatibility/menu-image.php';

function megamaxmenu_detect_plugin( $plugin_slug ){

  return false;
}

// Requires priority of 20, otherwise the standard General filter is going to override it
add_filter( 'megamaxmenu_general_settings_sections' , 'megamaxmenu_general_settings_sections_plugincompat' , 20 , 1 );
function megamaxmenu_general_settings_sections_plugincompat( $section ){

	$section['sub_sections']['plugin_compatibility'] = array(
		'title'	=> __( 'Plugin Compatibility', 'megamaxmenu' ),
	);

	return $section;
}


add_filter( 'megamaxmenu_settings_panel_fields' , 'megamaxmenu_settings_panel_fields_plugin_compatibility' , 30 , 1 );
function megamaxmenu_settings_panel_fields_plugin_compatibility( $all_fields = array() ){

	$fields = $all_fields[MEGAMAXMENU_PREFIX.'general'];

  $fields[800] = array(
		'name'	=> 'header_plugin_compatibiity',
		'label'	=> __( 'Plugin Compatibility' , 'megamaxmenu' ),
		'type'	=> 'header',
		'group'	=> 'plugin_compatibility',
	);

	$all_fields[MEGAMAXMENU_PREFIX.'general'] = $fields;

	return $all_fields;
}
