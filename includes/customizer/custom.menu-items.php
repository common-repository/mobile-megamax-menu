<?php

/***********

MENU ITEM STYLES

***********/

function megamaxmenu_generate_item_styles(){
	$item_styles = get_option( MEGAMAXMENU_MENU_ITEM_STYLES , array() );

	$styles = '';

	$prefix_boost = megamaxmenu_op( 'custom_prefix' , 'general' );

	foreach( $item_styles as $item_id => $rules ){

		if( empty( $rules ) ) continue;

		$spacing = 12;
		$delim = "/* $item_id */";
		$remainder = $spacing - strlen( $delim );
		if( $remainder < 0 ) $remainder = 0;
		$styles.= $delim . str_repeat( ' ' , $remainder );

		$k = 0;
		foreach( $rules as $selector => $property_map ){
			if( $k > 0 ) $styles.= str_repeat( ' ' , $spacing );
			$k++;

			$styles.= "$prefix_boost $selector { ";
			foreach( $property_map as $property => $value ){
				$styles.= "$property:$value; ";
			}
			$styles.= "}\n";
		}
	}
	return $styles;
}


//Delete when menu item is deleted
add_action( 'before_delete_post' , 'megamaxmenu_delete_item_custom_styles' );
function megamaxmenu_delete_item_custom_styles( $post_id ){

	if( 'nav_menu_item' == get_post_type( $post_id ) ){

		//Reset styles for post ID
		$custom_styles = _MEGAMAXMENU()->get_item_styles( $post_id );

		//Update DB
		_MEGAMAXMENU()->update_item_styles();

		//Force regerenation of styles
		megamaxmenu_reset_generated_styles();

		// megamaxp( _MEGAMAXMENU()->get_item_styles() , 2 );
		// die();

		//unset( $custom_styles[$item_id] );
		//update_option( MEGAMAXMENU_MENU_ITEM_STYLES , $custom_styles );
	}
}




//Saving is deferred until after all properties have been processed for efficiency
add_action( 'megamaxmenu_after_menu_item_save' , 'megamaxmenu_update_item_styles' , 10 , 1 );
function megamaxmenu_update_item_styles( $menu_item_id ){
	_MEGAMAXMENU()->update_item_styles();
}


function megamaxmenu_set_item_style( $item_id , $selector , $property_map ){
	_MEGAMAXMENU()->set_item_style( $item_id , $selector , $property_map );
}


