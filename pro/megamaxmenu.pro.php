<?php

require_once MEGAMAXMENU_DIR . 'pro/menuitems/menuitems.pro.php';
require_once MEGAMAXMENU_DIR . 'pro/search.php';
require_once MEGAMAXMENU_DIR . 'pro/maps.php';
require_once MEGAMAXMENU_DIR . 'pro/shortcodes.php';
require_once MEGAMAXMENU_DIR . 'pro/admin/admin.pro.php';
require_once MEGAMAXMENU_DIR . 'pro/toolbar.php';
require_once MEGAMAXMENU_DIR . 'pro/fonts.php';
require_once MEGAMAXMENU_DIR . 'pro/widgets.php';
require_once MEGAMAXMENU_DIR . 'pro/widget.php';
require_once MEGAMAXMENU_DIR . 'pro/diagnostics/diagnostics.php';
require_once MEGAMAXMENU_DIR . 'pro/sandbox/sandbox.php';

require_once MEGAMAXMENU_DIR . 'pro/thirdparty/loader.php';
//require_once MEGAMAXMENU_DIR . 'pro/admin/migration.php';


function megamaxmenu_pro_load_assets(){

	$assets = MEGAMAXMENU_URL . 'pro/assets/';

	//Load Core MegaMaxMenu CSS unless disabled
	if( megamaxmenu_op( 'load_megamaxmenu_css' , 'general' ) != 'off' ){
		wp_deregister_style( 'megamaxmenu' );
		//wp_dequeue_style( 'megamaxmenu' );
		$main_stylesheet = $assets.'css/megamaxmenu.min.css';
		if( megamaxmenu_op( 'custom_prefix' , 'general' ) ){
			$uploads_url = wp_upload_dir();
			$uploads_url = trailingslashit( $uploads_url['baseurl'] );
			$main_stylesheet = $uploads_url . 'megamaxmenu/megamaxmenu.prefix.css';
		}
		wp_enqueue_style( 'megamaxmenu' , $main_stylesheet , false , MEGAMAXMENU_VERSION );
	}
}

add_action( 'wp_enqueue_scripts' , 'megamaxmenu_pro_load_assets' , 22 );
