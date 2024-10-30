<?php

function megamaxmenu_is_plugin_active_menu_image(){
  return class_exists( 'Menu_Image_Plugin' );
}

/** ADMIN **/
add_filter( 'megamaxmenu_settings_panel_fields' , 'megamaxmenu_settings_panel_fields_plugin_compatibility_menu_image' , 40 , 1 );
function megamaxmenu_settings_panel_fields_plugin_compatibility_menu_image( $all_fields = array() ){

	$fields = $all_fields[MEGAMAXMENU_PREFIX.'general'];

  $header_desc = __( 'The <strong>Menu Image</strong> plugin completely changes the markup of your menu, including MegaMaxMenu, therefore breaking the entire menu.  This plugin is not compatible with MegaMaxMenu and must be disabled. <br/>Plugin Status:', 'megamaxmenu' );
  $plugin_status = '<span class="megamaxmenu-plugin-compatibility-inactive megamaxmenu-plugin-compatibility-success">'.__( 'Not active' , 'megamaxmenu' ).'</span>';
  if( megamaxmenu_is_plugin_active_menu_image() ){
    $plugin_status = '<span class="megamaxmenu-plugin-compatibility-active megamaxmenu-plugin-compatibility-fail">'.__( 'Active', 'megamaxmenu' ).'</span>';
  }
  $header_desc.= ' '.$plugin_status;

  $fields[810] = array(
		'name'	=> 'header_plugin_compatibiity_menu_image',
		'label'	=> __( 'Menu Image' , 'megamaxmenu' ),
		'type'	=> 'header',
    'desc'  => $header_desc,
		'group'	=> 'plugin_compatibility',
	);
  $fields[811] = array(
		'name'	=> 'plugin_compatibility_menu_image_autodisable',
		'label' => __( 'Automatically disable Menu Image plugin filter' , 'megamaxmenu' ),
		'desc'	=> __( 'MegaMaxMenu will automatically attempt to disable the filter in the Menu Image plugin that destroys the MegaMaxMenu markup.  You should still be sure to disable the Menu Image plugin.  MegaMaxMenu has its own image functionality built in.', 'megamaxmenu' ),
		'type'	=> 'checkbox',
		'default' => 'on',
		'group'	=> 'plugin_compatibility',
	);

	$all_fields[MEGAMAXMENU_PREFIX.'general'] = $fields;

	return $all_fields;
}


/** FUNCTIONALITY **/
add_action( 'wp' , 'megamaxmenu_kill_menu_image_plugin' );
function megamaxmenu_kill_menu_image_plugin(){
  global $menu_image;
  if( megamaxmenu_is_plugin_active_menu_image() && isset( $menu_image ) && ( 'off' != megamaxmenu_op( 'plugin_compatibility_menu_image_autodisable' , 'general' ) ) ){
    remove_action( 'walker_nav_menu_start_el', array( $menu_image, 'menu_image_nav_menu_item_filter' ), 10, 4 );
  }
}
