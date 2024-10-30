<?php

/** Shortcodes **/

function megamaxmenu_shortcode( $atts ){

	extract(shortcode_atts(array(
		'instance_id'		=> '',		//deprecated
		'config_id'			=> 'main',

		'theme_location' 	=> '',
		'menu'				=> '',
	), $atts));

	//If an instance_id (deprecated) was passed, use it as the config_id
	if( $instance_id != '' ) $config_id = $instance_id;

	$args = array(
		'echo'	=> false,
	);

	if( $theme_location ){
		$args['theme_location'] = $theme_location;
	}
	if( $menu ){
		$args['menu'] = $menu;
	}

	return megamaxmenu( $config_id , $args );
}
add_shortcode( 'megamaxmenu' , 'megamaxmenu_shortcode' );



function megamaxmenu_direct_shortcode( $atts ){
	extract(shortcode_atts(array(
		'theme_location' => 'megamaxmenu'
	), $atts));

	return megaMaxMenu_direct( $theme_location , false , false );
}
add_shortcode( 'megaMaxMenu_direct' , 'megamaxmenu_direct_shortcode' );


function megaMaxMenu_easyIntegrate_shortcode( $atts = array(), $data = '' ){
	extract(shortcode_atts(array(
		'echo'	=>	'true',
	), $atts));

	$echo = $echo == 'false' ? false : true;

	$args = array(
		'echo'	=> $echo
	);

	return megaMaxMenu_easyIntegrate( $config_id = 'main' , $args );

}
add_shortcode( 'megaMaxMenu_easyIntegrate' , 'megaMaxMenu_easyIntegrate_shortcode');