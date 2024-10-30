<?php

add_action( 'wp_enqueue_scripts' , 'megamaxmenu_load_fonts' );
function megamaxmenu_load_fonts(){

	$menus = megamaxmenu_get_menu_instances( true );
	foreach( $menus as $menu ){
		$google_font = megamaxmenu_op( 'google_font' , $menu );
		if( $google_font ){
			//echo $google_font.'<br/>';
			megamaxmenu_load_font( $google_font );
		}
	}

}

/** CUSTOMIZATION FUNCTIONS **/
function megamaxmenu_get_menu_style_google_font( $field , $menu_id , &$menu_styles ){

	$font_id = megamaxmenu_op( $field['name'] , $menu_id );
	if( $font_id ){
		$stack = megamaxmenu_get_font_stack( $font_id );
		$selector = ".megamaxmenu-$menu_id, .megamaxmenu-$menu_id .megamaxmenu-target, .megamaxmenu-$menu_id .megamaxmenu-nav .megamaxmenu-item-level-0 .megamaxmenu-target, .megamaxmenu-$menu_id div, .megamaxmenu-$menu_id p, .megamaxmenu-$menu_id input";
		$menu_styles[$selector]['font-family'] = $stack;

		$style = megamaxmenu_op( 'google_font_style' , $menu_id );
		$props = megamaxmenu_get_font_style_props( $style );
		if( is_array( $props ) ){
			foreach( $props as $prop => $val ){
				$menu_styles[$selector][$prop] = $val;
			}
		}
	}
}
function megamaxmenu_get_menu_style_custom_font( $field , $menu_id , &$menu_styles ){

	$font_value = megamaxmenu_op( $field['name'] , $menu_id );
	if( $font_value ){

		$selector = ".megamaxmenu-$menu_id, .megamaxmenu-$menu_id .megamaxmenu-target, .megamaxmenu-$menu_id .megamaxmenu-nav .megamaxmenu-item-level-0 .megamaxmenu-target";
		$menu_styles[$selector]['font'] = $font_value;
	}
}

function megamaxmenu_get_menu_style_custom_font_family( $field , $menu_id , &$menu_styles ){

	$font_value = megamaxmenu_op( $field['name'] , $menu_id );
	if( $font_value ){

		$selector = ".megamaxmenu-responsive-toggle-$menu_id, .megamaxmenu-$menu_id, .megamaxmenu-$menu_id .megamaxmenu-target, .megamaxmenu-$menu_id .megamaxmenu-nav .megamaxmenu-item-level-0 .megamaxmenu-target";
		$menu_styles[$selector]['font-family'] = $font_value;
	}
}


function megamaxmenu_get_font_ops(){

	$fonts = megamaxmenu_get_registered_fonts();
	ksort( $fonts );

	$font_select = array( '' => 'None' );
	foreach( $fonts as $font_id => $font_ops ){
		$font_select[$font_id] = $font_ops['label'];
	}
	//up( $font_select , 2 );
	return $font_select;
}

function megamaxmenu_get_font_stack( $font_id ){
	$fonts = megamaxmenu_get_registered_fonts();
	if( isset( $fonts[$font_id] ) ){
		if( isset( $fonts[$font_id]['stack'] ) ){
			return $fonts[$font_id]['stack'];
		}
	}
}




function megamaxmenu_init_fonts(){
	add_action( 'megamaxmenu_register_fonts' , 'megamaxmenu_register_default_fonts' );
	do_action( 'megamaxmenu_register_fonts' );
}
add_action( 'init' , 'megamaxmenu_init_fonts' , 8 ); //Run before megamaxmenu init

function megamaxmenu_get_registered_fonts(){
	$fonts = _MEGAMAXMENU()->get_registered_fonts();
	$fonts = apply_filters( 'megamaxmenu_registered_fonts' , $fonts );

	return $fonts;
}

function megamaxmenu_register_font( $font_id , $font_ops ){
	_MEGAMAXMENU()->register_font( $font_id , $font_ops );
}



function megamaxmenu_load_font( $font_id , $style = '' ){

	$fonts = megamaxmenu_get_registered_fonts();
	if( isset( $fonts[$font_id] ) ){
		extract( $fonts[$font_id] );

		if( $style != '' ){
			$style = ':'.$style;
		}
		else{
			$styles = megamaxmenu_get_font_styles();

			//foreach( $styles as $style_id => $style_name ){}
			if( is_array( $styles ) ){
				$style.= ':';
				$style.= implode( ',' , array_keys( $styles ) );
			}
		}

		$src = '//fonts.googleapis.com/css?family='.$family.$style;

		wp_enqueue_style( 'megamaxmenu-'.$font_id , $src , false ); //, 'um-'.MEGAMAXMENU_VERSION );
	}
}

