<?php

require_once 'MegaMaxMenuItem.class.php';
require_once 'MegaMaxMenu_dummy_item.class.php';
require_once 'MegaMaxMenuItemDefault.class.php';
require_once 'MegaMaxMenuItemRow.class.php';
require_once 'MegaMaxMenuItemColumn.class.php';


add_filter( 'megamaxmenu_item_object_class' , 'megamaxmenu_get_item_object_class' , 10 , 4 );
function megamaxmenu_get_item_object_class( $class , $item , $id , $auto_child = '' ){
	$megamaxmenu_custom_type = '';

	if( $item->custom_type ){
		$megamaxmenu_custom_type = $item->custom_type;
	}
	else{
		$megamaxmenu_custom_type = get_post_meta( $item->db_id , '_megamaxmenu_custom_item_type' , true );
	}

	switch( $megamaxmenu_custom_type ){
		case 'row':
			$class = 'MegaMaxMenuItemRow';
			break;

		case 'column';
			$class = 'MegaMaxMenuItemColumn';
			break;

		case 'divider':
			$class = 'MegaMaxMenuItemDivider';
			break;

		default:
			$class = 'MegaMaxMenuItemDefault';
			break;

	}

	return $class;
}