<?php

class MegaMaxMenuItemRow extends MegaMaxMenuItem{

	protected $type = 'row';

	function init(){
		if( $this->depth == 0 ){
			$this->walker->set_ignore( $this->item->ID );
			return '<!-- Rows can only be used in submenus-->';
		}
	}


	function get_start_el(){

		$classes = array();

		//Rob submenus should be like if they were in a mega sub
		$this->settings['submenu_type_calc'] = 'mega';

		//If the submenu column default is auto, inherit from parent
		if( $this->getSetting( 'submenu_column_default' ) == 'auto' ){
			$this->settings['submenu_column_default'] = $this->walker->parent_item()->getSetting( 'submenu_column_default' );
		}


		//Autoclear
		$autoclear = '';

		//Dummy inherits
		if( $this->is_dummy ){	//if this is a dummy row, check with the parent
			if( $this->walker->parent_item()->getSetting( 'submenu_column_autoclear' ) == 'on' ){
				$autoclear = 'megamaxmenu-autoclear';
			}
		}
		//Row setting
		else{
			if( $this->getSetting( 'submenu_column_autoclear' ) == 'on' ){
				$autoclear = 'megamaxmenu-autoclear';
			}
		}


		//Row content alignment
		$content_align = $this->getSetting( 'row_content_align' );
		if( $content_align && $content_align != 'default' ){
			$classes[] = 'megamaxmenu-row-content-align-' . $content_align;
		}


		if( $this->getSetting( 'grid_row' ) == 'on' ){
			$classes[] = 'megamaxmenu-grid-row';
		}

		//Custom menu item class
		//Add all the classes in the array prior to the 'menu-item' class
		if( isset( $this->item->classes[0] ) && $this->item->classes[0] ){
			$k = 0;
			while( $this->item->classes[$k] !== 'menu-item' && $k < count( $this->item->classes ) ){
				$classes[] = $this->item->classes[$k];
				$k++;
			}
		}

		$classes = implode( ' ' , $classes );

		return '<ul class="megamaxmenu-row megamaxmenu-row-id-'.$this->item->ID.' ' . $autoclear .' '. $classes.'">';
	}
	function get_end_el(){
		$item_output = "</ul>"; //<!-- end row ".$this->item->ID."-->\n";
		return $item_output;
	}

	function get_submenu_wrap_start(){
		return '';
	}
	function get_submenu_wrap_end(){
		return '';
	}
}
