<?php

/***********

MENU STYLES

************/



///////////////////////////////////////
//////// THE MANAGEMENT
///////////////////////////////////////


function megamaxmenu_generate_all_menu_styles( $menu_styles = false ){

	$styles = '';

	if( !$menu_styles ){
		$menu_styles = get_option( MEGAMAXMENU_MENU_STYLES , array() );
	}

	foreach( $menu_styles as $menu_id => $rules ){

		if( empty( $rules ) ) continue;

		$styles.= "/* $menu_id */\n";

		//Normal
		megamaxmenu_process_menu_rules( $rules , false , $styles );

		//Responsive
		if( isset( $rules['_responsive'] ) ){
			$breakpoint = megamaxmenu_op( 'responsive_breakpoint' , 'general' );
			if( !$breakpoint ) $breakpoint = 959;
			if( is_numeric( $breakpoint ) ) $breakpoint.= 'px';

			$styles.= "/* $menu_id - responsive */\n";
			$styles.= "@media screen and (max-width:$breakpoint){\n";
				megamaxmenu_process_menu_rules( $rules , '_responsive' , $styles );
			$styles.= "}\n";
		}

	}

	return $styles;

}

function megamaxmenu_process_menu_rules( $rules , $flag, &$styles ){

	if( $flag ){
		if( isset( $rules[$flag] ) ){
			$rules = $rules[$flag];
		}
		else return;
	}

	$prefix_boost = megamaxmenu_op( 'custom_prefix' , 'general' );

	foreach( $rules as $selector => $property_map ){

		if( $selector == '_responsive' ) continue;

		$styles.= "$prefix_boost $selector { ";
		foreach( $property_map as $property => $value ){

			if( is_array( $value ) ){
				//Multiple instances of this property  (for example, when using browser prefix gradients)
				foreach( $value as $v ){
					$styles.= "$property:$v; ";
				}
			}
			else{
				$styles.= "$property:$value; ";
			}
		}
		$styles.= "}\n";
	}

	//return $styles;
}


/**
 * Call megamaxmenu_save_menu_styles() for each menu instance
 */
add_action( 'megamaxmenu_settings_panel_updated' , 'megamaxmenu_save_all_menu_styles' );
add_action( 'customize_save_after' , 'megamaxmenu_save_all_menu_styles' );
function megamaxmenu_save_all_menu_styles(){

	megamaxmenu_save_menu_styles( 'main' );

	if( function_exists( 'megamaxmenu_get_menu_instances' ) ){
		$menus = megamaxmenu_get_menu_instances();
		foreach( $menus as $menu_id ){
			megamaxmenu_save_menu_styles( $menu_id );
		}
	}

	megamaxmenu_reset_generated_styles();	//clears transient

	add_settings_error( 'menu' , 'menu-styles' , 'Custom menu styles updated.' , 'updated' );

}


/**
 * For each field, checks to see if it has a custom style callback.
 * If callback exists, runs it to generate the style and adds that
 * style to the master array of styles.  Then saves all styles back
 * to the DB in an array format
 * ($menu_id => $selector => $property => $value )
 *
 * @param  string  $menu_id The ID of the menu instance to save
 * @param  boolean $fields  An optional set of fields to use.
 *                          Uses all registered settings by default
 */
function megamaxmenu_save_menu_styles( $menu_id , $fields = false ){

	$menu_key = MEGAMAXMENU_PREFIX . $menu_id;

	if( !$fields ){
		$all_fields = megamaxmenu_get_settings_fields();
		$fields = $all_fields[$menu_key];
	}

	$menu_styles = array();

	/*
	if( !isset( $menu_styles[$menu_id] ) ){
		$menu_styles[$menu_id] = array();
	}
	*/

	foreach( $fields as $field ){

		if( isset( $field['custom_style'] ) ){
			$callback = 'megamaxmenu_get_menu_style_'. $field['custom_style'];

			if( function_exists( $callback ) ){
				$callback( $field , $menu_id , $menu_styles );
			}
		}

	}

	//up( $menu_styles );

	$all_styles = get_option( MEGAMAXMENU_MENU_STYLES , array() );
	$all_styles[$menu_id] = $menu_styles;
//megamaxp( $all_styles , 3 );
	update_option( MEGAMAXMENU_MENU_STYLES , $all_styles );

}


function megamaxmenu_delete_menu_styles( $menu_id ){
	$all_styles = get_option( MEGAMAXMENU_MENU_STYLES , array() );
	unset( $all_styles[$menu_id] );
	update_option( MEGAMAXMENU_MENU_STYLES , $all_styles );
	megamaxmenu_reset_generated_styles();	//clear transient
}




///////////////////////////////////////
//////// HELPERS
///////////////////////////////////////


/*
 * HELPER: BACKGROUND GRADIENT
 */

function megamaxmenu_set_menu_style_background_gradient( $val , $selector , &$menu_styles ){


	$colors = explode( ',' , $val );

	switch( count( $colors ) ){

		/* Flat */
		case 1:
			$menu_styles[$selector]['background'] = $val;
			break;

		/* Gradient */
		case 2:

			$c1 = $colors[0];
			$c2 = $colors[1];

			//Check for leading #hash
			if( $c1[0] != '#' ) $c1 = '#'.$c1;
			if( $c2[0] != '#' ) $c2 = '#'.$c2;

			//$property_map = array();


			$menu_styles[$selector]['background-color'] = $c1;

			//Multiple background values
			$menu_styles[$selector]['background'] = array();

			$menu_styles[$selector]['background'][] = "-webkit-gradient(linear,left top,left bottom,from($c1),to($c2))";
			$menu_styles[$selector]['background'][] = "-webkit-linear-gradient(top,$c1,$c2)";
			$menu_styles[$selector]['background'][] = "-moz-linear-gradient(top,$c1,$c2)";
			$menu_styles[$selector]['background'][] = "-ms-linear-gradient(top,$c1,$c2)";
			$menu_styles[$selector]['background'][] = "-o-linear-gradient(top,$c1,$c2)";
			$menu_styles[$selector]['background'][] = "linear-gradient(top,$c1,$c2)";

	}
}




///////////////////////////////////////
//////// CALLBACKS
///////////////////////////////////////


/*
 * MENU BAR BACKGROUND
 */
