<?php

function megamaxmenu_widget_manager_panel(){

	$menu_item_widget_areas = get_option( MEGAMAXMENU_MENU_ITEM_WIDGET_AREAS , array() );
	
	if( isset( $_GET['widget_area_id'] ) ){
		$widget_area_ids = _MEGAMAXMENU()->sanitize_data($_GET['widget_area_id'], '');
		if( $widget_area_ids ){

			//megamaxp( $widget_area_ids );
			foreach( $widget_area_ids as $id ){
				if( isset( $menu_item_widget_areas[$id] ) ){
					add_settings_error( 'widget-manager' , 'deletion-notice' , 'Deleted Widget Area: '.$menu_item_widget_areas[$id] , 'updated' );
					unset( $menu_item_widget_areas[$id] );
				}
			}

			update_option( MEGAMAXMENU_MENU_ITEM_WIDGET_AREAS , $menu_item_widget_areas );

		}
	}


	?>
	<div class="wrap megamaxmenu-wrap">

		<?php settings_errors(); ?>
	
		<h2><strong>MegaMaxMenu</strong> Widget Manager <span class="megamaxmenu-version">v<?php echo MEGAMAXMENU_VERSION; ?></span></h2>

		<p>This screen allows you to remove orphaned custom widget areas.  Check the widget areas you wish to delete.</p>
		<p>Note that if you re-save a menu item with the Custom Widget Area Setting, the widget area will be regenerated</p>
		<p>To remove reusable widget areas, click the Widgets tab under General Settings</p>

		<?php

		if( count( $menu_item_widget_areas ) == 0 ){
			echo '<div class="error below-h2"><p>';
			echo __( '<strong>No Custom Widget Areas found</strong>', 'megamaxmenu' );
			echo '</p></div>';
		}

		?>
		<form>
			<input type="hidden" name="page" value="megamaxmenu-settings" />
			<input type="hidden" name="do" value="widget-manager" />
			<?php wp_nonce_field( 'megamaxmenu-control-panel-do' , 'megamaxmenu_nonce' ); ?>

		<?php
		foreach( $menu_item_widget_areas as $menu_item_id => $widget_title ){

			?>
			<div>
				<label><input name="widget_area_id[]" type="checkbox" value="<?php echo $menu_item_id; ?>" /> <strong><?php echo $widget_title.'</strong> [Menu Item '.$menu_item_id.']'; ?></label>
			</div>
			<?php
		}

		?>
			<br/>
			
			<input class="button button-primary" type="submit" value="Delete Widget Areas" />
		</form>
		<br/>
		<br/>
		
		<?php megamaxmenu_admin_back_to_settings_button(); ?>
 
	</div>
	<?php
}
