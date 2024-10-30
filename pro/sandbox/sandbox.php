<?php
function megamaxmenu_sandbox_url(){
  return site_url( '?megamaxmenu-sandbox=_' );
}
function megamaxmenu_sandbox_rewrites() {
    add_rewrite_rule( '^megamaxmenu-sandbox/?', 'index.php?megamaxmenu-sandbox=main', 'top' );
}
add_action( 'init', 'megamaxmenu_sandbox_rewrites' );

function megamaxmenu_sandbox_query_vars( $vars ) {
  $vars[] = 'megamaxmenu-sandbox';
  return $vars;
}
add_filter( 'query_vars', 'megamaxmenu_sandbox_query_vars' );

function megamaxmenu_sandbox_standalone_path( $wp_query ) {

  if( !function_exists( 'wp_get_current_user' ) ) return; // something is calling this too early

  //Admins only
  if( !current_user_can( 'manage_options' ) ){
    return;
  }


  if ( $wp_query->is_main_query() && get_query_var( 'megamaxmenu-sandbox', false ) ) {

    //Gotta clear out the custom prefix and re-register the non-prefixed styles if there is a prefix set
    if( megamaxmenu_op( 'custom_prefix' , 'general' ) ){
      add_filter( 'megamaxmenu_op' , 'megamaxmenu_sandbox_eliminate_prefix' , 10 , 3 );
      add_action( 'wp_head' , 'megamaxmenu_sandbox_eliminate_prefix_customizer_styles' );
      _MEGAMAXMENU()->deregister_skins();
      megamaxmenu_register_skins();
      megamaxmenu_register_skins_pro();
      if( function_exists( 'megamaxmenu_skins_flat_register_megamaxmenu_skins' ) ){
        megamaxmenu_skins_flat_register_megamaxmenu_skins(); //TODO make this more generic to work with any skins pack
      }
    }

    megamaxmenu_sandbox_load_assets();

    add_action('wp_print_scripts', 'megamaxmenu_sandbox_remove_all_scripts', 999 );
    add_action('wp_print_styles', 'megamaxmenu_sandbox_remove_all_styles', 999);

    megamaxmenu_sandbox_interface();

  }
}
add_action( 'parse_query', 'megamaxmenu_sandbox_standalone_path' );


function megamaxmenu_sandbox_interface(){

  $config = get_query_var( 'megamaxmenu-sandbox' );
  //stylesheet

  //script

?>
<!doctype html>
<html>
  <head>
    <title>MegaMaxMenu Sandbox Viewer (Alpha)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php

    wp_head();
    ?>
  </head>
  <body>
    <div class="container">
      <h1>MegaMaxMenu Sandbox</h1>

      <form id="megamaxmenu-sandbox-preview-form">

        <div class="ums-form-group ums-form-group-assign">
          <label class="ums-form-group-label">Assign menu by</label>
          <label><input type="radio" name="assignment" value="menu" /> Menu</label>
          <label><input type="radio" name="assignment" value="theme_location" checked /> Theme Location</label>
        </div>

        <div class="ums-form-group  ums-form-group-menu ums-form-group-disabled">
          <label class="ums-form-group-label">Menu</label>
          <select>
            <?php
            $menus = get_terms('nav_menu');
            foreach( $menus as $menu ){
              echo "<option value='$menu->term_id'>$menu->name</option>";
            }
            ?>
          </select>
        </div>

        <div class="ums-form-group ums-form-group-theme_loc">
          <label class="ums-form-group-label">Theme Location</label>
          <?php
          $menus = get_registered_nav_menus();
          if( empty( $menus ) ){
            echo 'no registerd theme locations';
          }
          else{
            ?>
            <select>
              <?php
              foreach ( $menus as $location => $description ) {
                echo "<option value='$location'>$description [$location]</option>";
              }
              ?>
            </select>
            <?php
          }
          ?>
        </div>

        <div class="ums-form-group ums-form-group-config">
          <label class="ums-form-group-label">MegaMaxMenu Configuration</label>
          <select>
            <option value="main">Main MegaMaxMenu Configuration</option>
            <?php
              $configs = megamaxmenu_get_menu_instances(false);
              foreach( $configs as $_config ){
                ?>
                  <option value="<?php echo $_config; ?>">+<?php echo $_config; ?></option>
                <?php
              }
              ?>
          </select>
        </div>

        <input type="hidden" name="megamaxmenu-sandbox" value="main" />

        <div class="ums-form-group ums-form-group-config">
          <label class="ums-form-group-label">View result</label>
          <button>View Menu</button>
        </div>

      </form>

      <div id="megamaxmenu-sandbox-menu-preview">
        <div class="ums-hint">Select options above to load a menu preview</div>
        <?php
          //megamaxmenu( $config , array( 'theme_location' => 'primary' ) );
        ?>
      </div>

    </div>

    <div class="loading">
      <div class="sk-folding-cube">
        <div class="sk-cube1 sk-cube"></div>
        <div class="sk-cube2 sk-cube"></div>
        <div class="sk-cube4 sk-cube"></div>
        <div class="sk-cube3 sk-cube"></div>
      </div>
    </div>

  <?php

    //Google Maps API
    $gmaps_uri = '//maps.googleapis.com/maps/api/js';

    $query_params = array();

    //API Key
    $api_key = trim( megamaxmenu_op( 'google_maps_api_key' , 'general' ) );
    if( $api_key ){
      $query_params['key'] = $api_key;
    }

    //Language
    $language = trim( megamaxmenu_op( 'google_maps_language' , 'general' ) );
    if( $language ){
      $query_params['language'] = $language;
    }

    //Region
    $region = trim( megamaxmenu_op( 'google_maps_region' , 'general' ) );
    if( $region ){
      $query_params['region'] = $region;
    }

    $query = http_build_query( $query_params );

    $gmaps_uri.= '?'.$query;

    wp_enqueue_script( 'google-maps', $gmaps_uri , array( 'jquery' ), null , true );

    wp_print_footer_scripts();

  ?>

  </body>
  </html>

<?php
exit;
}