function megamaxmenu_get_menu_style_menu_bar_background( $field , $menu_id , &$menu_styles ){
//echo 'mbback';
	$val = megamaxmenu_op( $field['name'] , $menu_id );
	$selector = ".megamaxmenu-$menu_id";
	if( $val ){
		megamaxmenu_set_menu_style_background_gradient( $val , $selector , $menu_styles );
		//$menu_styles[$selector]['background'] = $background;
	}
	else{
		//echo 'unset';
		//unset( $menu_styles[$selector]['background'] );
	}

}

/*
 * MENU BAR TRANSPARENT
 */
function megamaxmenu_get_menu_style_menu_bar_transparent( $field , $menu_id , &$menu_styles ){
//echo 'mbback';
	$val = megamaxmenu_op( $field['name'] , $menu_id );
	$selector = ".megamaxmenu.megamaxmenu-$menu_id";
	if( $val == 'on' ){
		$menu_styles[$selector]['background'] = 'none';
		$menu_styles[$selector]['border'] = 'none';
		$menu_styles[$selector]['box-shadow'] = 'none';

		$top_items_selector = "$selector .megamaxmenu-item-level-0 > .megamaxmenu-target";
		$menu_styles[$top_items_selector]['border'] = 'none';
		$menu_styles[$top_items_selector]['box-shadow'] = 'none';

		//No border, align subs to the left and top
		if( megamaxmenu_op( 'style_menu_bar_border' , $menu_id ) == '' ){
			$full_sub_selector = "$selector.megamaxmenu-horizontal .megamaxmenu-submenu-drop.megamaxmenu-submenu-align-left_edge_bar, $selector.megamaxmenu-horizontal .megamaxmenu-submenu-drop.megamaxmenu-submenu-align-full_width";
			$menu_styles[$full_sub_selector]['left'] = 0;

			$active_sub_selector = "$selector.megamaxmenu-horizontal .megamaxmenu-item-level-0.megamaxmenu-active > .megamaxmenu-submenu-drop, $selector.megamaxmenu-horizontal:not(.megamaxmenu-transition-shift) .megamaxmenu-item-level-0 > .megamaxmenu-submenu-drop";
			$menu_styles[$active_sub_selector]['margin-top'] = 0;
		}



	}
}

/*
 * MENU BAR BORDER
 */
function megamaxmenu_get_menu_style_menu_bar_border( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id";
		$color = $val[0] == '#' ? $val : '#'.$val;
		$menu_styles[$selector]['border'] = "1px solid $color";
	}
	else{
		if( megamaxmenu_op( 'skin' , $menu_id ) == 'none' ){
			$menu_styles[".megamaxmenu-$menu_id.megamaxmenu-transition-fade .megamaxmenu-item .megamaxmenu-submenu-drop"]['margin-top'] = 0;
		}
	}

}

/*
 * MENU BAR RADIUS
 */
function megamaxmenu_get_menu_style_menu_bar_radius( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id, .megamaxmenu-$menu_id > .megamaxmenu-nav";

		$radius = $val;
		if( is_numeric( $radius ) ) $radius.= 'px';

		$menu_styles[$selector]['-webkit-border-radius'] = $radius;
		$menu_styles[$selector]['-moz-border-radius'] = $radius;
		$menu_styles[$selector]['-o-border-radius'] = $radius;
		$menu_styles[$selector]['border-radius'] = $radius;

		//If items are aligned left, give the first item a radius as well
		if( is_numeric( $val ) && megamaxmenu_op( 'items_align' , $menu_id ) == 'left' ){
			$selector = ".megamaxmenu-$menu_id > .megamaxmenu-nav > .megamaxmenu-item:first-child > .megamaxmenu-target";
			$radius = "{$val}px 0 0 {$val}px";

			$menu_styles[$selector]['-webkit-border-radius'] = $radius;
			$menu_styles[$selector]['-moz-border-radius'] = $radius;
			$menu_styles[$selector]['-o-border-radius'] = $radius;
			$menu_styles[$selector]['border-radius'] = $radius;
		}
	}

}



/*
 * TOP LEVEL FONT SIZE
 */
function megamaxmenu_get_menu_style_top_level_font_size( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";

		$size = is_numeric( $val ) ? $val.'px' : $val;

		$menu_styles[$selector]['font-size'] = $size;
	}
}


/*
 * TOP LEVEL LINE HEIGHT
 */
function megamaxmenu_get_menu_style_top_level_line_height( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target, ".
					".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target.megamaxmenu-item-notext > .megamaxmenu-icon";

		$height = is_numeric( $val ) ? $val.'px' : $val;

		$menu_styles[$selector]['line-height'] = $height;
	}
}



/*
 * TOP LEVEL TEXT TRANSFORM
 */
function megamaxmenu_get_menu_style_top_level_text_transform( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";
		$menu_styles[$selector]['text-transform'] = $val;
	}
}

/*
 * TOP LEVEL FONT WEIGHT
 */
function megamaxmenu_get_menu_style_top_level_font_weight( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-nav .megamaxmenu-item.megamaxmenu-item-level-0 > .megamaxmenu-target";
		$menu_styles[$selector]['font-weight'] = $val;
	}
}


/*
 * TOP LEVEL FONT COLOR
 */
function megamaxmenu_get_menu_style_top_level_font_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";

		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * TOP LEVEL FONT COLOR - HOVER
 */
function megamaxmenu_get_menu_style_top_level_font_color_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-item-level-0:hover > .megamaxmenu-target, .megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-active > .megamaxmenu-target";	//removed notouch

		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * TOP LEVEL FONT COLOR - CURRENT
 */
function megamaxmenu_get_menu_style_top_level_font_color_current( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-current-menu-item > .megamaxmenu-target, ".
					".megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-current-menu-parent > .megamaxmenu-target, ".
					".megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-current-menu-ancestor > .megamaxmenu-target";

		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * TOP LEVEL FONT COLOR - HIGHLIGHT
 */
function megamaxmenu_get_menu_style_top_level_font_color_highlight( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item.megamaxmenu-item-level-0 > .megamaxmenu-highlight";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * TOP LEVEL ITEM MARGIN - INDIVIDUAL ITEMS
 */
function megamaxmenu_get_menu_style_top_level_margin( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );

	if( is_numeric( $val ) ) $val = $val.='px';

	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-item-level-0";

		$menu_styles[$selector]['margin'] = $val;
	}
}