function megamaxmenu_get_font_style_ops(){
	$styles = megamaxmenu_get_font_styles();
	$style_ops = array();
	foreach( $styles as $id => $_style ){
		$style_ops[$id] = $_style['label'];
	}
	return  $style_ops;
}
function megamaxmenu_get_font_styles(){

	$styles = array(

		'' 		=> array(
			'label'	=> __( 'Default', 'megamaxmenu' ),
			'props'	=> '',
		),
		'300'	=> array(
			'label'	=> __( 'Light', 'megamaxmenu' ),
			'props'	=> array(
							'font-weight' => '300'
						),
			),
		'400'	=> array(
			'label'	=> __( 'Normal', 'megamaxmenu' ),
			'props'	=> array(
							'font-weight' => '400'
						),
			),
		'700'	=> array(
			'label'	=> __( 'Bold', 'megamaxmenu' ),
			'props'	=> array(
							'font-weight' => '700'
						),
			),
	);

	return apply_filters( 'megamaxmenu_google_font_styles' , $styles );
}
function megamaxmenu_get_font_style_props( $style_id ){
	$styles = megamaxmenu_get_font_styles();
	if( isset( $styles[$style_id] ) ){
		return $styles[$style_id]['props'];
	}
	return false;
}

function megamaxmenu_register_default_fonts(){

	megamaxmenu_register_font( 'open-sans' , array(
			'label'		=> 'Open Sans',
			'family'	=> 'Open+Sans',
			'stack'		=> "'Open Sans', sans-serif",
		)
	);


	megamaxmenu_register_font( 'roboto' , array(
			'label'		=> 'Roboto',
			'family'	=> 'Roboto',
			'stack'		=> "'Roboto', sans-serif",
		)
	);


	megamaxmenu_register_font( 'oswald' , array(
			'label'		=> 'Oswald',
			'family'	=> 'Oswald',
			'stack'		=> "'Oswald', sans-serif",
		)
	);

	megamaxmenu_register_font( 'lato' , array(
			'label'		=> 'Lato',
			'family'	=> 'Lato',
			'stack'		=> "'Lato', sans-serif",
		)
	);

	megamaxmenu_register_font( 'droid-sans' , array(
			'label'		=> 'Droid Sans (no light)',
			'family'	=> 'Droid+Sans',
			'stack'		=> "'Droid Sans', sans-serif",
		)
	);

	megamaxmenu_register_font( 'open-sans-condensed' , array(
			'label'		=> 'Open Sans Condensed (Light or Bold only)',
			'family'	=> 'Open+Sans+Condensed',
			'stack'		=> "'Open Sans Condensed', sans-serif",
		)
	);

	megamaxmenu_register_font( 'pt-sans' , array(
			'label'		=> 'PT Sans (Normal or Bold only)',
			'family'	=> 'PT+Sans',
			'stack'		=> "'PT Sans', sans-serif",
		)
	);

	megamaxmenu_register_font( 'source-sans-pro' , array(
			'label'		=> 'Source Sans Pro',
			'family'	=> 'Source+Sans+Pro',
			'stack'		=> "'Source Sans Pro', sans-serif",
		)
	);

	megamaxmenu_register_font( 'roboto-condensed' , array(
			'label'		=> 'Roboto Condensed',
			'family'	=> 'Roboto+Condensed',
			'stack'		=> "'Roboto Condensed', sans-serif",
		)
	);

	megamaxmenu_register_font( 'droid-serif' , array(
			'label'		=> 'Droid Serif (Normal or Bold only)',
			'family'	=> 'Droid+Serif',
			'stack'		=> "'Droid Serif', serif",
		)
	);

	megamaxmenu_register_font( 'ubuntu' , array(
			'label'		=> 'Ubuntu',
			'family'	=> 'Ubuntu',
			'stack'		=> "'Ubuntu', serif",
		)
	);

	megamaxmenu_register_font( 'montserrat' , array(
			'label'		=> 'Montserrat (Normal or Bold only)',
			'family'	=> 'Montserrat',
			'stack'		=> "'Montserrat', sans-serif",
		)
	);

	megamaxmenu_register_font( 'raleway' , array(
			'label'		=> 'Raleway',
			'family'	=> 'Raleway',
			'stack'		=> "'Raleway', sans-serif",
		)
	);

	megamaxmenu_register_font( 'lora' , array(
			'label'		=> 'Lora',
			'family'	=> 'Lora',
			'stack'		=> "'Lora', serif",
		)
	);

	megamaxmenu_register_font( 'dosis' , array(
			'label'		=> 'Dosis',
			'family'	=> 'Dosis',
			'stack'		=> "'Dosis', sans-serif",
		)
	);

	megamaxmenu_register_font( 'abel' , array(
			'label'		=> 'Abel (Normal only)',
			'family'	=> 'Abel',
			'stack'		=> "'Abel', sans-serif",
		)
	);

	megamaxmenu_register_font( 'arvo' , array(
			'label'		=> 'Arvo (Normal or Bold only)',
			'family'	=> 'Arvo',
			'stack'		=> "'Arvo', serif",
		)
	);

	megamaxmenu_register_font( 'arimo' , array(
			'label'		=> 'Arimo (Normal or Bold only)',
			'family'	=> 'Arimo',
			'stack'		=> "'Arimo', sans-serif",
		)
	);

	megamaxmenu_register_font( 'bitter' , array(
			'label'		=> 'Bitter (Normal or Bold only)',
			'family'	=> 'Bitter',
			'stack'		=> "'Bitter', serif",
		)
	);

	megamaxmenu_register_font( 'lobster' , array(
			'label'		=> 'Lobster (Normal only)',
			'family'	=> 'Lobster',
			'stack'		=> "'Lobster', cursive",
		)
	);


	megamaxmenu_register_font( 'shadows_into_light' , array(
			'label'		=> 'Shadows Into Light (Normal only)',
			'family'	=> 'Shadows+Into+Light',
			'stack'		=> "'Shadows Into Light', cursive",
		)
	);

	megamaxmenu_register_font( 'rokkitt' , array(
			'label'		=> 'Rokkitt (Normal or Bold only)',
			'family'	=> 'Rokkitt',
			'stack'		=> "'Rokkitt', serif",
		)
	);

	megamaxmenu_register_font( 'roboto_slab' , array(
			'label'		=> 'Roboto Slab',
			'family'	=> 'Roboto+Slab',
			'stack'		=> "'Roboto Slab', serif",
		)
	);

	megamaxmenu_register_font( 'merriweather' , array(
			'label'		=> 'Merriweather',
			'family'	=> 'Merriweather',
			'stack'		=> "'Merriweather', serif",
		)
	);

	megamaxmenu_register_font( 'indie_flower' , array(
			'label'		=> 'Indie Flower (Normal only)',
			'family'	=> 'Indie+Flower',
			'stack'		=> "'Indie Flower', cursive",
		)
	);

	megamaxmenu_register_font( 'inconsolata' , array(
			'label'		=> 'Inconsolata (Normal or Bold only)',
			'family'	=> 'Inconsolata',
			'stack'		=> "'Inconsolata', monospace",
		)
	);

	megamaxmenu_register_font( 'play' , array(
			'label'		=> 'Play (Normal or Bold only)',
			'family'	=> 'Play',
			'stack'		=> "'Play', sans-serif",
		)
	);

	megamaxmenu_register_font( 'archivo_narrow' , array(
			'label'		=> 'Archivo Narrow (Normal or Bold only)',
			'family'	=> 'Archivo+Narrow',
			'stack'		=> "'Archivo Narrow', sans-serif",
		)
	);

	megamaxmenu_register_font( 'pacifico' , array(
			'label'		=> 'Pacifico (Normal only)',
			'family'	=> 'Pacifico',
			'stack'		=> "'Pacifico', cursive",
		)
	);

	megamaxmenu_register_font( 'bree_serif' , array(
			'label'		=> 'Bree Serif (Normal only)',
			'family'	=> 'Bree+Serif',
			'stack'		=> "'Bree Serif', sans-serif",
		)
	);



	//3.3
	megamaxmenu_register_font( 'slabo' , array(
			'label'		=> 'Slabo (Normal only)',
			'family'	=> 'Slabo+27px',
			'stack'		=> "'Bree 27px', serif",
		)
	);

	megamaxmenu_register_font( 'titillium_web' , array(
			'label'		=> 'Titillium Web',
			'family'	=> 'Titillium+Web',
			'stack'		=> "'Titillium Web', sans-serif",
		)
	);

	megamaxmenu_register_font( 'oxygen' , array(
			'label'		=> 'Oxygen',
			'family'	=> 'Oxygen',
			'stack'		=> "'Oxygen', sans-serif",
		)
	);

	megamaxmenu_register_font( 'noto_sans' , array(
			'label'		=> 'Noto Sans (no lite)',
			'family'	=> 'Noto+Sans',
			'stack'		=> "'Noto Sans', sans-serif",
		)
	);

	megamaxmenu_register_font( 'cabin' , array(
			'label'		=> 'Cabin (no lite)',
			'family'	=> 'Cabin',
			'stack'		=> "'Cabin', sans-serif",
		)
	);

	megamaxmenu_register_font( 'vollkorn' , array(
			'label'		=> 'Vollkorn (no lite)',
			'family'	=> 'Vollkorn',
			'stack'		=> "'Vollkorn', serif",
		)
	);




}
