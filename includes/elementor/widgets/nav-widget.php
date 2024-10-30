<?php

class Elementor_MegaMaxMenu_Nav_Widget extends \Elementor\Widget_Base {

	public function get_name() {
    return 'megamaxmenu';
  }

	public function get_title() {
    return 'MegaMaxMenu Mega Menu';
  }

	public function get_icon() {
    return 'eicon-archive-posts';
  }

	public function get_categories() {
    return [ 'general' ];
  }

	protected function _register_controls() {
    $this->start_controls_section(
			'menu_section',
			[
				'label' => __( 'Menu', 'megamaxmenu' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

    $this->add_control(
			'assign',
			[
				'label' => __( 'Assign', 'megamaxmenu' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'menu' => __( 'Menu', 'megamaxmenu' ),
					'theme_location' => __( 'Theme Location', 'megamaxmenu' ),
				],
				'default' => 'menu',
			]
		);

    //$menu_ops = megamaxmenu_get_nav_menu_ops();
		$menus = wp_get_nav_menus( array('orderby' => 'name') );
		$menu_ops = array( 0 => '-- Select Menu --' );
		foreach( $menus as $menu ){
			$menu_ops[$menu->term_id] = $menu->name;
		}
		//megamaxp( $menu_ops );

    $this->add_control(
			'menu',
			[
				'label' => __( 'Menu', 'megamaxmenu' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $menu_ops,
        'default' => 0,
        //'default' => array_key_first( $menu_ops ),
        'condition' => [
          'assign' => 'menu'
        ],
        // 'condition' => [
    		// 	'terms' => [
    		// 		[
    		// 			'name' => 'assign',
    		// 			'operator' => '==',
    		// 			'value' => [
    		// 				'menu',
    		// 			],
    		// 		],
    		// 	],
    		// ],
			]
		);

    $theme_location_ops = get_registered_nav_menus(); //megamaxmenu_get_theme_location_ops();

    $this->add_control(
			'theme_location',
			[
				'label' => __( 'Theme Location', 'megamaxmenu' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $theme_location_ops,
        //'default' => array_key_first( $theme_location_ops ),
        'condition' => [
          'assign' => 'theme_location',
        ],
        // 'conditions' => [
    		// 	'terms' => [
    		// 		[
    		// 			'name' => 'assign',
    		// 			'operator' => '==',
    		// 			'value' => [
    		// 				'theme_location',
    		// 			],
    		// 		],
    		// 	],
    		// ],
			]
		);

    $configs = megamaxmenu_get_menu_instances(true);
    $config_ops = [];
    foreach( $configs as $config_id ){
      $config_ops[$config_id] = $config_id;
    }

		$this->add_control(
			'config',
			[
				'label' => __( 'Configuration', 'megamaxmenu' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $config_ops,
        'default' => 'main',
			]
		);

		$this->end_controls_section();
  }

	protected function render() {
    $settings = $this->get_settings_for_display();

		//echo '<div class="megamaxmenu-elementor-widget">';

    $config = $settings['config'];
    $menu = $settings['menu'];
    $theme_location = $settings['theme_location'];

    //megamaxp( $settings );

	  //echo "MegaMaxMenu $config Configuration $theme_location Theme Loc $menu Menu";

    switch( $settings['assign'] ){
      case 'menu':

        if( !$settings['menu'] ){
          megamaxmenu_admin_notice( 'Please select a <strong>Menu</strong> in the Elementor settings' );
          return;
        }

        megamaxmenu( $config , [ 'menu' => $settings['menu'] ] );
        break;

      case 'theme_location':

        if( !$settings['theme_location'] ){
          megamaxmenu_admin_notice( 'Please select a <strong>Theme Location</strong> in the Elementor settings' );
          return;
        }

        megamaxmenu( $config , ['theme_location' => $settings['theme_location'] ] );
        break;
    }

		//echo '</div>';
  }

	protected function _content_template() {}

}