/*
 * TOP LEVEL ITEM BORDER RADIUS - INDIVIDUAL ITEMS
 */
function megamaxmenu_get_menu_style_top_level_border_radius( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );

	if( is_numeric( $val ) ) $val = $val.='px';

	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";

		$menu_styles[$selector]['border-radius'] = $val;
	}
}

/*
 * TOP LEVEL BACKGROUND - INDIVIDUAL ITEMS
 */
function megamaxmenu_get_menu_style_top_level_background( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";

		megamaxmenu_set_menu_style_background_gradient( $val , $selector , $menu_styles );
	}
}

/*
 * TOP LEVEL BACKGROUND - HOVER
 */
function megamaxmenu_get_menu_style_top_level_background_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-item-level-0:hover > .megamaxmenu-target, .megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-active > .megamaxmenu-target";	//removed notouch

		//$menu_styles[$selector]['background'] = $val;
		megamaxmenu_set_menu_style_background_gradient( $val , $selector , $menu_styles );
	}
}

/*
 * TOP LEVEL BACKGROUND - CURRENT
 */
function megamaxmenu_get_menu_style_top_level_background_current( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-current-menu-item > .megamaxmenu-target, ".
					".megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-current-menu-parent > .megamaxmenu-target, ".
					".megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-current-menu-ancestor > .megamaxmenu-target";

		//$menu_styles[$selector]['background'] = $val;
		megamaxmenu_set_menu_style_background_gradient( $val , $selector , $menu_styles );
	}
}

/*
 * TOP LEVEL BACKGROUND - HIGHLIGHT
 */
function megamaxmenu_get_menu_style_top_level_background_highlight( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item.megamaxmenu-item-level-0 > .megamaxmenu-highlight";

		//$menu_styles[$selector]['background'] = $val;
		megamaxmenu_set_menu_style_background_gradient( $val , $selector , $menu_styles );
	}
}

function megamaxmenu_force_styles( $menu_id ){
	if( megamaxmenu_op( 'force_styles' , $menu_id ) == 'on' ){
		return true;
	}
	return false;
}

/*
 * TOP LEVEL ITEM DIVIDER COLOR
 */
function megamaxmenu_get_menu_style_top_level_item_divider_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){

		if( megamaxmenu_op( 'orientation' , $menu_id ) == 'vertical' ){
			$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";
			if( megamaxmenu_force_styles( $menu_id ) ){
				$menu_styles[$selector]['border-top'] = "1px solid $val";
			}
			else{
				$menu_styles[$selector]['border-top-color'] = $val;
			}
		}
		else{
			$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";
			if( megamaxmenu_force_styles( $menu_id ) ){
				$menu_styles[$selector]['border-left'] = "1px solid $val";
			}
			else{
				$menu_styles[$selector]['border-left-color'] = $val;
			}
		}
	}
}

function megamaxmenu_get_menu_style_top_level_item_divider_disable( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val == 'on' ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";
		$menu_styles[$selector]['border'] = "none";
		/*
		if( megamaxmenu_op( 'orientation' , $menu_id ) == 'vertical' ){
			$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";
			if( megamaxmenu_force_styles( $menu_id ) ){
				$menu_styles[$selector]['border-top'] = "1px solid $val";
			}
			else{
				$menu_styles[$selector]['border-top-color'] = $val;
			}
		}
		else{
			$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";
			if( megamaxmenu_force_styles( $menu_id ) ){
				$menu_styles[$selector]['border-left'] = "1px solid $val";
			}
			else{
				$menu_styles[$selector]['border-left-color'] = $val;
			}
		}
		*/
	}
}

/*
 * TOP LEVEL ITEM GLOW OPACITY
 */
function megamaxmenu_get_menu_style_top_level_item_glow_opacity( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( is_numeric( $val ) ){
		switch( megamaxmenu_op( 'orientation' , $menu_id ) ){
			case 'horizontal':
				$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target"; //added .megamaxmenu here, otherwise specificity to low to override Vertical orientation styles
				$menu_styles[$selector]['-webkit-box-shadow'] = 'inset 1px 0 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['-moz-box-shadow'] = 'inset 1px 0 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['-o-box-shadow'] = 'inset 1px 0 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['box-shadow'] = 'inset 1px 0 0 0 rgba(255,255,255,'. $val.')';
				break;
			case 'vertical';
				$selector = ".megamaxmenu-$menu_id.megamaxmenu-vertical .megamaxmenu-item-level-0 > .megamaxmenu-target"; //added .megamaxmenu here, otherwise specificity to low to override Vertical orientation styles
				$menu_styles[$selector]['-webkit-box-shadow'] = 'inset 1px 1px 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['-moz-box-shadow'] = 'inset 1px 1px 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['-o-box-shadow'] = 'inset 1px 1px 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['box-shadow'] = 'inset 1px 1px 0 0 rgba(255,255,255,'. $val.')';
				break;
		}
	}
}

/*
 * TOP LEVEL ITEM GLOW OPACITY - Hover
 */
function megamaxmenu_get_menu_style_top_level_item_glow_opacity_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( is_numeric( $val ) ){
		switch( megamaxmenu_op( 'orientation' , $menu_id ) ){
			case 'horizontal':
				$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-active > .megamaxmenu-target,.megamaxmenu-$menu_id .megamaxmenu-item-level-0:hover > .megamaxmenu-target";
				$menu_styles[$selector]['-webkit-box-shadow'] = 'inset 1px 0 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['-moz-box-shadow'] = 'inset 1px 0 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['-o-box-shadow'] = 'inset 1px 0 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['box-shadow'] = 'inset 1px 0 0 0 rgba(255,255,255,'. $val.')';
				break;
			case 'vertical';
				$selector = ".megamaxmenu-$menu_id.megamaxmenu-vertical .megamaxmenu-item-level-0.megamaxmenu-active > .megamaxmenu-target,.megamaxmenu-$menu_id.megamaxmenu-vertical .megamaxmenu-item-level-0:hover > .megamaxmenu-target";
				$menu_styles[$selector]['-webkit-box-shadow'] = 'inset 1px 1px 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['-moz-box-shadow'] = 'inset 1px 1px 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['-o-box-shadow'] = 'inset 1px 1px 0 0 rgba(255,255,255,'. $val.')';
				$menu_styles[$selector]['box-shadow'] = 'inset 1px 1px 0 0 rgba(255,255,255,'. $val.')';
				break;
		}
	}
}


