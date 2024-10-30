<?php


add_filter( 'wp_setup_nav_menu_item' , 'megamaxmenu_setup_nav_menu_item' );
function megamaxmenu_setup_nav_menu_item( $menu_item ){


	if( $menu_item->type == 'custom' ){

		//Check flag FIRST, only deal with URL if flag hasn't been set

		$custom_item_type = '';
		$items = megamaxmenu_get_custom_menu_item_types();
		$url = $menu_item->url;
		$megamax_prefix = '#megamaxmenu-';

		//When item is added to menu, set flag
		if( isset( $menu_item->post_status ) && $menu_item->post_status == 'draft' ){
			if( strpos( $url , $megamax_prefix ) === 0 ){

				$custom_item_type = substr( $url , strlen( $megamax_prefix ) );
				update_post_meta( $menu_item->ID , '_megamaxmenu_custom_item_type' , $custom_item_type );

			}
		}
		//Not new, check meta
		else{
			$custom_item_type = get_post_meta( $menu_item->ID , '_megamaxmenu_custom_item_type' , true );
		}


		if( $custom_item_type ){
			$menu_item->object = 'megamaxmenu-custom';	//perhaps if is_admin() only
			$label = $custom_item_type.' Undefined';
			if( isset( $items[$custom_item_type] ) ){
				$label = $items[$custom_item_type]['label'];
			}
			$menu_item->type_label = '[MegaMaxMenu ' . $label . ']';
		}

	}

	return $menu_item;
}

function megamaxmenu_get_custom_menu_item_types(){

	$items = array(

		'row'	=> array(
			'label'	=>	__( 'Row' , 'megamaxmenu' ),
			'title' =>	'['.__( 'Row' , 'megamaxmenu' ) . ']',
			'panels'	=> array( 'row' , 'responsive' ),
			'desc'	=>	__( 'A row which can wrap any type of item.  Useful for creating divisions and centering submenu contents.' , 'megamaxmenu' ),
		),

		'column' => array(
			'label'	=>	__( 'Column' , 'megamaxmenu' ),
			'title' =>	'['.__( 'Column' , 'megamaxmenu' ) . ']',
			'panels'	=> array( 'column_layout' , 'responsive' , 'customize_column'),
			'desc'	=>	__( 'A column, which can contain any type of item.  Useful for placing multiple blocks of items in a single column.' , 'megamaxmenu' ),
		),

		'divider'	=> array(
			'label'	=> 	__( 'Horizontal Divider' , 'megamaxmenu' ),
			'title'	=>	'['.__( 'Divider' , 'megamaxmenu' ).']',
			'panels'=> array( 'divider' ),
			'desc'	=>	__( 'A visual divider between submenu segments.' , 'megamaxmenu' )
		),

	);
	return apply_filters( 'megamaxmenu_custom_menu_item_types' , $items );
}

function megamaxmenu_get_menu_item_panels_map(){
	$map = array(

		'default'	=> array(
			'general',
			'icon',
			'image',
			'layout',
			'responsive',
			'submenu',
			'custom_content',
			'widgets',
			'customize',
			'deprecated',
		)

	);
	$items = megamaxmenu_get_custom_menu_item_types();
	foreach( $items as $id => $item ){
		if( isset( $item['panels'] ) ){
			$map['[MegaMaxMenu ' . $item['label'] . ']' ] = $item['panels'];
		}
	}

	$map = apply_filters( 'megamaxmenu_menu_item_settings_panels_map' , $map );

	return $map;
}


add_action( 'admin_init' , 'megamaxmenu_add_custom_menu_items_meta_box' );
function megamaxmenu_add_custom_menu_items_meta_box(){
	add_meta_box( 'megamaxmenu_custom_nav_items', 'MegaMaxMenu Advanced Items', 'megamaxmenu_custom_menu_items_meta_box', 'nav-menus', 'side', 'low' );
}

function megamaxmenu_custom_menu_items_meta_box() {
	global $_nav_menu_placeholder, $nav_menu_selected_id;

	$items = megamaxmenu_get_custom_menu_item_types();

	?>
	<div id="megamaxmenu-custom-menu-metabox" class="posttypediv">
		<div id="tabs-panel-megamaxmenu-custom" class="tabs-panel tabs-panel-active">
			<ul id ="megamaxmenu-custom-checklist" class="categorychecklist form-no-clear">

			<?php foreach( $items as $id => $item ):
				$url = '#megamaxmenu-'.$id;
				if( isset( $item['url'] ) ){
					$url = $item['url'];
				}

				?>

				<li>
					<label class="menu-item-title um-tooltip-wrap">
						<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder ?>][menu-item-label]" value="0"> <?php echo $item['label']; ?>
						<span class="um-tooltip"><?php echo $item['desc']; ?></span>
					</label>
					<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder ?>][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-megamaxmenu-custom" name="menu-item[<?php echo $_nav_menu_placeholder ?>][menu-item-megamaxmenu-custom]" value="on">
					<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder ?>][menu-item-title]" value="<?php echo $item['title']; ?>">
					<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder ?>][menu-item-url]" value="<?php echo $url; ?>">
				</li>

			<?php endforeach; ?>

			</ul>
		</div>
		<p class="button-controls">

			<span class="add-to-menu">
				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-megamaxmenu-custom-menu-item" id="submit-megamaxmenu-custom-menu-metabox">
				<span class="spinner"></span>
			</span>
		</p>
	</div>
	<?php
}
