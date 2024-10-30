<?php

/**
 * CREATE INSTANCE MANAGER
 */

add_action( 'megamaxmenu_settings_before' , 'megamaxmenu_instance_manager');

function megamaxmenu_instance_manager(){

	?>

	<div class="megamaxmenu_instance_manager">

		<a class="megamaxmenu_instance_toggle megamaxmenu_instance_button">+ Add MegaMaxMenu Configuration</a>

		<div class="megamaxmenu_instance_wrap megamaxmenu_instance_container_wrap">

			<div class="megamaxmenu_instance_container">

				<h3>Add MegaMaxMenu Configuration</h3>

				<form class="megamaxmenu_instance_form">
					<input class="megamaxmenu_instance_input" type="text" name="megamaxmenu_instance_id" placeholder="configuration_id" />
					<?php wp_nonce_field( 'megamaxmenu-add-instance' ); ?>
					<a class="megamaxmenu_instance_button megamaxmenu_instance_create_button">Create Configuration</a>
				</form>

				<p class="megamaxmenu_instance_form_desc">Enter an ID for your new menu configuration.  This ID will be used when printing the menu,
					and must contain only English letters, hyphens, and underscores.  <a class="megamaxmenu_instance_notice_close" href="#">close</a></p>

				<span class="megamaxmenu_instance_close">&times;</span>

			</div>

		</div>


		<div class="megamaxmenu_instance_wrap megamaxmenu_instance_notice_wrap megamaxmenu_instance_notice_success">
			<div class="megamaxmenu_instance_notice">
				New menu created. <a href="<?php echo admin_url('themes.php?page=megamaxmenu-settings'); ?>" class="megamaxmenu_instance_button">Refresh Page</a>
				<p>Note: Any setting changes you've made have not been saved yet.  <a class="megamaxmenu_instance_notice_close" href="#">close</a></p>
			</div>
		</div>

		<div class="megamaxmenu_instance_wrap megamaxmenu_instance_notice_wrap megamaxmenu_instance_notice_error">
			<div class="megamaxmenu_instance_notice">
				New menu configuration creation failed.  <span class="megamaxmenu-error-message">You may have a PHP error on your site which prevents AJAX requests from completing.</span>  <a class="megamaxmenu_instance_notice_close" href="#">close</a>
			</div>
		</div>

		<div class="megamaxmenu_instance_wrap megamaxmenu_instance_notice_wrap megamaxmenu_instance_delete_notice_success">
			<div class="megamaxmenu_instance_notice">
				Configuration Deleted.  <a class="megamaxmenu_instance_notice_close" href="#">close</a></p>
			</div>
		</div>

		<div class="megamaxmenu_instance_wrap megamaxmenu_instance_notice_wrap megamaxmenu_instance_delete_notice_error">
			<div class="megamaxmenu_instance_notice">
				Menu Configuration deletion failed.  <span class="megamaxmenu-delete-error-message">You may have a PHP error on your site which prevents AJAX requests from completing.</span>  <a class="megamaxmenu_instance_notice_close" href="#">close</a>
			</div>
		</div>


	</div>

	<?php
}

function megamaxmenu_add_instance_callback(){

	check_ajax_referer( 'megamaxmenu-add-instance' , 'megamaxmenu_nonce' );

	$response = array();

	$serialized_settings = _MEGAMAXMENU()->sanitize_data($_POST['megamaxmenu_data'], '');
	$dirty_settings = array();
	parse_str( $serialized_settings, $dirty_settings );

	//ONLY ALLOW SETTINGS WE'VE DEFINED
	$data = wp_parse_args( $dirty_settings, array( 'megamaxmenu_instance_id' ) );

	$new_id = $data['megamaxmenu_instance_id'];

	if( $new_id == '' ){
		$response['error'] = 'Please enter an ID. ';
	}
	else{
		//$new_id = sanitize_title( $new_id );
		$new_id = sanitize_key( $new_id );

		//update
		$menus = get_option( MEGAMAXMENU_MENU_INSTANCES , array() );

		if( in_array( $new_id , $menus ) ){
			$response['error'] = 'That ID is already taken. ';
		}
		else if( in_array( $new_id , array( 'general' , 'main' , 'help' , 'updates' ) ) ){
			$response['error'] = 'That ID is reserved for plugin use.  Please choose another.';
		}
		else{
			$menus[] = $new_id;
			update_option( MEGAMAXMENU_MENU_INSTANCES , $menus );
		}

		$response['id'] = $new_id;
	}

	$response['data'] = $data;

	echo json_encode( $response );

	die();
}
add_action( 'wp_ajax_megamaxmenu_add_instance', 'megamaxmenu_add_instance_callback' );


function megamaxmenu_delete_instance_callback(){

	check_ajax_referer( 'megamaxmenu-delete-instance' , 'megamaxmenu_nonce' );

	$response = array();
//echo json_encode( $_POST['megamaxmenu_data'] );
//die();
	//$serialized_settings = $_POST['megamaxmenu_data'];
	//$dirty_settings = array();
	//parse_str( $serialized_settings, $dirty_settings );

	$dirty_settings = _MEGAMAXMENU()->sanitize_data($_POST['megamaxmenu_data'], '');

	//ONLY ALLOW SETTINGS WE'VE DEFINED
	$data = wp_parse_args( $dirty_settings, array( 'megamaxmenu_instance_id' ) );

	$id = $data['megamaxmenu_instance_id'];

	if( $id == '' ){
		$response['error'] = 'Missing ID';
	}
	else{

		$menus = get_option( MEGAMAXMENU_MENU_INSTANCES , array() );

		if( !in_array( $id , $menus ) ){
			$response['error'] = 'ID not in $menus ['.$id.']';
		}
		else{

			//Remove Menu from menus list in DB
			$i = array_search( $id , $menus );
			if( $i !== false ) unset( $menus[$i] );
			update_option( MEGAMAXMENU_MENU_INSTANCES , $menus );

			//Remove menu's custom styles
			megamaxmenu_delete_menu_styles( $id );

			$response['menus'] = $menus;
		}

		$response['id'] = $id;
	}

	$response['data'] = $data;

	echo json_encode( $response );

	die();
}
add_action( 'wp_ajax_megamaxmenu_delete_instance', 'megamaxmenu_delete_instance_callback' );