/*
 * TOP LEVEL VERTICAL PADDING
 */
function megamaxmenu_get_menu_style_top_level_padding( $field , $menu_id , &$menu_styles ){

	$padding = megamaxmenu_op( $field['name'] , $menu_id );

	if( $padding ){

		if( is_numeric($padding) ) $padding.= 'px';

		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target, ".".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-custom-content.megamaxmenu-custom-content-padded";

		$menu_styles[$selector]['padding-top'] = $padding;
		$menu_styles[$selector]['padding-bottom'] = $padding;
	}
}

/*
 * TOP LEVEL VERTICAL PADDING - [RESPONSIVE]
 */
function megamaxmenu_get_menu_style_top_level_padding_responsive( $field , $menu_id , &$menu_styles ){

	$padding = megamaxmenu_op( $field['name'] , $menu_id );

	if( $padding ){

		if( is_numeric($padding) ) $padding.= 'px';

		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";

		$menu_styles['_responsive'][$selector]['padding-top'] = $padding;
		$menu_styles['_responsive'][$selector]['padding-bottom'] = $padding;
	}
}

/*
 * TOP LEVEL HORIZONTAL PADDING
 */
function megamaxmenu_get_menu_style_top_level_horiz_padding( $field , $menu_id , &$menu_styles ){

	$padding = megamaxmenu_op( $field['name'] , $menu_id );
	$indicator_width = 15;

	if( $padding ){

		$padding_raw = $padding;

		if( is_numeric($padding) ){
			$padding.= 'px';
		}
		else{
			//$padding_sub_indicator = trim( $padding_sub_indicator , 'px' );
			$padding_raw = trim( $padding_raw , 'px' );
		}

		$padding_sub_indicator = $padding_raw;
		$selector_offset = $padding_raw;

		if( is_numeric( $padding_sub_indicator ) ){
			$padding_sub_indicator+= $indicator_width;
			$padding_sub_indicator.= 'px';
		}
		else{
			$padding_sub_indicator = false;
		}


		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";

		$menu_styles[$selector]['padding-left'] = $padding;
		$menu_styles[$selector]['padding-right'] = $padding;

		//If a numeric or px value was set, add extra padding for the indicator
		if( $padding_sub_indicator && ( megamaxmenu_op( 'style_extra_submenu_indicator_padding' , $menu_id ) != 'off' ) ){
			$with_sub_selector = ".megamaxmenu-{$menu_id}.megamaxmenu-sub-indicators .megamaxmenu-item-level-0.megamaxmenu-has-submenu-drop > .megamaxmenu-target:not(.megamaxmenu-noindicator)";
			$menu_styles[$with_sub_selector]['padding-right'] = $padding_sub_indicator;

			//However, don't override the padding on items with disabled indicators
			$disabled_sub_selector = ".megamaxmenu-{$menu_id}.megamaxmenu-sub-indicators .megamaxmenu-item-level-0.megamaxmenu-has-submenu-drop > .megamaxmenu-target.megamaxmenu-noindicator";
			$menu_styles[$disabled_sub_selector]['padding-right'] = $padding;
		}
		else{
			$selector_offset-= $indicator_width;
			if( $selector_offset < 0 ) $selector_offset = 0;
		}

		// if( is_numeric( $selector_offset ) && megamaxmenu_op( 'style_align_submenu_indicator' , $menu_id ) == 'text' ){
		// 	$indicator_selector = ".megamaxmenu-{$menu_id}.megamaxmenu-sub-indicators .megamaxmenu-item-level-0.megamaxmenu-has-submenu-drop > .megamaxmenu-target:after";
		// 	$selector_offset.='px';
		// 	$menu_styles[$indicator_selector]['right'] = $selector_offset;
		// }

	}
}


/*
 * TOP LEVEL ITEM HEIGHT
 */
function megamaxmenu_get_menu_style_top_level_item_height( $field , $menu_id , &$menu_styles ){

	$height = megamaxmenu_op( $field['name'] , $menu_id );



	if( $height ){

		if( is_numeric($height) ) $height.= 'px';

		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-target";

		$menu_styles[$selector]['height'] = $height;
	}
}


/*
 * SUBMENU BACKGROUND COLOR
 */
function megamaxmenu_get_menu_style_submenu_background_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu.megamaxmenu-submenu-drop";
		$menu_styles[$selector]['background-color'] = $val;
	}
}

/*
 * SUBMENU BORDER COLOR
 */
function megamaxmenu_get_menu_style_submenu_border_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu.megamaxmenu-submenu-drop";
		if( megamaxmenu_force_styles( $menu_id ) ){
			$menu_styles[$selector]['border'] = "1px solid $val";
		}
		else{
			$menu_styles[$selector]['border-color'] = $val;
		}
	}
}

/*
 * SUBMENU DROPSHADOW OPACITY
 */
function megamaxmenu_get_menu_style_submenu_dropshadow_opacity( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( is_numeric( $val ) ){

		if( $val > 1 ) $val = $val / 100;

		$propval = "0 0 20px rgba(0,0,0, $val)";
		if( $val == 0 ) $propval = 'none';

		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0 > .megamaxmenu-submenu-drop";
		$menu_styles[$selector]['box-shadow'] = $propval;
	}
}



/*
 * SUBMENU FALLBACK FONT COLOR
 */
function megamaxmenu_get_menu_style_submenu_fallback_font_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu.megamaxmenu-submenu-drop";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * SUBMENU MINIMUM COLUMN WIDTH
 */
function megamaxmenu_get_menu_style_submenu_minimum_column_width( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-column";
		$menu_styles[$selector]['min-width'] = $val;
	}
}

/*
 * SUBMENU HIGHLIGHT FONT COLOR
 */
function megamaxmenu_get_menu_style_submenu_highlight_font_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-highlight";
		$menu_styles[$selector]['color'] = $val;
	}
}



/*
 * HEADER FONT SIZE
 */
function megamaxmenu_get_menu_style_header_font_size( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-item-header > .megamaxmenu-target, .megamaxmenu-$menu_id .megamaxmenu-tab > .megamaxmenu-target";
		$menu_styles[$selector]['font-size'] = $val;
	}
}


/*
 * HEADER TEXT TRANSFORM
 */
