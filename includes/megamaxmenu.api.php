<?php

function megamaxmenu( $config_id = 'main' , $args = array() ){
	$args = megamaxmenu_get_nav_menu_args( $args , 'api' , $config_id );
	return wp_nav_menu( $args );
}


function megamaxmenu_toggle( $target = '_any_' , $config_id = 'main' , $echo = true , $args = array() ){

	extract( wp_parse_args( $args , array(
		'toggle_content'	=> 'Menu',
		'icon_class'		=> 'bars',
		'tag'				=> 'a',
		'skin'				=> '',
		'toggle_id'			=> '',
		'content_align'		=> 'left',
		'align'				=> 'full',
		'classes'			=> '',
	)));

	//responsive_toggle_text

	if( !$skin && $config_id ){
		$skin = megamaxmenu_op( 'skin' , $config_id );
	}

	$class = 'megamaxmenu-responsive-toggle';
	if( $config_id ) $class.= ' megamaxmenu-responsive-toggle-'.$config_id;
	if( $skin ) $class.= ' megamaxmenu-skin-'.$skin;
	if( isset( $args['theme_location'] ) )$class.= ' megamaxmenu-loc-'.sanitize_title( $args['theme_location'] );
	$class.= ' megamaxmenu-responsive-toggle-content-align-'.$content_align;
	$class.= ' megamaxmenu-responsive-toggle-align-'.$align;
	if( !$toggle_content ) $class.= ' megamaxmenu-responsive-toggle-icon-only';

	$id = '';
	if( $toggle_id ) $id = ' id="'.$toggle_id.'" ';

	$class.= ' '.$classes;
	$class = apply_filters( 'megamaxmenu_toggle_class' , $class , $config_id );

	$aria = '';
	if( megamaxmenu_op( 'aria_responsive_toggle' , 'general' ) == 'on' ){
		$aria = ' role="button" aria-controls="'.$target.'" ';
	}

	$toggle = '<'.$tag . $aria . $id.' class="'.$class.'" tabindex="0" data-megamaxmenu-target="'.
				$target.'">';

	if( $icon_class ){
		$toggle_icon_tag = megamaxmenu_op( 'icon_tag' , $config_id );
		if( !$toggle_icon_tag ) $toggle_icon_tag = 'i';
		$aria = '';
		if( megamaxmenu_op( 'aria_hidden_icons' , 'general' ) == 'on' ){	//TODO deprecate
			$aria = 'aria-hidden="true"';
		}
		$toggle.= '<'.$toggle_icon_tag.' class="fas fa-'.$icon_class.'" '.$aria.'></'.$toggle_icon_tag.'>';
	}
	if( $toggle_content ) $toggle.= do_shortcode( $toggle_content );
	$toggle.= '</'.$tag.'>';

	if( $echo ) echo $toggle;
	return $toggle;
}



function megamaxmenu_toggle_shortcode( $atts, $content ){

	extract( shortcode_atts( array(
		'instance_id'	=> '',
		'config_id'		=> 'main',
		'target' 		=> '_any_',
		'toggle_id' 	=> '',
		'tag'			=> 'a',
		'icon_class'	=> 'bars',
		'skin'			=> '',
	), $atts, 'megamaxmenu_toggle' ) );

	//If an instance_id (deprecated) was passed, use it as the config_id
	if( $instance_id != '' ) $config_id = $instance_id;

	$args = array();
	if( $content ) $args['toggle_content'] = $content;
	$args['icon_class']		= $icon_class;
	$args['tag']			= $tag;
	$args['skin']			= $skin;

	$toggle = megamaxmenu_toggle( $target , $config_id , false , $args );

	return $toggle;
}
add_shortcode( 'megamaxmenu_toggle' , 'megamaxmenu_toggle_shortcode' );



function megaMaxMenu_direct( $theme_location = 'megamaxmenu' , $filter = true , $echo = true , $args = array() , $config_id = 'main' ){
	$args['theme_location'] = $theme_location;
	$args['filter'] = $filter;
	$args['echo'] = $echo;

	return megamaxmenu( $config_id , $args );
}

function megaMaxMenu_easyIntegrate( $config_id = 'main' , $args = array() ){

	//Check that Easy Integration is enabled
	if( megamaxmenu_op( 'megamaxmenu_theme_location' , 'general' ) != 'on' ){
		$msg = 'To use Easy Integration, please enable the <strong>Register Easy Integration MegaMaxMenu Theme Location</strong> setting in the <a target="_blank" href="'.admin_url( 'themes.php?page=megamaxmenu-settings').'">MegaMaxMenu Control Panel > General Settings > Advanced</a> and <a target="_blank" href="'.admin_url( 'nav-menus.php?action=locations' ).'">assign a menu</a> to the <strong>MegaMaxMenu [Easy Integration]</strong> theme locaiton';
		megamaxmenu_admin_notice( $msg );
		return;
	}

	//Check that the theme location has been assigned
	else if( !has_nav_menu( 'megamaxmenu' ) ){
		$msg = 'To use Easy Integration, please <a target="_blank" href="'.admin_url( 'nav-menus.php?action=locations' ).'">assign a menu</a> to the <strong>MegaMaxMenu [Easy Integration]</strong> theme location';
		megamaxmenu_admin_notice( $msg );
		return;
	}


	//$args = array();
	$args['theme_location'] = 'megamaxmenu';
	return megamaxmenu( $config_id , $args );
}
