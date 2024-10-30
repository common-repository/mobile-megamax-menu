<?php
function megamaxmenu_get_settings_fields_instance( $config_id ){

	$settings = array(

		//Integration


		//Basic

		60 => array(
			'name'	=> 'header_basic',
			'label'	=> __( 'Basic Configuration' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'basic',
		),



		70 => /* SKIN */
		array(
			'name'	=> 'skin',
			'label'	=> __( 'Skin' , 'megamaxmenu' ),
			'type'	=> 'select',
			'desc'	=> __( 'If you disable the skin, you must provide your own custom skin.  Otherwise, use the Minimal Base or Vanilla skin as a Customizer base.' , 'megamaxmenu' ),
			//'options'	=> array(),
			'options' => 'megamaxmenu_get_skin_ops',
			'default' => 'black-white-2',
			'group'	=> 'basic',
			'customizer' => true,
		),

		80 => array(
			'name'	=> 'orientation',
			'label'	=> __( 'Orientation' , 'megamaxmenu' ),
			'type'	=> 'radio',
			'desc'	=> __( 'Orient the menu vertically or horizontally' , 'megamaxmenu' ) . '<br/><a target="_blank" href="https://megamax.menu">Vertical Menu Demo</a>',
			'options'=> array(
				//'auto'		=> __( 'Automatic' , 'megamaxmenu' ),

				'horizontal'	=> __( 'Horizontal', 'megamaxmenu' ),
				'vertical'	 	=> __( 'Vertical', 'megamaxmenu' ),
			),
			'default'=> 'horizontal',
			'group'	=> 'basic',

			'customizer'	=> true,
			'customizer_section' => 'menu_bar',

		),

		90 => array(
			'name'	=> 'vertical_submenu_width',
			'label'	=> __( 'Vertical Menu Mega Submenu Width' , 'megamaxmenu' ),
			'type'	=> 'text',
			'default'=> '',
			'desc'	=> __( '600px by default.  Can be overridden on a per submenu basis in the Menu Item Settings.' , 'megamaxmenu' ),
			'group'	=> 'basic',
			'custom_style' => 'vertical_submenu_width',
		),


		/* TRIGGER */
		100 => array(
			'name'	=> 'trigger_header',
			'label'	=> __( 'Trigger' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'basic',
		),

		110 => array(
			'name'	=> 'trigger',
			'label'	=> __( 'Trigger' , 'megamaxmenu' ),
			'type'	=> 'radio',
			'desc'	=> __( 'Open the submenu via this trigger' , 'megamaxmenu' ),
			'options'=> array(
				//'auto'		=> __( 'Automatic' , 'megamaxmenu' ),

				'hover' 		=> __( 'Hover', 'megamaxmenu' ),
				'hover_intent' 	=> __( 'Hover Intent', 'megamaxmenu' ),
				'click'			=> __( 'Click', 'megamaxmenu' ),
			),
			'default'=> 'hover_intent',
			'group'	=> 'basic',

			'customizer' => true,
		),






		/* TRANSITION */
		120 => array(
			'name'	=> 'transition_header',
			'label'	=> __( 'Dropdown Transitions' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'basic',
		),

		130 => array(
			'name'	=> 'transition',
			'label'	=> __( 'Transition' , 'megamaxmenu' ),
			'desc'	=> __( 'Transitions supported in Chrome, Safari, Firefox, IE10+', 'megamaxmenu' ),
			'type'	=> 'radio',
			'options'=> array(
				'none'		=> __( 'None' , 'megamaxmenu' ),
				'slide' 	=> __( 'Slide Reveal', 'megamaxmenu' ),
				'fade'		=> __( 'Fade', 'megamaxmenu' ),
				'shift' 	=> __( 'Shift Up', 'megamaxmenu' ),
			),
			'default'=> 'shift',
			'group'	=> 'basic',

			'customizer' => true,
			'customizer_section' => 'submenu',
		),


		140 => array(
			'name'	=> 'transition_duration',
			'label'	=> __( 'Transition Duration' , 'megamaxmenu' ),
			'type'	=> 'text',
			'default'=> '',
			'desc'	=> __( 'You can use <code>.5s</code> or <code>500ms</code>.  Defaults to .3s' , 'megamaxmenu' ),
			'group'	=> 'basic',
			'custom_style' => 'transition_duration',
		),



		//Position & Layout

		//Menu Items Alignment







		/* DESCRIPTIONS */
		240 => array(
			'name'	=> 'header_descriptions',
			'label'	=> __( 'Descriptions' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'descriptions',
		),


		250 => array(
			'name'		=> 'descriptions_top_level',
			'label'		=> __( 'Top Level Descriptions' , 'megamaxmenu' ),
			'desc'		=> __( 'Allow descriptions on top level menu items.' , 'megamaxmenu' ),
			'type'		=> 'checkbox',
			'default' 	=> 'on',
			'group'	=> 'descriptions',
		),

		260 => array(
			'name'		=> 'descriptions_headers',
			'label'		=> __( 'Header Item Descriptions' , 'megamaxmenu' ),
			'desc'		=> __( 'Allow descriptions on header menu items.' , 'megamaxmenu' ),
			'type'		=> 'checkbox',
			'default' 	=> 'on',
			'group'	=> 'descriptions',
		),

		270 => array(
			'name'		=> 'descriptions_normal',
			'label'		=> __( 'Normal Item Descriptions' , 'megamaxmenu' ),
			'desc'		=> __( 'Allow descriptions on normal menu items.' , 'megamaxmenu' ),
			'type'		=> 'checkbox',
			'default' 	=> 'on',
			'group'	=> 'descriptions',
		),

		275 => array(
			'name'		=> 'descriptions_tab',
			'label'		=> __( 'Tab Item Descriptions' , 'megamaxmenu' ),
			'desc'		=> __( 'Allow descriptions on tab toggle menu items.' , 'megamaxmenu' ),
			'type'		=> 'checkbox',
			'default' 	=> 'on',
			'group'	=> 'descriptions',
		),

		280 => array(
			'name'		=> 'target_divider',
			'label'		=> __( 'Target Divider' , 'megamaxmenu' ),
			'desc'		=> __( 'The character(s) separating the title from the description.  This will not be visible, but is useful for screen readers.' , 'megamaxmenu' ),
			'type'		=> 'text',
			'default' 	=> ' &ndash; ',
			'group'	=> 'descriptions',
			'sanitize_callback' => 'megamaxmenu_allow_html'
		),






		//Submenus

		//Images

		//Background Images






		/** RESPONSIVE **/

		400 => array(
			'name'	=> 'header_responsive',
			'label'	=> __( 'Responsive &amp; Mobile' , 'megamaxmenu' ),
			'type'	=> 'header',
			'desc'	=> __( 'Settings for responsiveness &amp; mobile devices' ),
			'group'	=> 'responsive',
		),

		410 => array(
			'name' 		=> 'responsive',
			'label' 	=> __( 'Responsive Menu', 'megamaxmenu' ),
			'desc' 		=> __( 'Uncheck this if you do not want a responsive menu.', 'megamaxmenu' ),
			'type' 		=> 'checkbox',
			'default' 	=> 'on',
			'group'		=> 'responsive',
		),

		415 => array(
			'name'		=> 'responsive_columns',
			'label'		=> __( 'Top Level Responsive Columns (Tablet)' , 'megamaxmenu' ),
			'desc'		=> __( 'By default, the top level menu items will appear in two (2) columns from 480px to your breakpoint (960 by default) - approximately tablet-size.  If you\'d like them to appear in a single column instead, you can change that here.  This will only affect top level menu items.' , 'megamaxmenu' ),
			'type'		=> 'radio',
			'options'	=> array(
				2		=> __( 'Two (2) Columns' , 'megamaxmenu' ),
				1		=> __( 'One (1) Column' , 'megamaxmenu' ),
			),
			'default'	=> 2,
			'group'		=> 'responsive',
		),

		416 => array(
			'name'		=> 'responsive_submenu_columns',
			'label'		=> __( 'Submenu Responsive Columns (Tablet)' , 'megamaxmenu' ),
			'desc'		=> __( 'By default, the mega submenu items will appear in two (2) columns from 480px to your breakpoint (960 by default) - approximately tablet-size.  If you\'d like them to appear in a single column instead, you can change that here.  This will only affect standard submenu items in mega submenus.' , 'megamaxmenu' ),
			'type'		=> 'radio',
			'options'	=> array(
				2		=> __( 'Two (2) Columns' , 'megamaxmenu' ),
				1		=> __( 'One (1) Column' , 'megamaxmenu' ),
			),
			'default'	=> 2,
			'group'		=> 'responsive',
		),


		420 => array(
			'name' 		=> 'responsive_toggle',
			'label' 	=> __( 'Responsive Toggle', 'megamaxmenu' ),
			'desc' 		=> __( 'Automatically display a responsive toggle for this menu.', 'megamaxmenu' ),
			'type' 		=> 'checkbox',
			'default' 	=> 'on',
			'group'		=> 'responsive',
		),

		422 => array(
			'name' 		=> 'responsive_toggle_tag',
			'label' 	=> __( 'Responsive Toggle Tag', 'megamaxmenu' ),
			'desc' 		=> __( 'Anchor by default.', 'megamaxmenu' ),
			'type' 		=> 'radio',
			'default' 	=> 'a',
			'options'		=> array(
				'a'		=> '&lt;a&gt;',
				'div'	=> '&lt;div&gt;',
				'span'	=> '&lt;span&gt;',
				'button'=> '&lt;button&gt;',
			),
			'group'		=> 'responsive',
		),

		430 => array(
			'name' 		=> 'responsive_toggle_content',
			'label' 	=> __( 'Responsive Toggle Content', 'megamaxmenu' ),
			'desc' 		=> __( 'The text to display on the responsive toggle.', 'megamaxmenu' ),
			'type' 		=> 'text',
			'default' 	=> __( 'Menu', 'megamaxmenu' ),
			'group'		=> 'responsive',
			'sanitize_callback' => 'megamaxmenu_allow_html',
			'customizer'	=> true,
			'customizer_section' => 'toggle',
		),

		435 => array(
			'name' 		=> 'responsive_toggle_content_alignment',
			'label' 	=> __( 'Responsive Toggle Content Alignment', 'megamaxmenu' ),
			'desc' 		=> __( 'Alignment of the content within the toggle', 'megamaxmenu' ),
			'type' 		=> 'radio',
			'options'	=> array(
								'left'		=> __( 'Left' , 'megamaxmenu' ),
								'center'	=> __( 'Center' , 'megamaxmenu' ),
								'right'		=> __( 'Right' , 'megamaxmenu' ),
							),
			'default' 	=> 'left',
			'group'		=> 'responsive',
			'customizer'	=> true,
			'customizer_section' => 'toggle',
		),

		437 => array(
			'name' 		=> 'responsive_toggle_alignment',
			'label' 	=> __( 'Responsive Toggle Alignment', 'megamaxmenu' ),
			'desc' 		=> __( 'Alignment of the toggle button', 'megamaxmenu' ),
			'type' 		=> 'radio',
			'options'	=> array(
								'full'	=> __( 'Full Width' , 'megamaxmenu' ),
								'left'		=> __( 'Left' , 'megamaxmenu' ),
								'right'		=> __( 'Right' , 'megamaxmenu' ),
							),
			'default' 	=> 'full',
			'group'		=> 'responsive',
			'customizer'	=> true,
			'customizer_section' => 'toggle',
		),

		440 => array(
			'name' 		=> 'responsive_collapse',
			'label' 	=> __( 'Collapse by default', 'megamaxmenu' ),
			'desc' 		=> __( 'Uncheck this if you do not want a responsive toggle, but just want to see all top level menu items on mobile.', 'megamaxmenu' ),
			'type' 		=> 'checkbox',
			'default' 	=> 'on',
			'group'		=> 'responsive',
		),

		450 => array(
			'name'	=> 'responsive_max_height',
			'label'	=> __( 'Responsive Max Height (px)' , 'megamaxmenu' ),
			'type'	=> 'text',
			'desc'	=> __( 'Adjusting this to the height of your responsive menu can make the transition smoother.  500 by default', 'megamaxmenu' ),
			'group'	=> 'responsive',
			'custom_style'	=> 'responsive_max_height',
		),


		/* Responsive Submenu */

		460 => array(
			'name'		=> 'responsive_submenu_settings',
			'label'		=> __( 'Responsive Submenus' , 'megamaxmenu' ),
			'type'		=> 'header',
			'group'		=> array( 'responsive' , 'submenus' ),
		),

		465 => array(
			'name' 		=> 'display_retractor_top',
			'label' 	=> __( 'Display Submenu Retractor [Top]', 'megamaxmenu' ),
			'desc' 		=> __( 'Display a "Close" button at the top of the submenu on mobile devices.', 'megamaxmenu' ),
			'type' 		=> 'checkbox',
			'default' 	=> 'off',
			'group'		=> 'submenus',
		),

		466 => array(
			'name' 		=> 'display_retractor_bottom',
			'label' 	=> __( 'Display Submenu Retractor [Bottom]', 'megamaxmenu' ),
			'desc' 		=> __( 'Display a "Close" button at the bottom of the submenu on mobile devices.', 'megamaxmenu' ),
			'type' 		=> 'checkbox',
			'default' 	=> 'off',
			'group'		=> 'submenus',
		),

		470 => array(
			'name' 		=> 'retractor_label',
			'label' 	=> __( 'Submenu Retractor Text', 'megamaxmenu' ),
			'desc' 		=> __( 'By default, the retractor will read "Close", and will be translatable.  You can override it here but it will no longer be translatable.', 'megamaxmenu' ),
			'type' 		=> 'text',
			'default' 	=> '',
			'group'		=> 'submenus',
		),



/*
		array(
			'name'	=> 'responsive_breakpoint',
			'label'	=> __( 'Responsive Breakpoint' , 'megamaxmenu' ),
			'type'	=> 'text',
			'desc'	=> __( '959 by default', 'megamaxmenu' ),
			'group'	=> 'responsive',
		),
*/



		);

	return apply_filters( 'megamaxmenu_instance_settings' , $settings , $config_id );
}


function megamaxmenu_get_settings_fields(){

	$prefix = MEGAMAXMENU_PREFIX;

	$settings_fields = _MEGAMAXMENU()->get_settings_fields();
	if( $settings_fields ) return $settings_fields;



	$main_assigned = '';
	if(!has_nav_menu('megamaxmenu')){
		$main_assigned = 'No Menu Assigned';
	}
	else{
    	$menus = get_nav_menu_locations();
    	$menu_title = wp_get_nav_menu_object($menus['megamaxmenu'])->name;
    	$main_assigned = $menu_title;
    }

    $main_assigned = '<span class="megamaxmenu-main-assigned">'.$main_assigned.'</span>  <p class="megamaxmenu-desc-understated">The menu assigned to the <strong>MegaMaxMenu [Main]</strong> theme location will be displayed.  <a href="'.admin_url( 'nav-menus.php?action=locations' ).'">Assign a menu</a></p>';

	$config_id = 'main';

	$fields = array(
		$prefix.$config_id => megamaxmenu_get_settings_fields_instance( $config_id )
	);


	$fields = apply_filters( 'megamaxmenu_settings_panel_fields' , $fields );


	//Allow ordering
	foreach( $fields as $section_id => $section_fields ){
		ksort( $fields[$section_id] );
		$fields[$section_id] = array_values( $fields[$section_id] );
	}

	_MEGAMAXMENU()->set_settings_fields( $fields );

	// up( $fields , 2 );

//up( $fields );
	return $fields;
}

function megamaxmenu_get_settings_defaults(){

	$pro_defaults = false;
	if( !megamaxmenu_is_pro() ){
		//Setup pro defaults
		$pro_defaults = array(
			'auto_theme_location'	=> '',
			'nav_menu_id'			=> '_none',
			'bar_align'				=> 'full',
			'bar_width'				=> '',
			'items_align'			=> 'left',
			'items_align_vertical'	=> 'bottom',
			'bar_inner_center'		=> 'off',
			'bar_inner_width'		=> '',
			'bound_submenus'		=> 'on',
			'submenu_inner_width'	=> '',
			'submenu_max_height'	=> '',
			'image_size'			=> 'full',
			'image_width'			=> '',
			'image_height'			=> '',
			'image_set_dimensions'	=> 'on',
			'image_title_attribute'	=> 'off',
			'submenu_background_image_reponsive_hide' => 'off',

			'google_font'			=> '',
			'google_font_style'		=> '',
			'custom_font_property'	=> '',

			'container_tag'			=> 'nav',
			'allow_shortcodes_in_labels' => 'off',
			'display_submenu_indicators' => 'on',
			'display_submenu_close_button' => 'off',
			'theme_location_instance'	=> 0,

			'responsive_columns'	=> 2,
		);
	}

	$fields = megamaxmenu_get_settings_fields();

	$settings_defaults = array();

	foreach( $fields as $section_id => $ops ){
		$section_defaults = array();

		foreach( $ops as $op ){
			if( $op['type'] == 'header' ) continue;
			$section_defaults[$op['name']] = isset( $op['default'] ) ? $op['default'] : '';
		}

		if( $pro_defaults !== false ){
			$section_defaults = array_merge( $section_defaults , $pro_defaults );
		}

		$settings_defaults[$section_id] = $section_defaults;
	}
	return apply_filters( 'megamaxmenu_settings_defaults' , $settings_defaults );
}

add_filter( 'megamaxmenu_settings_panel_fields' , 'megamaxmenu_settings_panel_fields_general' );
function megamaxmenu_settings_panel_fields_general( $fields ){


	$fields[MEGAMAXMENU_PREFIX.'general'] = array(

		/* Custom Styles */
		10 => array(
			'name'	=> 'header_custom_styles',
			'label'	=> __( 'Custom Styles' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'custom_css',
		),

		20 => array(
			'name'	=> 'custom_tweaks',
			'label'	=> __( 'Custom CSS Tweaks' , 'megamaxmenu' ),
			'type'	=> 'textarea',
			'group'	=> 'custom_css',
			'sanitize_callback' => 'megamaxmenu_allow_html',
		),

		30 => array(
			'name'	=> 'custom_tweaks_mobile',
			'label'	=> __( 'Custom CSS Tweaks - Mobile' , 'megamaxmenu' ),
			'desc'	=> __( 'Styles to apply below the responsive breakpoint only.' , 'megamaxmenu' ),
			'type'	=> 'textarea',
			'group'	=> 'custom_css',
			'sanitize_callback' => 'megamaxmenu_allow_html',
		),

		40 => array(
			'name'	=> 'custom_tweaks_desktop',
			'label'	=> __( 'Custom CSS Tweaks - Desktop' , 'megamaxmenu' ),
			'desc'	=> __( 'Styles to apply above the responsive breakpoint only.' , 'megamaxmenu' ),
			'type'	=> 'textarea',
			'group'	=> 'custom_css',
			'sanitize_callback' => 'megamaxmenu_allow_html',
		),









		/** Script Configuration **/
		170 => array(
			'name'	=> 'header_script_config',
			'label'	=> __( 'Script Configuration' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'script_config',
		),

		175	=> array(
			'name'	=> 'touch_off_close',
			'label'	=> __( 'Touch-off Close' , 'megamaxmenu' ),
			'desc'	=> __( 'Close all submenus when the user clicks or touches off of the menu.  If you disable this, make sure you leave your users with another way to close the submenu.' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'=> 'on',
			'group'	=> 'script_config',
		),

		177	=> array(
			'name'	=> 'submenu_indicator_close_mobile',
			'label'	=> __( 'Show Indicator Submenu Close Button on Mobile' , 'megamaxmenu' ),
			'desc'	=> __( 'When a submenu is toggled open on mobile, a close button will appear in place of the submenu indicator on the parent item.' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'=> 'on',
			'group'	=> 'script_config',
		),


		180 => array(
			'name'	=> 'reposition_on_load',
			'label' => __( 'Reposition Submenus on window.load' , 'megamaxmenu' ),
			'desc'	=> __( 'Reposition the Submenus after other assets have loaded.  Useful if using @font-face fonts in the menu.', 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'=> 'off',
			'group'	=> 'script_config',
		),

		185 => array(
			'name'	=> 'remove_conflicts',
			'label' => __( 'Remove JS Conflicts' , 'megamaxmenu' ),
			'desc'	=> __( 'This will disable any event bindings added with jQuery unbind() or off() before the MegaMaxMenu script runs.  If you wish to bind your own events, or have other scripts act on the menu, you may need to disable this.', 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'=> 'on',
			'group'	=> 'script_config',
		),


		190 => array(
			'name'	=> 'header_hoverintent',
			'label'	=> __( 'HoverIntent Settings' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'script_config',
		),
		191 => array(
			'name'	=> 'intent_delay',
			'label'	=> __( 'Hover Intent Delay' , 'megamaxmenu' ),
			'desc'	=> __( 'Time to wait until closing the submenu after hover-out (ms)' , 'megamaxmenu' ),
			'type'	=> 'text',
			'default'	=> 300,
			'group'	=> 'script_config',
		),

		195 => array(
			'name'	=> 'intent_interval',
			'label'	=> __( 'Hover Intent Interval' , 'megamaxmenu' ),
			'desc'	=> __( 'Polling interval for mouse comparisons (ms)' , 'megamaxmenu' ),
			'type'	=> 'text',
			'default'	=> 100,
			'group'	=> 'script_config',
		),

		200 => array(
			'name'	=> 'intent_threshold',
			'label'	=> __( 'Hover Intent Threshold' , 'megamaxmenu' ),
			'desc'	=> __( 'Maximum number of pixels over the target that the mouse can move (since the last poll) to be considered an intentional hover' , 'megamaxmenu' ),
			'type'	=> 'text',
			'default'	=> 7,
			'group'	=> 'script_config',
		),

		210 => array(
			'name'	=> 'header_scrollto',
			'label'	=> __( 'ScrollTo Settings' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'script_config',
		),
		211 => array(
			'name'	=> 'scrollto_offset',
			'label' => __( 'ScrollTo Offset' , 'megamaxmenu' ),
			'desc'	=> __( 'Pixel offset to leave when scrolling.', 'megamaxmenu' ),
			'type'	=> 'text',
			'default'=> 50,
			'group'	=> 'script_config',
		),

		215 => array(
			'name'	=> 'scrollto_duration',
			'label' => __( 'ScrollTo Duration' , 'megamaxmenu' ),
			'desc'	=> __( 'Duration of the scroll animation in milliseconds.  The actual speed will be determined by the distance that needs to be traveled.  <em>1000</em> is 1 second.', 'megamaxmenu' ),
			'type'	=> 'text',
			'default'=> 1000,
			'group'	=> 'script_config',
		),
		216 => array(
			'name'	=> 'scrollto_disable_current',
			'label' => __( 'Automatically Disable Current Item Classes on ScrollTo Items' , 'megamaxmenu' ),
			'desc'	=> __( 'If you have multiple ScrollTo Links on the same page, they will all be marked as current on that page, if this setting is disabled.', 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'=> 'on',
			'group'	=> 'script_config',
		),

		217 => array(
			'name'	=> 'collapse_after_scroll',
			'label'	=> __( 'Collapse Menu after Scroll To (Mobile)' , 'megamaxmenu' ),
			'desc'	=> __( 'When a ScrollTo-enabled item is clicked on mobile, collapse the menu after the scroll completes' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'=> 'on',
			'group'	=> 'script_config',
		),













		/** Admin Notices **/


		270 => array(
			'name'	=> 'header_misc',
			'label'	=> __( 'Miscellaneous' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'misc',
		),


		280 => array(
			'name'	=> 'admin_notices',
			'label'	=> __( 'Show Admin Notices' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'	=> 'on',
			'desc'	=> __( 'Display helpful notices - only to admins', 'megamaxmenu' ),
			'group'	=> 'misc',
		),


		285 => array(
			'name'	=> 'disable_custom_content',
			'label'	=> __( 'Disable Custom Content Areas' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'=> 'off',
			'desc'	=> __( 'Easy way to test if conflicts are coming from custom content added within your menu.' , 'megamaxmenu' ),
			'group'	=> 'misc',
		),
		286 => array(
			'name'	=> 'disable_widget_areas',
			'label'	=> __( 'Disable Widget Areas' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'=> 'off',
			'desc'	=> __( 'Easy way to test if conflicts are coming from widget content added within your menu.' , 'megamaxmenu' ),
			'group'	=> 'misc',
		),






		/** MAINTAINENCE **/
		330 => array(
			'name'	=> 'header_maintenance',
			'label'	=> __( 'Maintenance' , 'megamaxmenu' ),
			'desc'	=> '<i class="fas fa-exclamation-triangle"></i> '. __( 'You should only adjust settings in this section if you are certain of what you are doing.'  , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'maintenance',
		),

		335 => array(
			'name'	=> 'migrate_fa4_fa5',
			'label'	=> __( 'Migrate Font Awesome', 'megamaxmenu' ),
			'desc'	=> '<a class="button button-primary" href="'.admin_url('themes.php?page=megamaxmenu-settings&do=fa4_to_fa5&megamaxmenu_nonce='.wp_create_nonce( 'megamaxmenu-control-panel-do' ) ).'">'.__( 'Migrate Font Awesome 4 to 5' , 'megamaxmenu' ).'</a><br/><p>'.__( 'Migrate Font Awesome 4 Icons to Font Awesome 5', 'megamaxmenu' ).'</p>',
			'type'	=> 'html',
			'group'	=> 'maintenance',

		),

		340 => array(
			'name'	=> 'migration',
			'label'	=> __( 'Migrate Settings' , 'megamaxmenu' ),
			'desc'	=> '<a class="button button-primary" href="'.admin_url('themes.php?page=megamaxmenu-settings&do=migration-check&megamaxmenu_nonce='.wp_create_nonce( 'megamaxmenu-control-panel-do' ) ).'">'.__( 'Migrate Settings' , 'megamaxmenu' ).'</a><br/><p>'.__( 'Migrate MegaMaxMenu 2 Settings to MegaMaxMenu 3', 'megamaxmenu' ).'</p>',
			'type'	=> 'html',
			'group'	=> 'maintenance',
		),

		350 => array(
			'name'	=> 'reset_all',
			'label'	=> __( 'Reset ALL Settings' , 'megamaxmenu' ),
			'desc'	=> '<a class="button button-primary" href="'.admin_url('themes.php?page=megamaxmenu-settings&do=reset-all-check&megamaxmenu_nonce='.wp_create_nonce( 'megamaxmenu-control-panel-do' )).'">'.__( 'Reset Settings' , 'megamaxmenu' ).'</a><br/><p>'.__( 'Reset ALL Control Panel settings to the factory defaults.', 'megamaxmenu' ).'</p>',
			'type'	=> 'html',
			'group'	=> 'maintenance',
		),


		/** ACCESSIBILITY */
		500 => array(
			'name'	=> 'accessibility_header',
			'label' => __( 'Accessibility' , 'megamaxmenu' ),
			'desc'	=> __( '' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'accessibility',
		),
		510 => array(
			'name'	=> 'accessible',
			'label'	=> __( 'Enable Accessibility Features' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'	=> 'on',
			'desc'	=> __( 'Allow users to tab through the menu and highlight focused elements', 'megamaxmenu' ),
			'group'	=> 'accessibility',
		),
		520	=> array(
			'name'	=> 'skip_navigation',
			'label'	=> __( 'Skip Navigation Link' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'	=> 'off',
			'desc'	=> __( 'Add a hidden Skip Navigation link to assist those using screen readers.', 'megamaxmenu' ),
			'group'	=> 'accessibility',
		),
		//PROBLEM: different for each instance potentially
		// 525	=> array(
		// 	'name'	=> 'skip_navigation_target',
		// 	'label'	=> __( 'Skip Navigation Target' , 'megamaxmenu' ),
		// 	'type'	=> 'text',
		// 	'default'	=> '',
		// 	'desc'	=> __( 'Where to skip to.', 'megamaxmenu' ),
		// 	'group'	=> 'accessibility',
		// ),
		540	=> array(
			'name'	=> 'aria_header',
			'label' => __( 'ARIA' , 'megamaxmenu' ),
			'desc'	=> __( 'ARIA settings' , 'megamaxmenu' ),
			'type'	=> 'header',
			'group'	=> 'accessibility',
		),
		550	=> array(
			'name'	=> 'aria_role_navigation',
			'label'	=> __( 'role="navigation"' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'	=> 'off',
			'desc'	=> __( 'Add role="navigation" to the navigation container.  Note that this is redundant when using the HTML5 nav tag.', 'megamaxmenu' ),
			'group'	=> 'accessibility',
		),
		551	=> array(
			'name'	=> 'aria_nav_label',
			'label'	=> __( 'aria-label for nav' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'	=> 'off',
			'desc'	=> __( 'Add aria-label="{menu title}" to the nav tag.  The title of your menu assigned in Appearance - Menus will be used for this value', 'megamaxmenu' ),
			'group'	=> 'accessibility',
		),
		// 560	=> array(
		// 	'name'	=> 'aria_role_button',
		// 	'label'	=> __( 'role="button"' , 'megamaxmenu' ),
		// 	'type'	=> 'checkbox',
		// 	'default'	=> 'off',
		// 	'desc'	=> __( 'Add role="button" to items that toggle hidden panels.', 'megamaxmenu' ),
		// 	'group'	=> 'accessibility',
		// ),
		570	=> array(
			'name'	=> 'aria_expanded',
			'label'	=> __( 'aria-expanded' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'	=> 'off',
			'desc'	=> __( 'Add the aria-expanded attribute to activated anchors and their submenus when open', 'megamaxmenu' ),
			'group'	=> 'accessibility',
		),
		580	=> array(
			'name'	=> 'aria_hidden',
			'label'	=> __( 'aria-hidden [submenus]' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'	=> 'off',
			'desc'	=> __( 'Add the aria-hidden attribute to hidden submenus', 'megamaxmenu' ),
			'group'	=> 'accessibility',
		),
		// 590	=> array(
		// 	'name'	=> 'aria_controls',
		// 	'label'	=> __( 'aria-controls' , 'megamaxmenu' ),
		// 	'type'	=> 'checkbox',
		// 	'default'	=> 'off',
		// 	'desc'	=> __( 'Add the aria-controls attribute to anchors with submenus.  This necessitates adding an ID to each submenu', 'megamaxmenu' ),
		// 	'group'	=> 'accessibility',
		// ),
		594 => array(
			'name'	=> 'aria_responsive_toggle',
			'label'	=> __( 'ARIA for responsive toggle' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'	=> 'off',
			'desc'	=> __( 'Add role="button", aria-controls, aria-hidden, and aria-expanded attributes to responsive toggle.  Recommended: change the Responsive Toggle Tag setting to BUTTON', 'megamaxmenu' ),
			'group'	=> 'accessibility',
		),
		595	=> array(
			'name'	=> 'aria_hidden_icons',
			'label'	=> __( 'aria-hidden [icons]' , 'megamaxmenu' ),
			'type'	=> 'checkbox',
			'default'	=> 'off',
			'desc'	=> __( 'Add the aria-hidden attribute to Font Awesome icons.  You should enable this if your icons are purely decorative.', 'megamaxmenu' ),
			'group'	=> 'accessibility',
		),



	);


	return $fields;
}

add_action( 'admin_init' , 'megamaxmenu_reset_settings' , 100 );
function megamaxmenu_reset_settings(){

	if( isset( $_GET['page'] ) && $_GET['page'] == 'megamaxmenu-settings' ){

		if( !current_user_can( 'manage_options' ) ){
			die( 'You need to be an admin to do that' );
		}

		if( isset( $_GET['do'] ) && $_GET['do'] == 'reset-all' ){

			$instances = megamaxmenu_get_menu_instances( true );
			foreach( $instances as $config_id ){
				delete_option( MEGAMAXMENU_PREFIX.$config_id );
			}
			delete_option( MEGAMAXMENU_PREFIX.'general' );
			megamaxmenu_save_all_menu_styles();

		}
		else if( isset( $_GET['do'] ) && $_GET['do'] == 'reset-styles' ){

			$instances = megamaxmenu_get_menu_instances( true );
			$all_fields = megamaxmenu_get_settings_fields();
			foreach( $instances as $config_id ){
				$ops = megamaxmenu_get_instance_options( $config_id );
				$fields = $all_fields[MEGAMAXMENU_PREFIX.$config_id];
				//up( $fields , 2 );
				foreach( $fields as $field ){
					if( $field['group'] == 'style_customizations' && $field['type'] != 'header' ){
						$ops[$field['name']] = isset( $field['default'] ) ? $field['default'] : '';
					}
				}
				//up( $ops );
				update_option( MEGAMAXMENU_PREFIX.$config_id , $ops );
				megamaxmenu_save_all_menu_styles();

			}

		}
	}
}

function megamaxmenu_get_settings_sections(){

	$prefix = MEGAMAXMENU_PREFIX;

	$sections = array(

		array(
			'id' => $prefix.'main',
			'title' => __( 'Main MegaMaxMenu Configuration', 'megamaxmenu' ),
			'sub_sections'	=> megamaxmenu_get_settings_subsections( 'main' ),
		),

	);

	$sections = apply_filters( 'megamaxmenu_settings_panel_sections' , $sections );

	return $sections;

}

add_filter( 'megamaxmenu_settings_panel_sections' , 'megamaxmenu_general_settings_tab' , 80 );
function megamaxmenu_general_settings_tab( $sections ){
	$prefix = MEGAMAXMENU_PREFIX;
	$section = array(
		'id'	=> $prefix.'general',
		'title'	=> __( 'General Settings' , 'megamaxmenu' ),
		'sub_sections'	=> array(

			'custom_css'=> array(
				'title'	=> __( 'Custom CSS' , 'megamaxmenu' ),
			),
			'script_config'=> array(
				'title'	=> __( 'Script Configuration' , 'megamaxmenu' ),
			),
			'misc'=> array(
				'title'	=> __( 'Miscellaneous' , 'megamaxmenu' ),
			),
			'maintenance'=> array(
				'title'	=> __( 'Maintenance', 'megamaxmenu' ),
			),
			'accessibility'=> array(
				'title'	=> __( 'Accessibility', 'megamaxmenu' ),
			),

		),
	);

	$section = apply_filters( 'megamaxmenu_general_settings_sections' , $section );

	$sections[] = $section;

	return $sections;
}

function megamaxmenu_get_settings_subsections( $config_id ){
	return apply_filters( 'megamaxmenu_settings_subsections' ,
		array(
			'basic' => array(
				'title' => __( 'Basic Configuration' , 'megamaxmenu' ),
			),
			'descriptions'	=> array(
				'title'	=> __( 'Descriptions' , 'megamaxmenu' ),
			),
			'responsive'	=> array(
				'title'	=> __( 'Responsive & Mobile' , 'megamaxmenu' ),
			),
		),
		$config_id
	);
}

/**
 * Registers settings section and fields
 */
function megamaxmenu_admin_init() {

	$prefix = MEGAMAXMENU_PREFIX;

 	$sections = megamaxmenu_get_settings_sections();
 	$fields = megamaxmenu_get_settings_fields();

 	//set up defaults so they are accessible
	_MEGAMAXMENU()->set_defaults();


	$settings_api = _MEGAMAXMENU()->settings_api();

	//set sections and fields
	$settings_api->set_sections( $sections );
	$settings_api->set_fields( $fields );

	//initialize them
	$settings_api->admin_init();

}
add_action( 'admin_init', 'megamaxmenu_admin_init' );

function megamaxmenu_init_frontend_defaults(){
	if( !is_admin() ){
		_MEGAMAXMENU()->set_defaults();
	}
}
add_action( 'init', 'megamaxmenu_init_frontend_defaults' );

/**
 * Register the plugin page
 */
function megamaxmenu_admin_menu() {
	add_menu_page(
		'MegaMaxMenu Settings',
		'MegaMaxMenu',
		'manage_options',
		'megamaxmenu-settings',
		'megamaxmenu_control_panel',
        MEGAMAXMENU_URL . 'admin/assets/images/icon.png'//'megamaxmenu_settings_panel',

	);
}

add_action( 'admin_menu', 'megamaxmenu_admin_menu' );





/**
 * Display the plugin settings options page
 */
function megamaxmenu_control_panel() {

	if( !isset( $_GET['do'] ) ){
		megamaxmenu_settings_panel();
	}
	else{

		check_admin_referer( 'megamaxmenu-control-panel-do' , 'megamaxmenu_nonce' );

		switch( $_GET['do'] ){
			case 'fa4_to_fa5':
				megamaxmenu_fa4_to_fa5_panel();
				break;
			case 'widget-manager':
				megamaxmenu_widget_manager_panel();
				break;
			case 'migration-check':
				megamaxmenu_migration_panel();
				break;
			case 'migrate':
				megamaxmenu_migration_complete_panel();
				break;
			case 'reset-all-check':
				megamaxmenu_reset_all_check_panel();
				break;
			case 'reset-all':
				megamaxmenu_reset_all_complete_panel();
				break;

			case 'reset-styles-check':
				megamaxmenu_reset_styles_check_panel();
				break;
			case 'reset-styles':
				megamaxmenu_reset_styles_complete_panel();
				break;

			case 'settings-import':
				megamaxmenu_settings_import_panel();
				break;

			case 'no-migrate':
				//

			case 'reset-migration-check':
				//

			default:
				megamaxmenu_settings_panel();
				break;
		}
	}
}

function megamaxmenu_settings_import_panel(){

	?>
	<div class="wrap megamaxmenu-wrap">

		<h2><strong>MegaMaxMenu</strong> Settings Importer <span class="megamaxmenu-version">(alpha)</span></h2>

		<?php settings_errors(); ?>

		<?php


		//ACTUAL IMPORT ACTION
		if( isset( $_POST['megamaxmenu-settings-import-json'] ) && check_admin_referer( 'megamaxmenu-control-panel-do' , 'megamaxmenu_nonce' ) ){ //check_admin_referer( 'megamaxmenu-settings-import' , 'megamaxmenu_nonce' )

			$config_id = sanitize_text_field( $_POST['config_id'] );
			$json = stripslashes( $_POST['megamaxmenu-settings-import-json'] );

			if( !$json ){
				echo '<div class="error"><p>'.__( 'Please paste the data to import.' , 'megamaxmenu' ).'</p></div>';
			}
			else{
				$settings = json_decode( $json , true );	//decode into array
				if( !$settings ){
					echo '<div class="error"><p>'.__( 'Could not interpret import data.  Must be a valid JSON string.' , 'megamaxmenu' ).'</p></div>';
					echo '<pre style="border:1px solid #ddd; background:#eee; padding:10px; ">'.$json.'</pre>';
				}
				else{
					//megamaxp( $settings );
					update_option( MEGAMAXMENU_PREFIX.$config_id , $settings );
					echo '<div class="updated"><p>'.__( 'Settings imported successfully.' , 'megamaxmenu' ).'</p></div>';
				}
			}

		}

		//IMPORT DATA PANEL (PASTE EXPORT DATA)
		else{
			$config_id = sanitize_text_field( $_GET['config_id'] );
			if( $config_id == 'main' ): ?>
				<h3><?php _e( 'Import Settings for Main MegaMaxMenu Configuration' , 'megamaxmenu' ); ?></h3>
			<?php else: ?>
				<h3><?php _e( 'Import Settings for Configuration:' , 'megamaxmenu' ); ?> +<?php echo esc_html( $config_id ); ?></h3>
			<?php endif; ?>

			<?php
				echo '<div class="error"><p>'.__( 'This operation will overwrite all settings for this Configuration.  This cannot be undone.  This is the only warning/confirmation screen' , 'megamaxmenu' ).'</p></div>';
			?>

				<form action="<?php echo admin_url( 'themes.php?page=megamaxmenu-settings&do=settings-import' ); ?>" method="POST">
					<input type="hidden" name="page" value="megamaxmenu-settings" />
					<input type="hidden" name="do" value="settings-import" />
					<input type="hidden" name="config_id" value="<?php echo esc_html( $config_id ); ?>" />
					<?php //wp_nonce_field( 'megamaxmenu-settings-import' , 'megamaxmenu_nonce' ); ?>
					<?php wp_nonce_field( 'megamaxmenu-control-panel-do' , 'megamaxmenu_nonce' ); ?>

					<p><?php _e( 'Paste your export data here to import the settings into this configuration' , 'megamaxmenu' ); ?></p>

					<textarea style="width:600px; height:200px" name="megamaxmenu-settings-import-json"></textarea>

					<br/>
					<input type="submit" class="button button-primary" value="<?php _e( 'Confirm and Import Settings Now' , 'megamaxmenu' ); ?>" />
				</form>

				<br/><br/>
			<?php
		}

		megamaxmenu_admin_back_to_settings_button();

		?>
	</div>
	<?php
}

function megamaxmenu_migration_complete_panel(){

	?>
	<div class="wrap megamaxmenu-wrap">



		<h2><strong>MegaMaxMenu</strong> Migration <span class="megamaxmenu-version">v<?php echo MEGAMAXMENU_VERSION; ?></span></h2>

		<?php settings_errors(); ?>

		<?php

			$migration_status = get_option( MEGAMAXMENU_PREFIX.'migration_status' , false );

			//Settings Don't Exist
			if( $migration_status == 'complete' ){
				echo '<div class="updated"><p>'.__( 'Migration completed successfully!' , 'megamaxmenu' ).'</p></div>';
			}
			else if( $migration_status == 'in_progress' ){
				echo '<div class="error"><p>'.__( 'Migration error.  You may need to disable safe_mode to complete the migration.' , 'megamaxmenu' ).'</p></div>';
			}
			else{
				echo 'Ruh-roh, Reorge. Something\'s not right.';
				echo '<br/>migration_status: '.$migration_status;
			}

		?>
		<br/><br/>
		<?php megamaxmenu_admin_back_to_settings_button(); ?>
	</div>
	<?php
}

function megamaxmenu_migration_panel(){

	//megamaxmenu_migrate_item_settings();

	?>
	<div class="wrap megamaxmenu-wrap">

		<h2><strong>MegaMaxMenu</strong> Migration <span class="megamaxmenu-version">v<?php echo MEGAMAXMENU_VERSION; ?></span></h2>

		<?php
			$old_ops = get_option( 'wp-mega-menu-settings' , false ); // 'sparkops_megamaxmenu' );

			//Settings Don't Exist
			if( !$old_ops ){
				echo '<div class="error"><p>'.__( 'Sorry, couldn\'t find any MegaMaxMenu 2 settings to migrate' , 'megamaxmenu' ).'</p></div>';
				return;
			}
			else{
				echo '<div class="updated"><p>'.__( 'MegaMaxMenu 2 Settings Found' , 'megamaxmenu' ).'</p></div>';
				//megamaxp( $old_ops );

				echo '<div class="error"><p>'.__( 'Migrating will merge your MegaMaxMenu 2 settings into MegaMaxMenu 3, giving precedence to MegaMaxMenu 2 settings. This may overwrite existing MegaMaxMenu 3 settings.  Please be sure you wish to proceed, as this process is cannot be undone.  The process can take some time to complete.' , 'megamaxmenu' ).'</p></div>';


				?>
				<form action="<?php echo admin_url( 'themes.php' ); ?>" method="GET">
					<input type="hidden" name="page" value="megamaxmenu-settings" />
					<input type="hidden" name="do" value="migrate" />
					<?php wp_nonce_field( 'megamaxmenu-control-panel-do' , 'megamaxmenu_nonce' ); ?>

					<!-- <h4>Style Generator</h4> -->

					<br/>
					<h3>Migrate Control Panel Settings</h3>

					<label><input type="checkbox" checked="checked" value="on" name="migrate_control_panel" /> Migrate Control Panel</label>

					<p>This will migrate the settings from the MegaMaxMenu Control Panel, including the Style Generator settings if you had your Style Application set to the Style Generator.</p>

					<br/>
					<h3>Migrate Menu Item Settings</h3>

					<h4>Which Menus do you wish to migrate?</h4>

					<?php
						$menus = wp_get_nav_menus( array('orderby' => 'name') );
						foreach( $menus as $menu ): ?>
							<label><input type="checkbox" checked="checked" name="migrate_menu_ids[]" value="<?php echo $menu->term_id; ?>" /> <?php echo $menu->name; ?></label><br/>
						<?php endforeach; ?>

					<br/>
					<input type="submit" class="button button-primary" value="<?php _e( 'Confirm &amp; Migrate Settings' , 'megamaxmenu' ); ?>" />
				</form>

				<br/><br/>
				<?php
			}

			megamaxmenu_admin_back_to_settings_button();

		?>



	</div>
	<?php
}



function megamaxmenu_admin_back_to_settings_button(){
	?>
	<a class="button" href="<?php echo admin_url('themes.php?page=megamaxmenu-settings'); ?>">&laquo; Back to Control Panel</a>
	<?php
}



function megamaxmenu_reset_styles_complete_panel(){

	//megamaxmenu_migrate_item_settings();

	?>
	<div class="wrap megamaxmenu-wrap">



		<h2><strong>MegaMaxMenu</strong> Style Customization Settings Reset <span class="megamaxmenu-version">v<?php echo MEGAMAXMENU_VERSION; ?></span></h2>

		<?php settings_errors(); ?>

		<?php

			echo '<div class="updated"><p>'.__( 'Style Customizations reset complete!' , 'megamaxmenu' ).'</p></div>';

		?>
		<br/><br/>
		<?php megamaxmenu_admin_back_to_settings_button(); ?>
	</div>
	<?php
}



function megamaxmenu_reset_styles_check_panel(){

	//megamaxmenu_migrate_item_settings();

	?>
	<div class="wrap megamaxmenu-wrap">

		<h2><strong>MegaMaxMenu</strong> Style Customization Settings Reset <span class="megamaxmenu-version">v<?php echo MEGAMAXMENU_VERSION; ?></span></h2>

		<?php
			echo '<div class="error"><p>'.__( 'Please be sure you wish to proceed.  This will delete all Style Customizations (though not your custom CSS).  This cannot be undone.' , 'megamaxmenu' ).'</p></div>';


			?>
			<form action="<?php echo admin_url( 'themes.php' ); ?>" method="GET">
				<input type="hidden" name="page" value="megamaxmenu-settings" />
				<input type="hidden" name="do" value="reset-styles" />
				<?php wp_nonce_field( 'megamaxmenu-control-panel-do' , 'megamaxmenu_nonce' ); ?>

				<br/>
				<input type="submit" class="button button-primary" value="<?php _e( 'Confirm &amp; Reset Style Customizations' , 'megamaxmenu' ); ?>" />
			</form>
			<?php

		?>
		<br/><br/>
		<?php megamaxmenu_admin_back_to_settings_button(); ?>

	</div>
	<?php
}

function megamaxmenu_reset_all_complete_panel(){

	//megamaxmenu_migrate_item_settings();

	?>
	<div class="wrap megamaxmenu-wrap">



		<h2><strong>MegaMaxMenu</strong> Settings Reset <span class="megamaxmenu-version">v<?php echo MEGAMAXMENU_VERSION; ?></span></h2>

		<?php settings_errors(); ?>

		<?php

			echo '<div class="updated"><p>'.__( 'Settings reset complete!' , 'megamaxmenu' ).'</p></div>';

		?>
		<br/><br/>
		<?php megamaxmenu_admin_back_to_settings_button(); ?>
	</div>
	<?php
}



function megamaxmenu_reset_all_check_panel(){

	//megamaxmenu_migrate_item_settings();

	?>
	<div class="wrap megamaxmenu-wrap">

		<h2><strong>MegaMaxMenu</strong> Settings Reset <span class="megamaxmenu-version">v<?php echo MEGAMAXMENU_VERSION; ?></span></h2>

		<?php
			echo '<div class="error"><p>'.__( 'Please be sure you wish to proceed.  This will delete all Menu Settings, including Custom Styles, Instance Settings and General Settings.  This cannot be undone.' , 'megamaxmenu' ).'</p></div>';


			?>
			<form action="<?php echo admin_url( 'themes.php' ); ?>" method="GET">
				<input type="hidden" name="page" value="megamaxmenu-settings" />
				<input type="hidden" name="do" value="reset-all" />
				<?php wp_nonce_field( 'megamaxmenu-control-panel-do' , 'megamaxmenu_nonce' ); ?>

				<br/>
				<input type="submit" class="button button-primary" value="<?php _e( 'Confirm &amp; Reset Settings' , 'megamaxmenu' ); ?>" />
			</form>
			<?php

		?>
		<br/><br/>
		<?php megamaxmenu_admin_back_to_settings_button(); ?>

	</div>
	<?php
}

function megamaxmenu_settings_panel(){
	if( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true' ){
		do_action( 'megamaxmenu_settings_panel_updated' );
	}

	$settings_api = _MEGAMAXMENU()->settings_api();

	echo '<div class="wrap megamaxmenu-wrap">';
	settings_errors();

	?>
	<div class="megamaxmenu-settings-links">
		<?php do_action( 'megamaxmenu_settings_before_title' ); ?>
	</div>
	<?php

	echo '<h1><strong>MegaMaxMenu</strong> Control Panel <span class="megamaxmenu-version">v'.MEGAMAXMENU_VERSION.'</span></h1>';

	do_action( 'megamaxmenu_settings_before' );

	$settings_api->show_navigation();
	$settings_api->show_forms();

	do_action( 'megamaxmenu_settings_after' );

	echo '</div>';
}

function megamaxmenu_settings_links(){
	echo '<a target="_blank" class="button button-secondary megamaxmenu-button-support" href="'.megamaxmenu_get_support_url().'"><i class="fas fa-life-ring"></i> Support</a> ';
}
add_action( 'megamaxmenu_settings_before_title' , 'megamaxmenu_settings_links' );




/**
 * Get the value of a settings field
 *
 * @param string $option settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 * @return mixed
 */
function megamaxmenu_op( $option, $section, $default = null ) {

	$options = get_option( MEGAMAXMENU_PREFIX.$section , array() );		//cached by WP

	//Value from settings
	if ( isset( $options[$option] ) ) {
		$val = $options[$option];
		//return $val;
	}
	//Default Fallback
	else{
		//No default passed
		if( $default === null ){
			//$default = _MEGAMAXMENU()->settings_api()->get_default( $option, MEGAMAXMENU_PREFIX.$section );
			$val = _MEGAMAXMENU()->get_default( $option, MEGAMAXMENU_PREFIX.$section );
		}
		//Use passed default
		else{
			$val = $default;
		}
	}

	$val = apply_filters( 'megamaxmenu_op' , $val , $option , $section );

	return $val;
}
function megamaxmenu_get_instance_options( $instance ){
	//echo MEGAMAXMENU_PREFIX.$instance;
	$defaults = _MEGAMAXMENU()->get_defaults( MEGAMAXMENU_PREFIX.$instance );
	$options = get_option( MEGAMAXMENU_PREFIX.$instance , $defaults );
	if( !is_array( $options ) || count( $options ) == 0 ) return $defaults;
	return $options;
}

function megamaxmenu_admin_panel_assets( $hook ){
	if( $hook == 'toplevel_page_megamaxmenu-settings' ){
		wp_enqueue_script( 'megamaxmenu-control-panel' , MEGAMAXMENU_URL . 'admin/assets/admin.settings.js' , array( 'jquery' ) , MEGAMAXMENU_VERSION , true );
		wp_enqueue_style( 'megamaxmenu-settings-styles' , MEGAMAXMENU_URL.'admin/assets/admin.settings.css' );

		//font awesome 4
		//wp_enqueue_style( 'megamaxmenu-font-awesome' , MEGAMAXMENU_URL.'assets/css/fontawesome/css/font-awesome.min.css' );

		//font awesome 5
		wp_enqueue_style( 'megamaxmenu-font-awesome-all' , 	MEGAMAXMENU_URL .'assets/fontawesome/css/all.min.css' , false , false );

		wp_localize_script( 'megamaxmenu-control-panel' , 'megamaxmenu_control_panel' , array(
			'load_google_cse'	=> megamaxmenu_op( 'load_google_cse' , 'general' ),
		) );
	}
}
add_action( 'admin_enqueue_scripts' , 'megamaxmenu_admin_panel_assets' );



function megamaxmenu_check_menu_assignment(){
	$display = megamaxmenu_op(  'display_main' , 'megamaxmenu-main' );

	if( $display == 'on' ){
		if( !has_nav_menu( 'megamaxmenu' ) ){
			?>
			<div class="update-nag"><strong>Important!</strong> There is no menu assigned to the <strong>MegaMaxMenu [Main]</strong> Menu Location.  <a href="<?php echo admin_url( 'nav-menus.php?action=locations' ); ?>">Assign a menu</a></div>
			<br/><br/>
			<?php
		}
	}
}
add_action( 'megamaxmenu_settings_before' , 'megamaxmenu_check_menu_assignment' );

function megamaxmenu_allow_html( $str ){
	return $str;
}








function megamaxmenu_new_default( $config_id , $new_default , $old_default ){
	//echo $config_id;
	$settings_exist = get_option( MEGAMAXMENU_PREFIX.$config_id , false );
	//megamaxp( $settings_exist );
	if( $settings_exist ){ //echo 'hi';
		return $old_default; }
	else return $new_default;

}