function megamaxmenu_get_menu_style_header_text_transform( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-item-header > .megamaxmenu-target, .megamaxmenu-$menu_id .megamaxmenu-tab > .megamaxmenu-target";
		$menu_styles[$selector]['text-transform'] = $val;
	}
}


/*
 * HEADER FONT COLOR
 */
function megamaxmenu_get_menu_style_header_font_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-item-header > .megamaxmenu-target";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * HEADER FONT COLOR - HOVER
 */
function megamaxmenu_get_menu_style_header_font_color_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-item-header > .megamaxmenu-target:hover";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * HEADER FONT COLOR - CURRENT
 */
function megamaxmenu_get_menu_style_header_font_color_current( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-item-header.megamaxmenu-current-menu-item > .megamaxmenu-target";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * HEADER BACKGROUND COLOR
 */
function megamaxmenu_get_menu_style_header_background_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-item-header > .megamaxmenu-target";
		$menu_styles[$selector]['background-color'] = $val;
	}
}

/*
 * HEADER BACKGROUND COLOR - HOVER
 */
function megamaxmenu_get_menu_style_header_background_color_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-item-header > .megamaxmenu-target:hover";
		$menu_styles[$selector]['background-color'] = $val;
	}
}

/*
 * HEADER BACKGROUND COLOR - CURRENT
 */
function megamaxmenu_get_menu_style_header_background_color_current( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-item-header.megamaxmenu-current-menu-item > .megamaxmenu-target";
		$menu_styles[$selector]['background-color'] = $val;
	}
}



/*
 * HEADER FONT WEIGHT
 */
function megamaxmenu_get_menu_style_header_font_weight( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-nav .megamaxmenu-submenu .megamaxmenu-item-header > .megamaxmenu-target";
		$menu_styles[$selector]['font-weight'] = $val;
	}
}

/*
 * HEADER BORDER COLOR
 */
function megamaxmenu_get_menu_style_header_border_color( $field , $menu_id , &$menu_styles ){

	$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-item-header.megamaxmenu-has-submenu-stack > .megamaxmenu-target";

	if( megamaxmenu_op( 'display_header_border_color' , $menu_id ) == 'off' ){
		$menu_styles[$selector]['border'] = 'none';
		$menu_styles[".megamaxmenu-$menu_id .megamaxmenu-submenu-type-stack"]['padding-top'] = 0;
	}
	else{
		$val = megamaxmenu_op( $field['name'] , $menu_id );
		if( $val ){
			if( megamaxmenu_force_styles( $menu_id ) ){
				$menu_styles[$selector]['border-bottom'] = "1px solid $val";
			}
			else{
				$menu_styles[$selector]['border-color'] = $val;
			}
		}
	}

}



/*
 * SUBMENU ITEM PADDING
 */
function megamaxmenu_get_menu_style_submenu_item_padding( $field , $menu_id , &$menu_styles ){
	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val || is_numeric( $val ) ){	//include '0'
		if( is_numeric( $val ) ){
			$val.= 'px';
		}
		$selector =
			".megamaxmenu-$menu_id .megamaxmenu-item-normal > .megamaxmenu-target,".
			".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-target,".
			".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-nonlink,".
			".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-widget,".
			".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-custom-content-padded,".
			".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-retractor,".
			".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-colgroup .megamaxmenu-column,".
			".megamaxmenu-$menu_id .megamaxmenu-submenu.megamaxmenu-submenu-type-stack > .megamaxmenu-item-normal > .megamaxmenu-target,".
			".megamaxmenu-$menu_id .megamaxmenu-submenu.megamaxmenu-submenu-padded";
		$menu_styles[$selector]['padding'] = $val;

		//Grid Row
		$menu_styles[".megamaxmenu-$menu_id .megamaxmenu-grid-row"]['padding-right'] = $val;
		$menu_styles[".megamaxmenu-$menu_id .megamaxmenu-grid-row .megamaxmenu-target"]['padding-right'] = 0;

		//With submenu indicators, add padding if it's set low
		if( intval( $val ) < 15 ){
			$menu_styles[".megamaxmenu-$menu_id.megamaxmenu-sub-indicators .megamaxmenu-submenu :not(.megamaxmenu-tabs-layout-right) .megamaxmenu-has-submenu-drop > .megamaxmenu-target"]['padding-right'] = '25px';
		}

	}
}


/*
 * NORMAL FONT COLOR
 */
function megamaxmenu_get_menu_style_normal_font_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-normal > .megamaxmenu-target";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * NORMAL FONT COLOR - HOVER
 */
function megamaxmenu_get_menu_style_normal_font_color_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-item-normal > .megamaxmenu-target:hover, .megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-item-normal.megamaxmenu-active > .megamaxmenu-target"; //removed notouch
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * NORMAL FONT COLOR - CURRENT
 */
function megamaxmenu_get_menu_style_normal_font_color_current( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-normal.megamaxmenu-current-menu-item > .megamaxmenu-target";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * NORMAL FONT SIZE
 */
function megamaxmenu_get_menu_style_normal_font_size( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-normal > .megamaxmenu-target";
		$menu_styles[$selector]['font-size'] = $val;
	}
}

/*
 * NORMAL FONT WEIGHT
 */
function megamaxmenu_get_menu_style_normal_font_weight( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-normal > .megamaxmenu-target";
		$menu_styles[$selector]['font-weight'] = $val;
	}
}

/*
 * NORMAL TEXT TRANSFORM
 */
function megamaxmenu_get_menu_style_normal_text_transform( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-normal > .megamaxmenu-target";
		$menu_styles[$selector]['text-transform'] = $val;
	}
}


/*
 * NORMAL TEXT UNDERLINE HOVER
 */
function megamaxmenu_get_menu_style_normal_underline_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val == 'on' ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-normal > .megamaxmenu-target:hover > .megamaxmenu-target-text";
		$menu_styles[$selector]['text-decoration'] = 'underline';
	}
}

/*
 * NORMAL BACKGROUND COLOR - HOVER
 */
function megamaxmenu_get_menu_style_normal_background_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-item-normal > .megamaxmenu-target:hover, .megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-item-normal.megamaxmenu-active > .megamaxmenu-target"; //removed notouch
		$menu_styles[$selector]['background-color'] = $val;
	}
}

/*
 * FLYOUT VERTICAL PADDING
 */
