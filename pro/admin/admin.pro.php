<?php

require_once( 'settings.pro.php' );
require_once( 'custom.menu-item-types.pro.php' );
require_once( 'settings.menu-item.pro.php' );
require_once( 'settings.instance-manager.php' );
require_once( 'prefix-booster.php' );

//require_once( 'item.settings.pro.php' );

add_action( 'admin_init' , 'megamaxmenu_remove_custom_admin_walker' );
function megamaxmenu_remove_custom_admin_walker(){
	if( megamaxmenu_op( 'disable_custom_admin_walker' , 'general' ) == 'on' ){
		remove_all_actions( 'wp_edit_nav_menu_walker' );
	}
}
