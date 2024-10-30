<?php

add_filter( 'megamaxmenu_custom_menu_item_types' , 'megamaxmenu_pro_custom_menu_item_types' );
function megamaxmenu_pro_custom_menu_item_types( $items ){

	$items['custom_content'] = array(
		'label'	=> __( 'Custom Content' , 'megamaxmenu' ),
		'title' => '['.__( 'Custom' , 'megamaxmenu' ) .']',
		'desc'	=> __( 'A block of custom content, which can include HTML and shortcodes' , 'megamaxmenu' ),
		'panels'=> array( 'custom_content' , 'custom_content_layout' , 'responsive' ),
	);

	$items['tabs'] = array(
		'label'	=>	__( 'Tabs Block' , 'megamaxmenu' ),
		'title' =>	'['.__( 'Tabs' , 'megamaxmenu' ) . ']',
		'panels'=> array( 'tabs' , 'responsive' ),
		'desc'	=> __( 'A group of tabs' , 'megamaxmenu' ),
		//'url'	=>	'#megamaxmenu-tabs',
	);

	$items['dynamic_posts'] = array(
		'label'	=>	__( 'Dynamic Posts' , 'megamaxmenu' ),
		'title' =>	'['.__( 'Dynamic Posts' , 'megamaxmenu' ) . ']',
		'desc'	=>	__( 'Dynamically generated post content.', 'megamaxmenu' ),
		'panels'=> array( 'dynamic_posts' , 'general', 'image' , 'icon', 'layout', 'responsive',
		'submenu', 'customize' ),
	);

	$items['dynamic_terms'] = array(
		'label'	=>	__( 'Dynamic Terms' , 'megamaxmenu' ),
		'title' =>	'['.__( 'Dynamic Terms' , 'megamaxmenu' ) . ']',
		'desc'	=>	__( 'Dynamically generated taxonomy/category content.', 'megamaxmenu' ),
		'panels'=> array( 'dynamic_terms' , 'general' , 'icon' , 'layout' , 'responsive' , 'submenu' , 'customize' ),
	);

	$items['menu_segment']	= array(
		'label'	=>	__( 'Menu Segment' , 'megamaxmenu' ),
		'title'	=>	'['.__( 'Menu Segment' , 'megamaxmenu' ).']',
		'desc'	=>	__( 'Insert a separate menu into the menu tree.' , 'megamaxmenu' ),
		'panels'=>	array( 'menu_segment' ), //, 'layout'
	);
	

	$items['widget_area']	= array(
		'label'	=> __( 'Widget Area' , 'megamaxmenu' ),
		'title'	=> '['.__( 'Widget Area' , 'megamaxmenu' ).']',
		'desc'	=> __( 'Insert a Widget Area into the menu' , 'megamaxmenu' ),
		'panels'=> array( 'widgets' , 'widget_layout' , 'responsive' ),
	);


	//Image
	
	//Search bar
	
	//Shopping cart?



	return $items;	
}