function megamaxmenu_get_menu_style_flyout_vertical_padding( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu-type-flyout > .megamaxmenu-item-normal > .megamaxmenu-target";
		$menu_styles[$selector]['padding-top'] = $val;
		$menu_styles[$selector]['padding-bottom'] = $val;
	}
}


/*
 * FLYOUT DIVIDERS
 */
function megamaxmenu_get_menu_style_flyout_divider( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu-type-flyout > .megamaxmenu-item-normal > .megamaxmenu-target";
		$menu_styles[$selector]['border-bottom'] = "1px solid $val";
	}
}








/*
 * TABS FONT SIZE
 */
function megamaxmenu_get_menu_style_tabs_font_size( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val = $val.='px';
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tabs .megamaxmenu-tabs-group > .megamaxmenu-tab > .megamaxmenu-target";
		$menu_styles[$selector]['font-size'] = $val;
	}
}

/*
 * TABS FONT WEIGHT
 */
function megamaxmenu_get_menu_style_tabs_font_weight( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tabs .megamaxmenu-tabs-group > .megamaxmenu-tab > .megamaxmenu-target";
		$menu_styles[$selector]['font-weight'] = $val;
	}
}
/*
 * TABS GROUP BACKGROUND COLOR
 */
function megamaxmenu_get_menu_style_tabs_background( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tabs .megamaxmenu-tabs-group";
		$menu_styles[$selector]['background-color'] = $val;
	}
}

/*
 * TAB TOGGLES FONT COLOR
 */
function megamaxmenu_get_menu_style_tabs_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tab > .megamaxmenu-target";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * TAB TOGGLES FONT COLOR - HOVER
 */
function megamaxmenu_get_menu_style_tabs_color_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-tab.megamaxmenu-active > .megamaxmenu-target";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * TAB TOGGLES FONT COLOR - CURRENT
 */
function megamaxmenu_get_menu_style_tabs_color_current( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){

		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-tab.megamaxmenu-current-menu-item > .megamaxmenu-target, ".
					".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-tab.megamaxmenu-current-menu-parent > .megamaxmenu-target, ".
					".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-tab.megamaxmenu-current-menu-ancestor > .megamaxmenu-target";

		$menu_styles[$selector]['color'] = $val;
	}
}


/*
 * TAB TOGGLES BACKGROUND COLOR - HOVER
 */
function megamaxmenu_get_menu_style_tabs_background_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tab.megamaxmenu-active > .megamaxmenu-target";
		$menu_styles[$selector]['background-color'] = $val;
	}
}

/*
 * TAB TOGGLES BACKGROUND COLOR - CURRENT
 */
function megamaxmenu_get_menu_style_tabs_background_current( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){

		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-tab.megamaxmenu-current-menu-item > .megamaxmenu-target, ".
					".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-tab.megamaxmenu-current-menu-parent > .megamaxmenu-target, ".
					".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-tab.megamaxmenu-current-menu-ancestor > .megamaxmenu-target";

		$menu_styles[$selector]['background-color'] = $val;
	}
}



/*
 * TABS CONTENT PANEL BACKGROUND COLOR
 */
function megamaxmenu_get_menu_style_tab_content_background( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tab-content-panel";
		$menu_styles[$selector]['background-color'] = $val;
	}
}

/*
 * TABS CONTENT PANEL HEADER FONT COLOR
 */
function megamaxmenu_get_menu_style_tab_content_force_header_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tabs-group .megamaxmenu-item-header > .megamaxmenu-target";
		$menu_styles[$selector]['color'] = $val .' !important';
	}
}

/*
 * TABS CONTENT PANEL NORMAL FONT COLOR
 */
function megamaxmenu_get_menu_style_tab_content_force_normal_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tabs-group .megamaxmenu-item-normal > .megamaxmenu-target";
		$menu_styles[$selector]['color'] = $val .' !important';
	}
}

/*
 * TABS CONTENT PANEL DESCRIPTION FONT COLOR
 */
function megamaxmenu_get_menu_style_tab_content_force_description_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tabs-group .megamaxmenu-target > .megamaxmenu-target-description";
		$menu_styles[$selector]['color'] = $val .' !important';
	}
}


/*
 * TABS / CONTENT PANEL DIVIDER COLOR
 */
function megamaxmenu_get_menu_style_tab_divider_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-tabs-group";
		$menu_styles[$selector]['border-color'] = $val;
	}
}











/*
 * DESCRIPTION FONT SIZE
 */
function megamaxmenu_get_menu_style_description_font_size( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-target > .megamaxmenu-target-description";
		$menu_styles[$selector]['font-size'] = $val;
	}
}

/*
 * DESCRIPTION FONT COLOR
 */
function megamaxmenu_get_menu_style_description_font_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-target > .megamaxmenu-target-description, .megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-target > .megamaxmenu-target-description";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * DESCRIPTION FONT COLOR - ACTIVE/HOVER
 */
function megamaxmenu_get_menu_style_description_font_color_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-target:hover > .megamaxmenu-target-description, .megamaxmenu-$menu_id .megamaxmenu-active > .megamaxmenu-target > .megamaxmenu-target-description, .megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-target:hover > .megamaxmenu-target-description, .megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-active > .megamaxmenu-target > .megamaxmenu-target-description";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * DESCRIPTION TEXT TRANSFORM
 */
function megamaxmenu_get_menu_style_description_text_transform( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-target > .megamaxmenu-target-description";
		$menu_styles[$selector]['text-transform'] = $val;
	}
}

/*
 * TOP LEVEL ARROW COLOR
 */
function megamaxmenu_get_menu_style_top_level_arrow_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-level-0.megamaxmenu-has-submenu-drop > .megamaxmenu-target > .megamaxmenu-sub-indicator";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * SUBMENU ARROW COLOR
 */
function megamaxmenu_get_menu_style_submenu_arrow_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-has-submenu-drop > .megamaxmenu-target > .megamaxmenu-sub-indicator";
		$menu_styles[$selector]['color'] = $val;
	}
}


/*
 * HR
 */
function megamaxmenu_get_menu_style_hr( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-submenu .megamaxmenu-divider > hr";
		$menu_styles[$selector]['border-top-color'] = $val;
	}
}


/*
 * RESPONSIVE MENU MAX HEIGHT
 */