function megamaxmenu_item_save_background_color( $item_id , $setting , $val , &$saved_settings ){
	//up( $setting ); //echo $val; //die();

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id > .megamaxmenu-target";

	$property_map = array(
		'background'	=> $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}
function megamaxmenu_item_save_font_color( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id > .megamaxmenu-target";

	$property_map = array(
		'color'	=> $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}

function megamaxmenu_item_save_background_color_active( $item_id , $setting , $val , &$saved_settings ){
	//up( $setting ); //echo $val; //die();

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-active > .megamaxmenu-target, .megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id > .megamaxmenu-target:hover, .megamaxmenu .megamaxmenu-submenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-active > .megamaxmenu-target, .megamaxmenu .megamaxmenu-submenu .megamaxmenu-item.megamaxmenu-item-$item_id > .megamaxmenu-target:hover";	//removed notouch

	$property_map = array(
		'background'	=> $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}
function megamaxmenu_item_save_font_color_active( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	//$selector = ".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-active > .megamaxmenu-target, .megamaxmenu .megamaxmenu-submenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-active > .megamaxmenu-target";
	$selector = ".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-active > .megamaxmenu-target, .megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id:hover > .megamaxmenu-target, .megamaxmenu .megamaxmenu-submenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-active > .megamaxmenu-target, .megamaxmenu .megamaxmenu-submenu .megamaxmenu-item.megamaxmenu-item-$item_id:hover > .megamaxmenu-target";

	$property_map = array(
		'color'	=> $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}

function megamaxmenu_item_save_background_color_current( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-current-menu-item > .megamaxmenu-target,".
				".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-current-menu-ancestor > .megamaxmenu-target";

	$property_map = array(
		'background'	=> $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}
function megamaxmenu_item_save_font_color_current( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-current-menu-item > .megamaxmenu-target,".
				".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-current-menu-ancestor > .megamaxmenu-target";

	$property_map = array(
		'color'	=> $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}

/*
 * ITEM PADDING
 */
function megamaxmenu_item_save_padding( $item_id , $setting , $val , &$saved_settings ){

	if( $val || is_numeric( $val ) ){	//include '0'
		if( is_numeric( $val ) ){
			$val.= 'px';
		}
		$selector =
			".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id > .megamaxmenu-target,".
			".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id > .megamaxmenu-content-block,".
			".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id.megamaxmenu-custom-content-padded";

		$property_map = array(
			'padding'	=> $val
		);

		megamaxmenu_set_item_style( $item_id , $selector , $property_map );
	}
}


/*
 * ROW PADDING
 */
function megamaxmenu_item_save_row_padding( $item_id , $setting , $val , &$saved_settings ){

	if( $val || is_numeric( $val ) ){	//include '0'
		if( is_numeric( $val ) ){
			$val.= 'px';
		}
		$selector = ".megamaxmenu .megamaxmenu-row-id-$item_id";

		$property_map = array(
			'padding'	=> $val
		);

		megamaxmenu_set_item_style( $item_id , $selector , $property_map );
	}
}

/*
 * TAB PANELS PADDING
 */
function megamaxmenu_item_save_panels_padding( $item_id , $setting , $val , &$saved_settings ){

	if( $val || is_numeric( $val ) ){	//include '0'
		if( is_numeric( $val ) ){
			$val.= 'px';
		}
		$selector = ".megamaxmenu .megamaxmenu-tabs.megamaxmenu-item-$item_id > .megamaxmenu-tabs-group > .megamaxmenu-tab > .megamaxmenu-tab-content-panel";

		$property_map = array(
			'padding'	=> $val
		);

		megamaxmenu_set_item_style( $item_id , $selector , $property_map );
	}
}


/*
 * SUBMENUS
 */

function megamaxmenu_item_save_submenu_column_divider_color( $item_id , $setting , $val , &$saved_settings ){
	if( !$val ) return;

	$selector = "body:not(.rtl) .megamaxmenu .megamaxmenu-submenu-id-$item_id > .megamaxmenu-column + .megamaxmenu-column:not(.megamaxmenu-clear-row)";

	if( $setting['id'] == 'row_column_dividers' ){
		$selector = ".megamaxmenu .megamaxmenu-row-id-$item_id > .megamaxmenu-column + .megamaxmenu-column:not(.megamaxmenu-clear-row)";
	}

	$property_map = array(
		'border-left' => "1px solid $val",
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );


	//RTL
	$rtl_selector = ".rtl .megamaxmenu .megamaxmenu-submenu-id-$item_id > .megamaxmenu-column + .megamaxmenu-column:not(.megamaxmenu-clear-row)";
	$rtl_property_map = array(
		'border-right' => "1px solid $val"
	);
	megamaxmenu_set_item_style( $item_id , $rtl_selector , $rtl_property_map );


	//If auto-clear is on, and we have a new row, remove the left border from the first column in the second row
	if( isset( $saved_settings['submenu_column_default'] ) && $saved_settings['submenu_column_default'] != 'auto' ){
		if( isset( $saved_settings['submenu_column_autoclear'] ) && $saved_settings['submenu_column_autoclear'] == 'on' ){

			$def = $saved_settings['submenu_column_default'];
			$cols = substr( $def , strlen( $def ) - 1 );

			//LTR
			$property_map = array(
				'border-left'	=> 'none',
			);
			$selector = "body:not(.rtl) .megamaxmenu .megamaxmenu-submenu-id-$item_id > .megamaxmenu-column + .megamaxmenu-column-$def:nth-child({$cols}n+1)";
			megamaxmenu_set_item_style( $item_id , $selector , $property_map );


			//RTL
			$rtl_property_map = array(
				'border-right'	=> 'none',
			);
			$rtl_selector = ".rtl .megamaxmenu .megamaxmenu-submenu-id-$item_id > .megamaxmenu-column + .megamaxmenu-column-$def:nth-child({$cols}n+1)";
			megamaxmenu_set_item_style( $item_id , $rtl_selector , $rtl_property_map );
		}
	}


}
function megamaxmenu_item_save_submenu_column_min_height( $item_id , $setting , $val , &$saved_settings ){
	if( !$val ) return;

	if( is_numeric( $val ) ) $val.='px';

	$selector = ".megamaxmenu .megamaxmenu-submenu-id-$item_id > .megamaxmenu-column";

	if( $setting['id'] == 'row_column_min_height' ){
		$selector = ".megamaxmenu .megamaxmenu-row-id-$item_id > .megamaxmenu-column";
	}

	$property_map = array(
		'min-height' => $val,
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );
}


function megamaxmenu_item_save_submenu_background_color( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id";

	$property_map = array(
		'background-color' => $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );
}

function megamaxmenu_item_save_submenu_color( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id .megamaxmenu-target, .megamaxmenu .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id .megamaxmenu-target > .megamaxmenu-target-description";

	$property_map = array(
		'color' => $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );
}

function megamaxmenu_item_save_submenu_width( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id";

	if( is_numeric( $val ) ){
		$val.='px';
	}

	$property_map = array(
		'width' => $val,
		'min-width' => $val,
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );
}

function megamaxmenu_item_save_submenu_min_width( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id";

	if( is_numeric( $val ) ) $val.= 'px';

	$property_map = array(
		'min-width' => $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );
}

function megamaxmenu_item_save_submenu_min_height( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id";

	if( is_numeric( $val ) ) $val.= 'px';

	$property_map = array(
		'min-height' => $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );
}

function megamaxmenu_item_save_submenu_background_image( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id";

	$img_src = wp_get_attachment_image_src( $val , 'full' );
	$img_url = $img_src[0];
	$img_url = str_replace( 'http://' , '//' , $img_url );

	$background_image = "url($img_url)";

	$background_repeat = $saved_settings['submenu_background_image_repeat'];
	$background_position = $saved_settings['submenu_background_position'];
	$background_size = $saved_settings['submenu_background_size'];


	$property_map = array(
		'background-image'	=> $background_image,
		'background-repeat'	=> $background_repeat,
		'background-position' => $background_position,
		'background-size'	=> $background_size,
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}

function megamaxmenu_item_save_submenu_padding( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-active > .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id, .megamaxmenu .megamaxmenu-in-transition > .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id";

	$property_map = array(
		'padding'	=>	$val,
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}

function megamaxmenu_item_save_image_dimensions( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;


	$img_id = $saved_settings['item_image'];

	if( !$img_id ){
		if( $saved_settings['inherit_featured_image'] == 'on' ){
			//Dynamic Items
			if( isset( $saved_settings['dp_posts_per_page'] ) ){
				$img_id = 'dynamic_post';
			}
			else{
				$post_id = get_post_meta( $item_id , '_menu_item_object_id' , true );
				$thumb_id = get_post_thumbnail_id( $post_id );
				if( $thumb_id ) $img_id = $thumb_id;
			}
		}
	}


	//If there's no image set, we don't need to do anything
	if( $img_id ){

		if( in_array( $val , array( 'natural' , 'custom' ) ) ){

			$layout = $saved_settings['item_layout'];
			if( $layout == 'default' ) $layout = 'image_left';	//TODO cleanup - check Default Image Layout (for Configuration) and Submenu Item Layout

			if( in_array( $layout , array( 'image_left' , 'image_right' /*, 'image_top' , 'image_bottom'*/ ) ) ){

				$selector = ".megamaxmenu .megamaxmenu-item-$item_id > .megamaxmenu-target.megamaxmenu-item-layout-$layout > ";
				$img_size = $saved_settings['image_size'];
				if( $img_size == 'inherit' ){
					//If it's inherited, we can ignore it, as this will be set globally
					megamaxmenu_set_item_style( $item_id , false , false );
					return;
				}
				$img_w = '';

				//Determine Natural Dimensions
				if( $val == 'natural' ){
					//Dynamic Posts
					if( $img_id == 'dynamic_post' ){
						//Use the width of the chosen image size - assuming it is not 'inherit' or 'full'
						$dims = megamaxmenu_get_image_size_dimensions( $img_size );
						if( $dims ){
							$img_w = $dims['w'];
						}
						//This happens if the image size is Full or Inherit - unknown width at this point, so we can't do anything
						//User should choose an Image Size
						else{
							//We don't know, just reset
							megamaxmenu_set_item_style( $item_id , false , false );
							return;
						}
					}
					//Normal items
					else{
						$img_src = wp_get_attachment_image_src( $img_id , $img_size );
						$img_w = $img_src[1];
					}
				}
				else if( $val == 'custom' ){
					$img_w = intval( $saved_settings['image_width_custom'] );
				}



				$padding = $img_w + 10;

				$property = '';
				switch( $layout ){

					//Add padding to left of text
					case 'image_left':
						$property = 'padding-left';
						$selector.= '.megamaxmenu-target-text';
						break;

					//Add padding to right of text
					case 'image_right':
						$property = 'padding-right';
						$selector.= '.megamaxmenu-target-text';
						break;

					/*
					//Add padding to bottom of image
					case 'image_above':
						$property = 'padding-bottom';
						$selector.= '.megamaxmenu-image';
						$padding = 10;
						break;

					//Add padding to top of image
					case 'image_below':
						$property = 'padding-top';
						$selector.= '.megamaxmenu-image';
						$padding = 10;
						break;
					*/

				}

				$property_map = array(
					$property => $padding.'px',
				);

				megamaxmenu_set_item_style( $item_id , $selector , $property_map );
				return;
			}
		}
	}
	//Do this to make sure to trigger reset
	megamaxmenu_set_item_style( $item_id , false , false );
}

function megamaxmenu_item_save_image_width_custom( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	//If there's no image set, we don't need to do anything
	$img_id = $saved_settings['item_image'];

	if( !$img_id ){
		if( $saved_settings['inherit_featured_image'] == 'on' ){
			$post_id = get_post_meta( $item_id , '_menu_item_object_id' , true );
			$thumb_id = get_post_thumbnail_id( $post_id );
			if( $thumb_id ) $img_id = $thumb_id;
		}
	}

	if( $img_id ){

		if( $saved_settings['image_dimensions'] == 'custom' ){

			$layout = $saved_settings['item_layout'];
			if( $layout == 'default' ) $layout = 'image_left';

			if( in_array( $layout , array( 'image_left' , 'image_right' /*, 'image_top' , 'image_bottom'*/ ) ) ){

				$selector = ".megamaxmenu .megamaxmenu-item-$item_id > .megamaxmenu-target.megamaxmenu-item-layout-$layout > ";

				$padding = $val + 10;

				$property = '';
				switch( $layout ){

					//Add padding to left of text
					case 'image_left':
						$property = 'padding-left';
						$selector.= '.megamaxmenu-target-text';
						break;

					//Add padding to right of text
					case 'image_right':
						$property = 'padding-right';
						$selector.= '.megamaxmenu-target-text';
						break;

				}

				$property_map = array(
					$property => $padding.'px',
				);

				megamaxmenu_set_item_style( $item_id , $selector , $property_map );
				return;
			}
		}
	}

	//Do this to make sure to trigger reset
	megamaxmenu_set_item_style( $item_id , false , false );
}

function megamaxmenu_item_save_image_text_top_padding( $item_id , $setting , $val , &$saved_settings ){

	if( !$val ) return;

	if( is_numeric( $val ) ) $val.= 'px';

	//$top_padding = $saved_settings['image_text_top_padding'];

	$selector = ".megamaxmenu .megamaxmenu-submenu.megamaxmenu-submenu-id-$item_id";
	$selector = ".megamaxmenu .megamaxmenu-item-$item_id > .megamaxmenu-item-layout-image_left > .megamaxmenu-target-title, .megamaxmenu .megamaxmenu-item-$item_id > .megamaxmenu-item-layout-image_right > .megamaxmenu-target-title" ;

	$property_map = array(
		'padding-top'	=>	$val,
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}



/* COLUMN BACKGROUND COLOR */
function megamaxmenu_item_save_column_background_color( $item_id , $setting , $val , &$saved_settings ){
	//up( $setting ); //echo $val; //die();

	if( !$val ) return;

	$selector = ".megamaxmenu .megamaxmenu-item.megamaxmenu-item-$item_id";

	$property_map = array(
		'background'	=> $val
	);

	megamaxmenu_set_item_style( $item_id , $selector , $property_map );

}
