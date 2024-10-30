<?php

require_once 'MegaMaxMenuItemMenuSegment.class.php';
require_once 'MegaMaxMenuItemTabs.class.php';
require_once 'MegaMaxMenuItemCustom.class.php';
require_once 'MegaMaxMenuItemWidgetArea.class.php';
require_once 'MegaMaxMenuItemDynamic.class.php';
require_once 'MegaMaxMenuItemDynamicTerms.class.php';
require_once 'MegaMaxMenuItemDynamicPosts.class.php';

add_filter( 'megamaxmenu_item_object_class' , 'megamaxmenu_pro_item_object_class' , 20 , 4 );

function megamaxmenu_pro_item_object_class( $class , $item , $id , $auto_child = ''  ){

	$megamaxmenu_custom_type = '';

	if( $item->custom_type ){
		$megamaxmenu_custom_type = $item->custom_type;
	}
	else{
		$megamaxmenu_custom_type = get_post_meta( $item->db_id , '_megamaxmenu_custom_item_type' , true );
	}

	switch( $megamaxmenu_custom_type ){

		case 'menu_segment':
			$class = 'MegaMaxMenuItemMenuSegment';
			break;

		case 'widget_area':
			$class = 'MegaMaxMenuItemWidgetArea';
			break;

		case 'custom_content':
			$class = 'MegaMaxMenuItemCustom';
			break;

		case 'tabs':
			$class = 'MegaMaxMenuItemTabs';
			break;

		case 'toggle_group':
			$class = 'MegaMaxMenuItemToggleGroup';
			break;

		case 'toggle_content_panel':
			$class = 'MegaMaxMenuItemToggleContentPanel';
			break;

		case 'toggle_content_panels_group':
			$class = 'MegaMaxMenuItemToggleContentPanelsGroup';
			break;

		case 'dynamic_terms':
			$class = 'MegaMaxMenuItemDynamicTerms';
			break;

		case 'dynamic_term_item':
			$class = 'MegaMaxMenuItemDynamicTerm';
			break;

		case 'dynamic_posts':
			$class = 'MegaMaxMenuItemDynamicPosts';
			break;

		case 'dynamic_post_item':
			$class = 'MegaMaxMenuItemDynamicPost';
			break;

		default:

			switch( $auto_child ){

				case 'tab':
					//$class = 'MegaMaxMenuItemToggle';
					$class = 'MegaMaxMenuItemTab';
					break;

				default:
					//don't touch, leave as is
			}
			break;
	}

	return $class;
}