function megamaxmenu_get_menu_style_responsive_max_height( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu.megamaxmenu-$menu_id:not(.megamaxmenu-responsive-collapse)";
		$menu_styles[$selector]['max-height'] = $val;

		//$breakpoint = megamaxmenu_op( 'responsive_breakpoint' , 'general' );
		//$menu_styles[$selector]['media_query'] = "@media screen and (max-width:$breakpoint)";
	}
}



/*
 * SEARCH BACKGROUND
 */
function megamaxmenu_get_menu_style_search_background( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-search input.megamaxmenu-search-input";
		$menu_styles[$selector]['background'] = $val;
	}
}

/*
 * SEARCH TEXT COLOR
 */
function megamaxmenu_get_menu_style_search_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-search input.megamaxmenu-search-input";
		$menu_styles[$selector]['color'] = $val;
	}
}


/*
 * SEACH FONT SIZE
 */
function megamaxmenu_get_menu_style_search_font_size( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.= 'px';
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-search input.megamaxmenu-search-input";
		$menu_styles[$selector.", .megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-search button[type='submit']"]['font-size'] = $val;
		$menu_styles[$selector.'::-webkit-input-placeholder']['font-size'] = $val;
		$menu_styles[$selector.'::-moz-placeholder']['font-size'] = $val;
		$menu_styles[$selector.'::-ms-input-placeholder']['font-size'] = $val;
	}
}


/*
 * SEARCH PLACEHOLDER COLOR
 */
function megamaxmenu_get_menu_style_search_placeholder_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-search input.megamaxmenu-search-input";
		$menu_styles[$selector.'::-webkit-input-placeholder']['color'] = $val;
		$menu_styles[$selector.'::-moz-placeholder']['color'] = $val;
		$menu_styles[$selector.'::-ms-input-placeholder']['color'] = $val;
	}
}

/*
 * SEARCH ICON COLOR
 */
function megamaxmenu_get_menu_style_search_icon_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu.megamaxmenu-$menu_id .megamaxmenu-search .megamaxmenu-search-submit";
		$menu_styles[$selector]['color'] = $val;
	}
}




/*
 * TOGGLE BACKGROUND
 */
function megamaxmenu_get_menu_style_toggle_background( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-responsive-toggle.megamaxmenu-responsive-toggle-$menu_id";
		$menu_styles[$selector]['background'] = $val;
	}
}
/*
 * TOGGLE COLOR
 */
function megamaxmenu_get_menu_style_toggle_color( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-responsive-toggle.megamaxmenu-responsive-toggle-$menu_id";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * TOGGLE BACKGROUND - HOVER
 */
function megamaxmenu_get_menu_style_toggle_background_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-responsive-toggle.megamaxmenu-responsive-toggle-$menu_id:hover";
		$menu_styles[$selector]['background'] = $val;
	}
}
/*
 * TOGGLE COLOR - HOVER
 */
function megamaxmenu_get_menu_style_toggle_color_hover( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-responsive-toggle.megamaxmenu-responsive-toggle-$menu_id:hover";
		$menu_styles[$selector]['color'] = $val;
	}
}

/*
 * TOGGLE FONT SIZE
 */
function megamaxmenu_get_menu_style_toggle_font_size( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu-responsive-toggle.megamaxmenu-responsive-toggle-$menu_id";
		$menu_styles[$selector]['font-size'] = $val;
	}
}

/*
 * TOGGLE FONT WEIGHT
 */
function megamaxmenu_get_menu_style_toggle_font_weight( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		//if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu-responsive-toggle.megamaxmenu-responsive-toggle-$menu_id";
		$menu_styles[$selector]['font-weight'] = $val;
	}
}

/*
 * TOGGLE PADDING
 */
function megamaxmenu_get_menu_style_toggle_padding( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		if( is_numeric( $val ) ) $val.='px';
		$selector = ".megamaxmenu-responsive-toggle.megamaxmenu-responsive-toggle-$menu_id";
		$menu_styles[$selector]['padding'] = $val;
	}
}




/*
 * ROW SPACING
 */

function megamaxmenu_get_menu_style_row_spacing( $field , $menu_id , &$menu_styles ){

	$row_spacing = megamaxmenu_op( $field['name'] , $menu_id );
	if( $row_spacing !== '' ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-row";

		//Assume pixels if no units provided
		if( is_numeric( $row_spacing ) ){
			$row_spacing.='px';
		}

		$menu_styles[$selector]['margin-bottom'] = $row_spacing;
	}

}

/*
 * ICON WIDTH
 */
function megamaxmenu_get_menu_style_icon_width( $field , $menu_id , &$menu_styles ){

	$icon_width = megamaxmenu_op( $field['name'] , $menu_id );
	if( $icon_width !== '' ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-icon";

		//Assume pixels if no units provided
		if( is_numeric( $icon_width ) ){
			$icon_width.='px';
		}

		$menu_styles[$selector]['width'] = $icon_width;
	}

}

/*
 * ICON NUDGE
 */
function megamaxmenu_get_menu_style_icon_nudge( $field , $menu_id , &$menu_styles ){

	$icon_nudge = megamaxmenu_op( $field['name'] , $menu_id );
	if( $icon_nudge !== '' ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-icon";

		//Assume pixels if no units provided
		if( is_numeric( $icon_nudge ) ){
			$icon_nudge.='px';
		}

		$menu_styles[$selector]['transform'] = "translateY($icon_nudge)";
	}

}

/*
 * MENU BAR WIDTH
 */

function megamaxmenu_get_menu_style_bar_width( $field , $menu_id , &$menu_styles ){

	$bar_width = megamaxmenu_op( $field['name'] , $menu_id );
	if( $bar_width ){
		$selector = ".megamaxmenu-$menu_id";

		//Assume pixels if no units provided
		if( is_numeric( $bar_width ) ){
			$bar_width.='px';
		}

		$menu_styles[$selector]['max-width'] = $bar_width;
	}

}

/*
 * MENU BAR MARGIN TOP
 */

function megamaxmenu_get_menu_style_bar_margin_top( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id";

		//Assume pixels if no units provided
		if( is_numeric( $val ) ){
			$val.='px';
		}

		$menu_styles[$selector]['margin-top'] = $val;
	}

}

/*
 * MENU BAR MARGIN BOTTOM
 */

