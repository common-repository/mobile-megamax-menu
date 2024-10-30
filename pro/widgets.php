<?php

function megamaxmenu_item_save_create_auto_widget_area( $item_id , $setting , $val , &$saved_settings ){

	$menu_item_widget_areas = get_option( MEGAMAXMENU_MENU_ITEM_WIDGET_AREAS , array() );

	//Widget Area ID
	//$widget_area_id = 'umitem_'.$item_id;
	$widget_area_id = $item_id;

	//If Widget Area Name is set, set it
	if( $val ){
		$menu_item_widget_areas[$widget_area_id] = $val;
	}
	//Remove if Widget Area name is blank
	else{
		unset( $menu_item_widget_areas[$widget_area_id] );
	}

	update_option( MEGAMAXMENU_MENU_ITEM_WIDGET_AREAS , $menu_item_widget_areas );

}


add_action( 'init' , 'megamaxmenu_register_menu_item_auto_widget_areas' , 500 );
function megamaxmenu_register_menu_item_auto_widget_areas(){
	$menu_item_widget_areas = get_option( MEGAMAXMENU_MENU_ITEM_WIDGET_AREAS , array() );

	foreach( $menu_item_widget_areas as $id => $name ){
		register_sidebar( array(
			'name'			=> '[MegaMaxMenu] '.$name,
			'id'			=> 'umitem_'.$id,
			'description'	=> __( 'MegaMaxMenu Custom Widget Area for Menu Item ', 'megamaxmenu' ).$id, // . '. <a href="'.admin_url('themes.php?page=megamaxmenu-settings&do=widget-manager').'">Manage</a>',
			'before_title'	=> '<h3 class="megamaxmenu-widgettitle megamaxmenu-target">',
			'after_title'	=> '</h3>',
			'before_widget'	=> '<li id="%1$s" class="widget %2$s megamaxmenu-widget megamaxmenu-column megamaxmenu-item-header">',
			'after_widget'	=> '</li>',
			//'class'			=> 'megamaxmenu-widget',
		));
	}

	$widget_areas = megamaxmenu_get_widget_areas();

	foreach( $widget_areas as $id => $name ){

		//$name = isset( $names[$k] ) ? trim( $names[$k] ) : 'MegaMaxMenu Widget Area ' . $k;

		register_sidebar( array(
			'name'			=> '[MegaMaxMenu] '.$name,
			'id'			=> $id,
			'description'	=> __( 'You can assign this widget area to a menu item in Appearance > Menus', 'megamaxmenu' ),
			'before_title'	=> '<h3 class="megamaxmenu-widgettitle megamaxmenu-target">',
			'after_title'	=> '</h3>',
			'before_widget'	=> '<li id="%1$s" class="widget %2$s megamaxmenu-widget megamaxmenu-column megamaxmenu-item-header">',
			'after_widget'	=> '</li>',
			//'class'			=> 'megamaxmenu-widget',
		));
	}

	
}

function megamaxmenu_get_widget_areas(){

	$widget_areas = array();

	$num_widget_areas = megamaxmenu_op( 'num_widget_areas' , 'general' , 0 );
	$widget_area_names = megamaxmenu_op( 'widget_area_names' , 'general' , '' );

	$names = explode( ',' , $widget_area_names );

	if( $num_widget_areas ){
		for( $k = 0; $k < $num_widget_areas; $k++ ){

			$id = 'megamaxmenu-sidebar-'.($k+1);
			//echo $id;
			$name = ( isset( $names[$k] ) && trim( $names[$k] ) ) ? trim( $names[$k] ) : 'MegaMaxMenu Widget Area ' . ($k+1);
			
			$widget_areas[$id] = $name;
		}
	}

	return $widget_areas;
}


function megamaxmenu_get_widget_area_ops(){

	$widget_areas = array();
	$widget_areas[''] = __( 'None' , 'megamaxmenu' );

	$widget_areas = array_merge( $widget_areas , megamaxmenu_get_widget_areas() );

	return $widget_areas;
}