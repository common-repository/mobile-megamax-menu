<?php
add_filter( 'pre_update_option_'.MEGAMAXMENU_PREFIX.'general' , 'megamaxmenu_booster_prefix_update' , 10 , 2 );
function megamaxmenu_booster_prefix_update( $new_value , $old_value ){
  // megamaxp( $old_value );
  // megamaxp( $new_value );

  //If there is a custom prefix setting
  if( isset( $new_value['custom_prefix'] ) ){

    $new_prefix = $new_value['custom_prefix'];

    //Set the old prefix to its value, or empty string if it does not exist.
    $old_prefix = isset( $old_value['custom_prefix'] ) ? $old_value['custom_prefix'] : '';
//echo $new_prefix . ' :: ' .$old_prefix;
    //If the prefix has changed
    if( $new_prefix != $old_prefix ){

      //And the user has set it to something
      if( !empty( $new_prefix ) ){

        //Note that the setting has changed in the DB
        update_option( MEGAMAXMENU_BOOSTER_PREFIX_CHANGED , 1 );
      }
      //Setting is set to empty string - could consider removing unnecessary files
      else{}
    }
    //No change in prefix, do nothing
    else{}
  }
  //The field isn't set, we shouldn't be here
  else{}

  return $new_value;
}


add_action( 'megamaxmenu_settings_before' , 'megamaxmenu_booster_prefix_check_for_changes' , 10 , 0 );
//add_action( 'admin_init' , 'megamaxmenu_booster_prefix_check_for_changes' , 10 , 0 );
function megamaxmenu_booster_prefix_check_for_changes(){
  if( get_option( MEGAMAXMENU_BOOSTER_PREFIX_CHANGED , 0 ) == 1 ){
    megamaxmenu_booster_prefix_generate_all();
    delete_option( MEGAMAXMENU_BOOSTER_PREFIX_CHANGED );
  }
}


function megamaxmenu_get_uploads_dir(){
  $uploads_dir = wp_upload_dir();
  $uploads_dir = $uploads_dir['basedir'];
  return trailingslashit( $uploads_dir );
}

/*
 * Given a prefix (from the settings), generate all necessary styles
 */
function megamaxmenu_booster_prefix_generate_all(){
  $prefix = megamaxmenu_op( 'custom_prefix' , 'general' );

  if( !$prefix ) return;
  //echo 'Prefix: ' . $prefix;

  //Get filesystem access
  global $wp_filesystem;
  $redirect_url = wp_nonce_url( 'themes.php?page=megamaxmenu-settings&do=prefix_boost', 'um-filesystem-nonce' );
  $upload_dir = megamaxmenu_get_uploads_dir();

  //if( 'direct' === get_filesystem_method() ){

  if( megamaxmenu_connect_fs( $redirect_url , '' , $upload_dir ) ){
    $megamax_path = $upload_dir .'megamaxmenu';
    //$dir = $wp_filesystem->find_folder( $upload_dir );

    //If /uploads/megamaxmenu directory doesn't exist, create it
    if( !$wp_filesystem->is_dir( $megamax_path ) ){
      $wp_filesystem->mkdir( $megamax_path );
    }

    //1. Generate Main MegaMaxMenu CSS file
    megamaxmenu_booster_prefix_generate_main_stylesheet( $prefix , $megamax_path );

    //2. Generate Skins
    megamaxmenu_booster_prefix_generate_skins( $prefix , $megamax_path );

    //3. Generate Customizer CSS
    megamaxmenu_reset_generated_styles();


    //4. Generate Custom Menu Item Style CSS



  }
  else{
    return new WP_Error( 'filesystem_error', 'Cannot initialize WordPress filesystem' );
  }
}

