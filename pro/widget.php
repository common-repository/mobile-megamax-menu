<?php
/**
 * MegaMaxMenu Navigation Widget
 *
 */

class MegaMaxMenu_Widget extends WP_Widget {

    protected $widget_slug = 'megamaxmenu_navigation_widget';

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// TODO: update description
		parent::__construct(
			$this->get_widget_slug(),
			__( 'MegaMaxMenu Navigation', 'megamaxmenu' ),
			array(
				'classname'  => $this->get_widget_slug().'-class',
				'description' => __( 'Place an MegaMaxMenu in a Widget Area.  Do NOT place in an [MegaMaxMenu] Widget Area.', 'megamaxmenu' )
			)
		);

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

	} // end constructor


    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		extract( wp_parse_args(
			(array) $instance , array(
				//'instance_id'	=> 'main',
				'config_id'		=> 'main',
				'theme_location'=> 0,
				'menu_id'		=> 0,
			)
		) );

		
		if( $theme_location ){
			wp_nav_menu( array( 'theme_location' => $theme_location ) );
		}
		else{
			if( $menu_id ){
				megamaxmenu( $config_id , array( 'menu' => $menu_id ) );
			}
			else{
				megamaxmenu( $config_id );
			}
		}

		echo $after_widget;

	} // end widget
	
	
	public function flush_widget_cache() 
	{
    	wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// TODO: Here is where you update your widget's old values with the new, incoming values

		$instance['config_id'] = isset( $new_instance['config_id'] ) ? $new_instance['config_id'] : 'main';
		$instance['theme_location'] = isset( $new_instance['theme_location'] ) ? $new_instance['theme_location'] : 0;
		$instance['menu_id'] = isset( $new_instance['menu_id'] ) ? $new_instance['menu_id'] : 0;

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		extract( wp_parse_args(
			(array) $instance , array(
				'config_id'	=> 'main',
				'theme_location'=> 0,
				'menu_id'		=> 0,
			)
		) );


		echo '<p>'.__( 'This widget inserts an MegaMaxMenu in one of two ways:' , 'megamaxmenu' ).'</p>';
		
		

		//Alternatively, you can set a Theme Location if you do not have a specific menu assigned to this instance, and that menu will be used.

		$instances = megamaxmenu_get_menu_instances( true );

		?>

		<hr/>

		<?php
		echo '<h4>'.__( '1. By Menu Configuration' , 'megamaxmenu' ).'</h4>';
		echo '<p>'.__( '(1) Set the MegaMaxMenu Configuration to use. The menu set for that Configuration\'s "Default Manual Integration Menu" will be used by default.  You can override the menu used by using the Menu setting below.' , 'megamaxmenu' ).'</p>';
		?>
		<p>
			<strong><label for="<?php echo $this->get_field_id('config_id'); ?>"><?php _e( 'MegaMaxMenu Configuration:' , 'megamaxmenu' ); ?></label></strong>
			<select id="<?php echo $this->get_field_id('config_id'); ?>" name="<?php echo $this->get_field_name('config_id'); ?>">
				<!--<option value="0"><?php _e( '[None - Use a Theme Location]' ) ?></option>-->
		<?php
			foreach ( $instances as $_config_id ) {
				echo '<option value="' . $_config_id . '"'
					. selected( $config_id, $_config_id, false )
					. '>'. $_config_id . '</option>';
			}
		?>
			</select>
		</p>
		<?php

		$menus = wp_get_nav_menus( array('orderby' => 'name') );

		?>
		<p>
			<strong><label for="<?php echo $this->get_field_id('menu_id'); ?>"><?php _e( 'Menu (optional):' , 'megamaxmenu' ); ?></label></strong>
			<select id="<?php echo $this->get_field_id('menu_id'); ?>" name="<?php echo $this->get_field_name('menu_id'); ?>">
				<option value="0"><?php _e( '[Automatic]' ) ?></option>
		<?php
			foreach( $menus as $menu ){
				echo '<option value="' . $menu->term_id . '"'
					. selected( $menu_id, $menu->term_id, false )
					. '>'. $menu->name . '</option>';
			}
		?>
			</select>
		</p>

		<hr />
		<?php
		echo '<h4>'.__( '2. By Theme Location' , 'megamaxmenu' ).'</h4>';
		echo '<p>'.__( 'Alternatively, set a Theme Location to just insert a standard menu that you can then activate as an MegaMaxMenu.' , 'megamaxmenu' ).'</p>';

		$theme_locs = get_registered_nav_menus();
		?>
		
		<p>
			<strong><label for="<?php echo $this->get_field_id('theme_location'); ?>"><?php _e( 'Theme Location:' , 'megamaxmenu' ); ?></label></strong>
			<select id="<?php echo $this->get_field_id('theme_location'); ?>" name="<?php echo $this->get_field_name('theme_location'); ?>">
				<option value="0"><?php _e( '[None]' ) ?></option>
		<?php
			foreach( $theme_locs as $loc_id => $loc_name ){
				//$loc_name
				echo '<option value="' . $loc_id . '"'
					. selected( $loc_id, $theme_location, false )
					. '>'. $loc_name . '</option>';
			}
		?>
			</select>
			<br/>
			<small>Setting the Theme Location will override the Menu Instance Settings</small>
		</p>


		<hr/>
		<?php
		

	} // end form

} // end class

function megamaxmenu_register_widget(){
	register_widget( 'MegaMaxMenu_Widget' );
}
add_action( 'widgets_init', 'megamaxmenu_register_widget' );