function megamaxmenu_get_menu_style_bar_margin_bottom( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id";

		//Assume pixels if no units provided
		if( is_numeric( $val ) ){
			$val.='px';
		}

		$menu_styles[$selector]['margin-bottom'] = $val;
	}

}

/*
 * INNER MENU BAR WIDTH
 */

function megamaxmenu_get_menu_style_bar_inner_width( $field , $menu_id , &$menu_styles ){

	$bar_width = megamaxmenu_op( $field['name'] , $menu_id );
	if( $bar_width ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-nav";

		//Assume pixels if no units provided
		if( is_numeric( $bar_width ) ){
			$bar_width.='px';
		}

		$menu_styles[$selector]['max-width'] = $bar_width;
	}

}


/*
 * BOUND SUBMENUS CUSTOM
 */

 // function megamaxmenu_get_menu_style_bound_submenu_custom( $field , $menu_id , &$menu_styles ){
 //
 // 	$submenu_bounds = megamaxmenu_op( $field['bound_submenu_custom'] , $menu_id );
 // 	if( $submenu_bounds ){
 // 		$selector = $submenu_bounds;
 //
 // 		$menu_styles[$selector]['position'] = 'relative';
 // 	}
 //
 // }

/*
 * SUBMENU INNER WIDTH
 */

function megamaxmenu_get_menu_style_submenu_inner_width( $field , $menu_id , &$menu_styles ){

	$submenu_width = megamaxmenu_op( $field['name'] , $menu_id );
	if( $submenu_width ){
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-row";

		//Assume pixels if no units provided
		if( is_numeric( $submenu_width ) ){
			$submenu_width.='px';
		}

		$menu_styles[$selector]['max-width'] = $submenu_width;
		$menu_styles[$selector]['margin-left'] = 'auto';
		$menu_styles[$selector]['margin-right'] = 'auto';
	}

}


/*
 * SUBMENU MAX HEIGHT
 */

function megamaxmenu_get_menu_style_submenu_max_height( $field , $menu_id , &$menu_styles ){

	$val = megamaxmenu_op( $field['name'] , $menu_id );
	if( $val ){
		$selector = ".megamaxmenu-$menu_id.megamaxmenu-transition-slide .megamaxmenu-active > .megamaxmenu-submenu.megamaxmenu-submenu-type-mega,".
					".megamaxmenu-$menu_id:not(.megamaxmenu-transition-slide) .megamaxmenu-submenu.megamaxmenu-submenu-type-mega,".
					".megamaxmenu .megamaxmenu-force > .megamaxmenu-submenu";

		//Assume pixels if no units provided
		if( is_numeric( $val ) ){
			$val.='px';
		}

		$menu_styles[$selector]['max-height'] = $val;
	}

}

/*
 * VERTICAL SUBMENU WIDTH
 */

function megamaxmenu_get_menu_style_vertical_submenu_width( $field , $menu_id , &$menu_styles ){

	$submenu_width = megamaxmenu_op( $field['name'] , $menu_id );
	if( $submenu_width ){
		$selector = ".megamaxmenu-$menu_id.megamaxmenu-vertical .megamaxmenu-submenu-type-mega";

		//Assume pixels if no units provided
		if( is_numeric( $submenu_width ) ){
			$submenu_width.='px';
		}

		$menu_styles[$selector]['width'] = $submenu_width;
	}

}


/*
 * IMAGE WIDTH
 */
function megamaxmenu_get_menu_style_image_width( $field , $menu_id , &$menu_styles ){

	$rules = array(
		'image_left' => array(
			'selector'	=> ".megamaxmenu-$menu_id .megamaxmenu-item-layout-image_left > .megamaxmenu-target-text",
			'property'	=> 'padding-left',
		),
		'image_right' => array(
			'selector'	=> ".megamaxmenu-$menu_id .megamaxmenu-item-layout-image_right > .megamaxmenu-target-text",
			'property'	=> 'padding-right',
		),
	);

	$value = megamaxmenu_op( $field['name'] , $menu_id ); // + 10;
	if( !$value ) return;
	$value+= 10;	//10px padding
	$value.= 'px';
	//echo $value;

	foreach( $rules as $layout => $rule ){

		$selector = $rule['selector'];
		$property = $rule['property'];

		if( !isset( $menu_styles[$selector] ) ){
		//if( !isset( $menu_styles[$menu_id][$selector] ) ){
			$menu_styles[$selector] = array();
			//$menu_styles[$menu_id][$selector] = array();
		}

		$menu_styles[$selector][$property] = $value;
		//$menu_styles[$menu_id][$selector][$property] = $value;

	}

}


/*
 * IMAGE TEXT TOP PADDING
 */
function megamaxmenu_get_menu_style_image_text_top_padding( $field , $menu_id , &$menu_styles ){

	$value = megamaxmenu_op( $field['name'] , $menu_id );
	if( $value ){
		if( is_numeric( $value ) ) $value.= 'px';
		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item-layout-image_left > .megamaxmenu-target-title, .megamaxmenu-$menu_id .megamaxmenu-item-layout-image_right > .megamaxmenu-target-title" ;
		$menu_styles[$selector]['padding-top'] = $value;
	}

}


/*
 * TRANSITION DURATION
 */
function megamaxmenu_get_menu_style_transition_duration( $field , $menu_id , &$menu_styles ){

	$duration = megamaxmenu_op( $field['name'] , $menu_id );

	if( $duration ){

		//Assume seconds if no units provided
		if( is_numeric( $duration ) ){
			$duration.= 's';
		}

		$selector = ".megamaxmenu-$menu_id .megamaxmenu-item .megamaxmenu-submenu-drop";
		$menu_styles[$selector]['-webkit-transition-duration'] = $duration;
		$menu_styles[$selector]['-ms-transition-duration'] = $duration;
		$menu_styles[$selector]['transition-duration'] = $duration;
	}
}


/*
 * DROPDOWN WITHIN MEGA
 */
function megamaxmenu_get_menu_style_dropdown_within_mega( $field , $menu_id , &$menu_styles ){
	if( megamaxmenu_op( $field['name'] , $menu_id ) == 'on' ){
		$menu_styles[".megamaxmenu-$menu_id .megamaxmenu-item.megamaxmenu-active > .megamaxmenu-submenu-drop.megamaxmenu-submenu-type-mega"]['overflow'] = 'visible';
	}
}