function megamaxmenu_booster_prefix_generate_main_stylesheet( $prefix , $output_folder ){

  global $wp_filesystem;

  //The file we'll write out
  $file = trailingslashit( $output_folder ) . 'megamaxmenu.prefix.css';

  //The prefix source file to replace with custom prefix
  $source = trailingslashit( MEGAMAXMENU_DIR ) . 'pro/assets/css/megamaxmenu.prefix.css';

  //Check if the source file exists
  if( $wp_filesystem->exists( $source ) ){

    //Read the content from the source file
    $content = $wp_filesystem->get_contents( $source );

    //Replace all instances of PREFIX with the custom prefix
    $content = str_replace( 'PREFIX' , $prefix , $content );

    //Write the generated file
    $wp_filesystem->put_contents( $file , $content , FS_CHMOD_FILE );
  }
  else{
    //Source file is missing
    //echo 'missing source';
  }

}

function megamaxmenu_booster_prefix_generate_skins( $prefix , $output_folder ){

  global $wp_filesystem;

  //Look for all skin packs, including the core skins dir
  $skin_packs = array(
    'core'  => array(
      'dir' => MEGAMAXMENU_DIR,
      'prefix_dir' => trailingslashit( MEGAMAXMENU_DIR ) . 'pro/assets/css/skins/prefix/',
    )
  );
  $skin_packs = apply_filters( 'megamaxmenu_skin_packs' , $skin_packs );
//megamaxp( $skin_packs , 3 );

  //For each set of skins (core, Flat Skins, etc), take the source prefix files and generate the prefixed files in the uploads directory
  foreach( $skin_packs as $pack ){

    $prefix_dir = $pack['prefix_dir'];

    //Just write all the skins
    $skins = $wp_filesystem->dirlist( $prefix_dir , false );
    //megamaxp( $skins , 3 );

    foreach( $skins as $filename => $data ){
      //$data
      // name 'aqua.css'
      // perms 'urw-r--r--'
      // permsn '0644'
      // number false
      // owner 'chris'
      // group 'staff'
      // size 7080
      // lastmodunix 1500160157
      // lastmod Jul 15
      // time '11:09:17'
      // type 'f'

      //Ignore everything except .css files
      if( '.css' === substr( $filename , -4 ) ){
        //echo $filename;
        //echo 'of: ' . $output_folder . '<br/>';
        megamaxmenu_booster_prefix_generate_skin( $filename , $prefix , $output_folder , $prefix_dir );
      }

    }
  }

}

function megamaxmenu_booster_prefix_generate_skin( $filename , $prefix , $output_folder , $prefix_src_dir = '' ){

  global $wp_filesystem;

  if( !$prefix_src_dir ) $prefix_src_dir = trailingslashit( MEGAMAXMENU_DIR ) . 'pro/assets/css/skins/prefix/';
  $prefix_src_dir = trailingslashit( $prefix_src_dir );

  //Change to match skin names aqua.prefix.css => aqua.css
  $output_filename = str_replace( '.prefix.css' , '.css' , $filename );

  //The file we'll write out to /uploads
  $file = trailingslashit( $output_folder ) . $output_filename;

  //The prefix source file to replace with custom prefix
  $source = $prefix_src_dir . $filename;
//echo $source . ' :: ' . $file . '<br/>';
  //Check if the source file exists
  if( $wp_filesystem->exists( $source ) ){

    //Read the content from the source file
    $content = $wp_filesystem->get_contents( $source );

    //Replace all instances of PREFIX with the custom prefix
    $content = str_replace( 'PREFIX' , $prefix , $content );
//echo $content;
    //Write the generated file
    $wp_filesystem->put_contents( $file , $content , FS_CHMOD_FILE );
  }
  else{
    //Source file is missing
    //echo 'missing source';
  }


}


function megamaxmenu_connect_fs( $redirect_url, $method, $context, $fields = null ){

  global $wp_filesystem;

  if( false === ( $credentials = request_filesystem_credentials( $redirect_url, $method, false, $context, $fields ) ) ){
    return false;
  }

  //check if credentials are correct or not.
  if( !WP_Filesystem( $credentials ) ){
    request_filesystem_credentials( $redirect_url, $method, true, $context );
    return false;
  }

  return true;
}