function megamaxmenu_sandbox_load_assets(){

  // megamaxmenu_load_assets();
  // megamaxmenu_pro_load_assets();


  wp_enqueue_style( 'megamaxmenu-sandbox' , MEGAMAXMENU_URL . '/pro/sandbox/sandbox.css' );
  wp_enqueue_script( 'megamaxmenu-sandbox' , MEGAMAXMENU_URL . '/pro/sandbox/sandbox.js' , array( 'jquery' ) , MEGAMAXMENU_VERSION, true );
  wp_localize_script( 'megamaxmenu-sandbox', 'megamaxmenu_sandbox_ajax', array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'security' => wp_create_nonce( 'megamaxmenu-sandbox' ),
    )
  );


}

/*
 * Remove all JS that isn't MegaMaxMenu
 */
function megamaxmenu_sandbox_remove_all_scripts() {
    global $wp_scripts;
    //megamaxp($wp_scripts->queue);
    foreach( $wp_scripts->queue as $i => $script ){
      //if( $script == 'google-maps' ) continue;
      if( strpos( $script , 'megamaxmenu' ) !== 0 ){
        unset( $wp_scripts->queue[$i] );
        //echo $script."<br/>";
      }
    }
    //megamaxp($wp_scripts->queue);
    //$wp_scripts->queue = array();
}


/*
 * Remove all CSS that isn't MegaMaxMenu
 */
function megamaxmenu_sandbox_remove_all_styles() {
    global $wp_styles;
    //megamaxp($wp_styles->queue);
    foreach( $wp_styles->queue as $i => $sheet ){
      if( strpos( $sheet , 'megamaxmenu' ) !== 0 ){
        unset( $wp_styles->queue[$i] );
      }
    }
    //megamaxp($wp_styles->queue);
    //$wp_styles->queue = array();

}

function megamaxmenu_sandbox_eliminate_prefix( $val , $option , $section ){
  if( $section == 'general' && $option == 'custom_prefix' ){
    $val = '';
  }
  return $val;
}

function megamaxmenu_sandbox_eliminate_prefix_customizer_styles(){
  echo '<style>'.megamaxmenu_generate_custom_styles().'</style>';
}



//AJAX

add_action( 'wp_ajax_megamaxmenu_sandbox_preview', 'megamaxmenu_sandbox_preview' );

function megamaxmenu_sandbox_preview() {

  check_ajax_referer( 'megamaxmenu-sandbox' , 'security' );

  $assign = isset( $_POST['assign'] ) ? sanitize_text_field( $_POST['assign'] ) : '';
  $menu = isset( $_POST['menu'] ) ? sanitize_text_field( $_POST['menu'] ) : '';
  $theme_loc = isset( $_POST['theme_location'] ) ? sanitize_text_field( $_POST['theme_location'] ) : '';
  $config = isset( $_POST['config'] ) ? sanitize_text_field( $_POST['config'] ) : '';

  $args = array();
  switch( $assign ){
    case 'menu' :
      $args['menu'] = $menu;
      break;
    case 'theme_location':
      $args['theme_location'] = $theme_loc;
      break;

  }
  megamaxmenu( $config , $args );

	wp_die(); // this is required to terminate immediately and return a proper response
}
