<?php

add_action( 'admin_bar_menu', 'megamaxmenu_add_toolbar_items', 100 );

function megamaxmenu_toolbar_icon( $icon_name ){
	if( is_admin() ) return '';
	return '<i class="fas fa-'.$icon_name.'"></i> ';
}

function megamaxmenu_add_toolbar_items( $admin_bar ){

	if( !current_user_can( 'manage_options' ) ) return;

	if( megamaxmenu_op( 'megamaxmenu_toolbar' , 'general' ) != 'on' ) return;

	$admin = is_admin();

	$admin_bar->add_node( array(
		'id'    => 'megamaxmenu',
		'title' => megamaxmenu_toolbar_icon( 'cogs' ). 'MegaMaxMenu',
		'href'  => admin_url( 'themes.php?page=megamaxmenu-settings' ),
		'meta'  => array(
			'title' => __( 'MegaMaxMenu' , 'MegaMaxMenu' ),
		),
	));

	$admin_bar->add_node( array(
		'id'    => 'megamaxmenu_customize',
		'parent' => 'megamaxmenu',
		'title' => megamaxmenu_toolbar_icon('eye') .__( 'Customize' , 'megamaxmenu' ),
		'href'  => admin_url( 'customize.php' ),
		'meta'  => array(
			'title' => __( 'Configure the MegaMaxMenu Settings' , 'megamaxmenu' ),
			'target' => '_blank',
			'class' => ''
		),
	));

	$admin_bar->add_node( array(
		'id'    => 'megamaxmenu_control_panel',
		'parent' => 'megamaxmenu',
		'title' => megamaxmenu_toolbar_icon( 'sliders-h' ).__( 'MegaMaxMenu Control Panel' , 'megamaxmenu' ),
		'href'  => admin_url( 'themes.php?page=megamaxmenu-settings' ),
		'meta'  => array(
			'title' => '<i class="fas fa-sliders"></i> '.__( 'Configure the MegaMaxMenu Settings' , 'megamaxmenu' ),
			'target' => '_blank',
			'class' => ''
		),
	));

	$admin_bar->add_node( array(
		'id'    	=> 'megamaxmenu_edit_menus',
		'parent' 	=> 'megamaxmenu',
		'title' 	=> megamaxmenu_toolbar_icon( 'pencil-alt' ).__( 'Edit Menus', 'megamaxmenu' ),
		'href'  	=> admin_url( 'nav-menus.php' ),
		'meta'  	=> array(
			'title' => __('Add, remove, and configure menu items' , 'megamaxmenu' ),
			'target' => '_blank',
			'class' => ''
		),
	));


	$menus = wp_get_nav_menus( array('orderby' => 'name') );
	foreach( $menus as $menu ){
		$admin_bar->add_node( array(
			'id'    	=> 'megamaxmenu_edit_menus_'.$menu->slug,
			'parent' 	=> 'megamaxmenu_edit_menus',
			'title' 	=> /*__( 'Edit' , 'megamaxmenu' ) . ' ' .*/ $menu->name,
			'href'  	=> admin_url( 'nav-menus.php?action=edit&menu='.$menu->term_id ),
			'meta'  	=> array(
				'title' => __('Configure' , 'megamaxmenu' ) . ' '. $menu->name,
				'target' => '_blank',
				'class' => ''
			),
		));
	}


	$admin_bar->add_node( array(
		'id'		=> 'megamaxmenu_assign_menus',
		'parent'	=> 'megamaxmenu',
		'title'		=> megamaxmenu_toolbar_icon( 'bars' ).__( 'Assign Menus', 'megamaxmenu' ),
		'href'		=> admin_url( 'nav-menus.php?action=locations' ),
		'meta'		=> array(
			'title'	=> __( 'Theme Location Manager' , 'megamaxmenu' ),
			'target'=> '_blank',
			'class'	=> ''
		),
	));

	$admin_bar->add_node( array(
		'id'    	=> 'megamaxmenu_troubleshooter',
		'parent'	=> 'megamaxmenu',
		'title' 	=> megamaxmenu_toolbar_icon( 'wrench' ).__( 'Troubleshooter', 'megamaxmenu' ),
		'href'  	=> MEGAMAXMENU_TROUBLESHOOTER_URL,
		'meta'  	=> array(
			'title' => __( 'MegaMaxMenu Troubleshooter' , 'megamaxmenu' ),
			'target' => '_blank',
			'class' => ''
		),
	));

	$admin_bar->add_node( array(
		'id'    	=> 'megamaxmenu_support',
		'parent'	=> 'megamaxmenu',
		'title' 	=> megamaxmenu_toolbar_icon( 'life-ring' ).__( 'Support / Help', 'megamaxmenu' ),
		'href'  	=> 'https://megamax.menu',
		'meta'  	=> array(
			'title' => __( 'MegaMaxMenu Support Center' , 'megamaxmenu' ),
			'target' => '_blank',
			'class' => ''
		),
	));


	$admin_bar->add_node( array(
		'id'    	=> 'megamaxmenu_sandbox',
		'parent'	=> 'megamaxmenu',
		'title' 	=> megamaxmenu_toolbar_icon( 'binoculars' ).__( 'View in Sandbox', 'megamaxmenu' ),
		'href'  	=> megamaxmenu_sandbox_url(),
		'meta'  	=> array(
			'title' => __( 'View your menu in a sandbox without CSS or JS interference' , 'megamaxmenu' ),
			'target' => '_blank',
			'class' => ''
		),
	));



	if( is_admin() ){
		//Diagnostics
		if( megamaxmenu_op( 'diagnostics' , 'general' ) == 'on' ){
			$admin_bar->add_node( array(
				'id'    	=> 'megamaxmenu_diagnostics',
				'parent'	=> 'megamaxmenu',
				'title' 	=> megamaxmenu_toolbar_icon( 'stethoscope' ).__( 'Diagnostics (Alpha) - Front End Only', 'megamaxmenu' ),
				'href'		=> site_url('?megamaxmenu_diagnostics'),
				'meta'  	=> array(
					'title' => __( 'Please click this link on the front end of your site to load the Diagnostics Tool' , 'megamaxmenu' ),
					'class' => 'megamaxmenu-diagnostics-loader-button'
				),
			));
		}
	}


	if( !is_admin() ){

		//Diagnostics
		if( megamaxmenu_op( 'diagnostics' , 'general' ) == 'on' ){
			$admin_bar->add_node( array(
				'id'    	=> 'megamaxmenu_diagnostics',
				'parent'	=> 'megamaxmenu',
				'title' 	=> megamaxmenu_toolbar_icon( 'stethoscope' ).__( 'Diagnostics (Alpha)', 'megamaxmenu' ),
				'href'		=> '#',
				'meta'  	=> array(
					'title' => __( 'Load diagnostics script (experimental feature in Alpha development)' , 'megamaxmenu' ),
					'class' => 'megamaxmenu-diagnostics-loader-button'
				),
			));
		}


		//Loading Message
		$admin_bar->add_node( array(
			'id'    => 'megamaxmenu_loading',
			'title' => megamaxmenu_toolbar_icon( 'exclamation-triangle' ). ' MegaMaxMenu waiting to load...',
			'href'  => '#',
		));

		$loading_msg = __( 'If this message does not disappear, it means that MegaMaxMenu\'s javascript has not been able to load.  This most commonly indicates that you have a javascript error on this page, which will need to be resolved in order to allow MegaMaxMenu to run - or that your MegaMaxMenu javascript is not being loaded, for example if your theme does not include the wp_footer() hook.' , 'megamaxmenu' );

		$admin_bar->add_node( array(
			'id'    => 'megamaxmenu_loading_msg',
			'parent'	=> 'megamaxmenu_loading',
			'title' => $loading_msg,
			'href'  => 'https://megamax.menu',
			'meta'	=> array(
				'target'	=> '_blank',
			),
		));
	}
}
