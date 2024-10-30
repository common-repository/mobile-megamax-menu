<?php

/*
Plugin Name: Mobile MegaMax Menu
Plugin URI: https://megamax.menu/pricing
Description: Do you have issues creating your desired big, creative menu? We are here to help!
Author: MegaMax
Tested up to: 5.3
License: GPLv2
Author URI: https://megamax.menu
Version: 0.9
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Plugin version
define( 'MEGAMAXMENU_VERSION', '0.9' );

if ( !class_exists( 'MegaMaxMenu' ) ) :

final class MegaMaxMenu {

	/** Singleton *************************************************************/

	private static $instance;
	private static $settings_api;
	private static $skins;
	private static $settings_defaults;
	private static $settings_fields = false;

	private static $registered_icons;
	private static $registered_fonts;

	private static $support_url;

	private static $item_styles;

	private static $loaded_stylesheets = array();
	private static $loaded_scripts = array();

	private $current_config = 'main';

	private $theme_location_counts = array();
	private $main_taken = false;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new MegaMaxMenu;
			self::$instance->setup_constants();
			self::$instance->includes();
		}
		return self::$instance;
	}

	/**
	 * Setup plugin constants
	 *
	 * @since 1.0
	 * @access private
	 * @uses plugin_dir_path() To generate plugin path
	 * @uses plugin_dir_url() To generate plugin url
	 */
	private function setup_constants() {
		//Override in wp-config.php

		// Plugin File
		if( ! defined( 'MEGAMAXMENU_FILE' ) )
			define( 'MEGAMAXMENU_FILE', __FILE__ );

		// Plugin Folder URL
		if( ! defined( 'MEGAMAXMENU_URL' ) )
			define( 'MEGAMAXMENU_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Folder Path
		if( ! defined( 'MEGAMAXMENU_DIR' ) )
			define( 'MEGAMAXMENU_DIR', plugin_dir_path( __FILE__ ) );

		if( ! defined( 'MEGAMAXMENU_BASENAME' ) ){
			define( 'MEGAMAXMENU_BASENAME' , plugin_basename(__FILE__) );
		}

		if( ! defined( 'MEGAMAXMENU_BASEDIR' ) ){
			define( 'MEGAMAXMENU_BASEDIR' , dirname( plugin_basename(__FILE__) ) );
		}

		if( ! defined( 'MEGAMAXMENU_MENU_ITEM_META_KEY' ) )
			define( 'MEGAMAXMENU_MENU_ITEM_META_KEY' , '_megamaxmenu_settings' );

		if( ! defined( 'MEGAMAXMENU_MENU_ITEM_DEFAULTS_OPTION_KEY' ) )
			define( 'MEGAMAXMENU_MENU_ITEM_DEFAULTS_OPTION_KEY' , '_megamaxmenu_menu_item_settings_defaults' );




		define( 'MEGAMAXMENU_PREFIX' , 'megamaxmenu_' );
		define( 'MEGAMAXMENU_VERSION_KEY' , 'megamaxmenu_db_version' );

		define( 'MEGAMAXMENU_MENU_INSTANCES' , 'megamaxmenu_menus' );								//Key for instances

		define( 'MEGAMAXMENU_SKIN_GENERATOR_STYLES' , '_megamaxmenu_skin_generator_styles' );		//Key for Skin Gen Styles Array
		define( 'MEGAMAXMENU_MENU_STYLES' , '_megamaxmenu_menu_styles' );							//Key for Menu Styles Array
		define( 'MEGAMAXMENU_MENU_ITEM_STYLES' , '_megamaxmenu_menu_item_styles' );				//Key for Item Styles Array
		define( 'MEGAMAXMENU_MENU_ITEM_WIDGET_AREAS' , '_megamaxmenu_menu_item_widget_areas' );
		define( 'MEGAMAXMENU_WIDGET_AREAS' , '_megamaxmenu_widget_areas' );

		define( 'MEGAMAXMENU_GENERATED_STYLES_CHANGED' , '_megamaxmenu_generated_styles_changed' );

		define( 'MEGAMAXMENU_GENERATED_STYLE_TRANSIENT' , '_megamaxmenu_generated_styles' );
		if( ! defined( 'MEGAMAXMENU_GENERATED_STYLE_TRANSIENT_EXPIRATION' ) )
			define( 'MEGAMAXMENU_GENERATED_STYLE_TRANSIENT_EXPIRATION' , 30 * DAY_IN_SECONDS );

		//URLs
		define( 'MEGAMAXMENU_KB_URL' , 'https://megamax.menu' );
		define( 'MEGAMAXMENU_VIDEOS_URL' , 'https://megamax.menu' );
		define( 'MEGAMAXMENU_SUPPORT_URL' , 'https://megamax.menu' );
		define( 'MEGAMAXMENU_TROUBLESHOOTER_URL' , 'https://megamax.menu' );
		define( 'MEGAMAXMENU_QUICKSTART_URL' , 'https://megamax.menu' );

		if( ! defined( 'MEGAMAXMENU_TERM_COUNT_WRAP_START' ) )
			define( 'MEGAMAXMENU_TERM_COUNT_WRAP_START' , '(' );
		if( ! defined( 'MEGAMAXMENU_TERM_COUNT_WRAP_END' ) )
			define( 'MEGAMAXMENU_TERM_COUNT_WRAP_END' , ')' );

		if( ! defined( 'MEGAMAXMENU_ALLOW_NAV_MENU_ITEM_ARGS_FILTER' ) )
			define( 'MEGAMAXMENU_ALLOW_NAV_MENU_ITEM_ARGS_FILTER' , false );

		if( !defined( 'MEGAMAXMENU_ALLOW_TOP_LEVEL_DYNAMIC_ITEMS' ) )
			define( 'MEGAMAXMENU_ALLOW_TOP_LEVEL_DYNAMIC_ITEMS' , false );

		if( ! defined( 'MEGAMAXMENU_JS_IN_FOOTER' ) )
			define( 'MEGAMAXMENU_JS_IN_FOOTER' , true );


		define( 'MEGAMAXMENU_UPDATE_NOTICES_KEY' , '_megamaxmenu_update_errors' );

		define( 'MEGAMAXMENU_BOOSTER_PREFIX_CHANGED' , '_megamaxmenu_booster_prefix_changed' );
        if( ! defined( 'MEGAMAXMENU_PRO' ) ) define( 'MEGAMAXMENU_PRO', MEGAMAXMENU_VERSION === '9.0.0' );

	}

	private function includes() {

		require_once MEGAMAXMENU_DIR . 'includes/menuitems/menuitems.php';
		require_once MEGAMAXMENU_DIR . 'includes/MegaMaxMenuWalker.class.php';
		require_once MEGAMAXMENU_DIR . 'includes/functions.php';
		require_once MEGAMAXMENU_DIR . 'includes/icons.php';
		require_once MEGAMAXMENU_DIR . 'includes/customizer/customizer.php';
		require_once MEGAMAXMENU_DIR . 'includes/customizer/custom-styles.php';
		require_once MEGAMAXMENU_DIR . 'includes/megamaxmenu.api.php';
		require_once MEGAMAXMENU_DIR . 'includes/shortcodes.php';
		require_once MEGAMAXMENU_DIR . 'includes/item-limit-detection.php';
		require_once MEGAMAXMENU_DIR . 'includes/plugin_compatibility/plugin-compatibility.php';
		require_once MEGAMAXMENU_DIR . 'includes/elementor/elementor.php';

		require_once MEGAMAXMENU_DIR . 'admin/admin.php';
		require_once MEGAMAXMENU_DIR . 'admin/migration.php';

//		if( megamaxmenu_is_pro() ){
			require_once MEGAMAXMENU_DIR . 'pro/megamaxmenu.pro.php';
//		}

	}

	public function settings_api(){
		if( self::$settings_api == null ){
			self::$settings_api = new MegaMaxMenu_Settings_API();
		}
		return self::$settings_api;
	}

	public function get_skins(){
		return self::$skins;
	}
	public function get_skin_classes( $skin_id ){
		//megamaxp( self::$skins ,3);
		if( isset( self::$skins[$skin_id] ) && isset( self::$skins[$skin_id]['classes'] ) ){
			return self::$skins[$skin_id]['classes'];
		}
		return '';
	}
	public function register_skin( $id , $title , $src , $classes = '' ){
		if( self::$skins == null ){
			self::$skins = array();
		}
		self::$skins[$id] = array(
			'title'	=> $title,
			'src'	=> $src,
			'classes' => $classes,
		);

		wp_register_style( 'megamaxmenu-'.$id , $src );
	}
	public function deregister_skins(){
		if( self::$skins ){
			foreach( self::$skins as $id => $skin ){
				wp_deregister_style( 'megamaxmenu-' . $id );
			}
		}
	}

	public function set_defaults(){

		self::$settings_defaults = megamaxmenu_get_settings_defaults();

	}

	function get_defaults( $section = null ){
		if( self::$settings_defaults == null ) self::set_defaults();

		if( $section != null && isset( self::$settings_defaults[$section] ) ) return self::$settings_defaults[$section];

		return self::$settings_defaults;
	}

	function get_default( $option , $section ){

		if( self::$settings_defaults == null ) self::set_defaults();

		$default = '';

		if( isset( self::$settings_defaults[$section] ) && isset( self::$settings_defaults[$section][$option] ) ){
			$default = self::$settings_defaults[$section][$option];
		}
		return $default;
	}

	function register_icons( $group , $iconmap ){
		if( !is_array( self::$registered_icons ) ) self::$registered_icons = array();
		self::$registered_icons[$group] = $iconmap;
	}
	function deregister_icons( $group ){
		if( is_array( self::$registered_icons ) && isset( self::$registered_icons[$group] ) ){
			unset( self::$registered_icons[$group] );
		}
	}
	function get_registered_icons(){ //$group = '' ){
		return self::$registered_icons;
	}


	function register_font( $font_id , $font_ops ){
		if( !is_array( self::$registered_fonts ) ) self::$registered_fonts = array();
		self::$registered_fonts[$font_id] = $font_ops;
	}
	function degister_font( $font_id ){
		if( is_array( self::$registered_fonts ) && isset( self::$registered_fonts[$font_id] ) ){
			unset( self::$registered_fonts[$font_id] );
		}
	}
	function get_registered_fonts(){ //$group = '' ){
		if( !is_array( self::$registered_fonts ) ) self::$registered_fonts = array();
		return self::$registered_fonts;
	}


	function set_item_style( $item_id , $selector , $property_map ){
		//Get all stored menu item styles
		$item_styles = _MEGAMAXMENU()->get_item_styles( $item_id );

		//Initialize new array if this menu item doesn't have any rules yet
		if( !isset( self::$item_styles[$item_id] ) ){
			self::$item_styles[$item_id] = array();
		}

		if( $selector ){
			//Initialize new array if this selector doesn't exist yet
			if( !isset( self::$item_styles[$item_id][$selector] ) ){
				self::$item_styles[$item_id][$selector] = array();
			}

			if( is_array( $property_map ) ){
				//Add to the $properties array
				foreach( $property_map as $property => $value ){
					self::$item_styles[$item_id][$selector][$property] = $value;
				}
			}
		}

	}
	function get_item_styles( $reset_id = false ){
		if( !is_array( self::$item_styles ) ){
			self::$item_styles = get_option( MEGAMAXMENU_MENU_ITEM_STYLES , array() );
			if( $reset_id ){
				//reset the item's styles so we can re-save from scratch
				unset( self::$item_styles[$reset_id] );
			}
		}
		return self::$item_styles;
	}
	function update_item_styles(){
		if( is_array( self::$item_styles ) ){

			//Clear out empty arrays
			foreach( self::$item_styles as $item_id => $styles ){
				if( !is_array( $styles ) || empty( $styles ) ){
					unset( self::$item_styles[$item_id] );
				}
			}

			update_option( MEGAMAXMENU_MENU_ITEM_STYLES , self::$item_styles );
		}
		self::$item_styles = null;	//reset so we'll need to grab it again
	}



	function set_current_config( $config_id ){
		$this->current_config = $config_id;
	}
	function get_current_config(){
		return $this->current_config;
	}

	function count_theme_location( $theme_location ){
		if( !isset( $this->theme_location_counts[$theme_location] ) ){
			$this->theme_location_counts[$theme_location] = 0;
		}
		$this->theme_location_counts[$theme_location]++;
	}
	function get_theme_location_count( $theme_location ){
		return isset( $this->theme_location_counts[$theme_location] ) ? $this->theme_location_counts[$theme_location] : 0;
	}


	function get_settings_fields(){
		return self::$settings_fields;
	}
	function set_settings_fields( $fields ){
		self::$settings_fields = $fields;
	}


	function load_stylesheet( $handle , $src , $deps , $ver , $media ){

		wp_enqueue_style( $handle, $src, $deps, $ver, $media );
		self::$loaded_stylesheets[$handle] = true;
		// array(
		// 	'src' => $src,
		// 	'deps' => $deps,
		// 	'ver' => $ver,
		// 	'media' => $media,
		// );
		// echo 'wp styles';
		// global $wp_styles;
		// megamaxp( $wp_styles , 3 );
	}
	function get_loaded_stylesheets(){
		return self::$loaded_stylesheets;
	}

	function sanitize_data($data, $type='text') {
	    switch($type){
            case 'text':
                return sanitize_text_field($data);
                break;
            case 'absint':
                return absint($data);
                break;
            case 'html':
                return wp_kses_post($data);
                break;
            case 'arraytext':
                return array_map('sanitize_text_field', $data);
                break;
            case 'arrayabsint':
                return array_map('absint', $data);
                break;
            case 'arrayasoctext':
                array_walk($data, function(&$item, $key){
                    $item = sanitize_text_field($item);
                });
                break;
            case 'arrayasocabsint':
                array_walk($data, function(&$item, $key){
                    $item = absint($item);
                });
                break;
        }

        return $data;
    }

	function load_script( $handle , $src , $deps , $ver , $in_footer ){
		wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
		self::$loaded_scripts[$handle] = array(
			'src' => $src,
			'deps' => $deps,
			'ver' => $ver,
			'in_footer' => $in_footer,
		);
	}
	function get_loaded_scripts(){
		return self::$loaded_scripts;
	}


	function get_support_url(){

		if( self::$support_url ){
			return self::$support_url;
		}

		if(megamaxmenu_is_pro()){
		    $url = MEGAMAXMENU_SUPPORT_URL;
        } else {
		    $url = 'https://wordpress.org/support/plugin/megamaxmenu/';
        }

		self::$support_url = $url;

		return $url;
	}

}


endif; // End if class_exists check

if( !function_exists( '_MEGAMAXMENU' ) ){
	function _MEGAMAXMENU() {
		return MegaMaxMenu::instance();
	}
	_MEGAMAXMENU();
}
