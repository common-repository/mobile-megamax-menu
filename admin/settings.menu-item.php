<?php

add_action( 'admin_print_styles-nav-menus.php' , 'megamaxmenu_admin_menu_load_assets' );

function megamaxmenu_admin_menu_load_assets() {

	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script('wp-color-picker');
	wp_enqueue_script( 'jquery' );


	$assets = MEGAMAXMENU_URL . 'admin/assets/';
	wp_enqueue_style( 'megamaxmenu-menu-admin', $assets.'admin.menu.css' );

	//fontawesome 4
	//wp_enqueue_style( 'megamaxmenu-menu-admin-font-awesome', $assets.'fontawesome/css/font-awesome.min.css' );
	//fontawesome 5
	//add_filter( 'script_loader_tag', 'megamaxmenu_fontawesome_defer', 10, 2 );

	wp_enqueue_style( 'megamaxmenu-font-awesome-all' , 	MEGAMAXMENU_URL .'assets/fontawesome/css/all.min.css' , false , false );


	wp_enqueue_script( 'megamaxmenu-menu-admin', $assets.'admin.menu.js' , array( 'jquery' ) , MEGAMAXMENU_VERSION , true );

	$megamaxmenu_menu_data = megamaxmenu_get_menu_items_data();

	wp_localize_script( 'megamaxmenu-menu-admin' , 'megamaxmenu_menu_item_data' , $megamaxmenu_menu_data );

	wp_localize_script( 'megamaxmenu-menu-admin' , 'megamaxmenu_meta' , array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'		=> megamaxmenu_menu_item_settings_nonce(),
		'item_panels'	=> megamaxmenu_get_menu_item_panels_map(),
		'sandbox_url'	=> megamaxmenu_sandbox_url(),
	) );
}

function megamaxmenu_menu_item_settings_panel(){

	$panels = megamaxmenu_menu_item_settings_panels();
	$settings = megamaxmenu_menu_item_settings();
	$defaults = megamaxmenu_menu_item_setting_defaults();

	$ordered_panels = array();
	$k = 1000;
	foreach( $panels as $panel_id => $panel ){
		$order = isset( $panel['order'] ) ? $panel['order'] : $k++;
		$ordered_panels[$order] = $panel_id;
	}
	ksort( $ordered_panels );

	?>
	<div class="megamaxmenu-js-check">
		<div class="megamaxmenu-js-check-peek"><i class="fas fa-truck"></i> Loading MegaMaxMenu...</div>
		<div class="megamaxmenu-js-check-details">
			<p>
			If this message does not disappear, it means that MegaMaxMenu has not been able to load.
			This most commonly indicates that you have a javascript error on this page, which will need to be resolved in order to allow MegaMaxMenu to run.
			</p>
		</div>
	</div>
	<div class="megamaxmenu-menu-item-settings-wrapper">

		<div class="megamaxmenu-menu-item-settings-topper">
			<i class="fas fa-cogs"></i> MEGAMAXMENU SETTINGS
		</div>

		<div class="megamaxmenu-menu-item-panel megamaxmenu-menu-item-panel-negative">

			<div class="megamaxmenu-menu-item-stats shift-clearfix">
				<div class="megamaxmenu-menu-item-title"><i class="fas fa-location-arrow"></i> Menu Item Unknown Template</div>
				<div class="megamaxmenu-menu-item-id">#menu-item-X</div>
				<div class="megamaxmenu-menu-item-type">Custom</div>
			</div>

			<div class="megamaxmenu-menu-item-panel-info" >


				<ul class="megamaxmenu-menu-item-tabs">
					<?php foreach( $ordered_panels as $order => $panel_id ):
						$panel = $panels[$panel_id];
						$icon = '';
						if( isset( $panel['icon'] ) ) $icon = '<i class="fas fa-'.$panel['icon'].'"></i> ';
						?>
					<li class="megamaxmenu-menu-item-tab"  data-megamaxmenu-tab="<?php echo $panel_id; ?>"><a href="#" data-megamaxmenu-tab="<?php echo $panel_id; ?>" ><?php echo $panel['title']; echo $icon; ?></a></li>
					<?php endforeach; ?>

				</ul>

			</div>

			<div class="megamaxmenu-menu-item-panel-settings shift-clearfix" >
				<form class="megamaxmenu-menu-item-settings-form" action="" method="post" enctype="multipart/form-data" >

					<div class="megamaxmenu-menu-item-save-button-wrapper">

						<a class="megamaxmenu-menu-item-settings-close" href="#"><i class="fas fa-times"></i> <span class="megamaxmenu-key">ESC</span></a>

						<input class="megamaxmenu-menu-item-save-button" type="submit" value="Save Menu Item" />
						<div class="megamaxmenu-menu-item-status megamaxmenu-menu-item-status-save">
							<i class="megamaxmenu-status-save fas fa-save"></i>
							<i class="megamaxmenu-status-success fas fa-check"></i>
							<i class="megamaxmenu-status-working fas fa-cog" title="Working..."></i>
							<i class="megamaxmenu-status-warning fas fa-exclamation-triangle"></i>
							<i class="megamaxmenu-status-error fas fa-exclamation-circle"></i>

							<span class="megamaxmenu-status-message"></span>
						</div>

						<a class="megamaxmenu-clear-settings">
							<i class="fas fa-eraser"></i>
							<div class="megamaxmenu-menu-item-setting-tip megamaxmenu-menu-item-setting-tip-below"><i class="megamaxmenu-tip-icon fas fa-lightbulb"></i> <?php
								_e( 'Clear the settings for this item.' , 'megamaxmenu' ); ?>
							</div>
						</a>

						<?php if( megamaxmenu_op( 'allow_custom_defaults' , 'general' ) == 'on' ): ?>
							<span class="megamaxmenu-menu-item-meta-ops-wrap">
								<i class="fas fa-cog megamaxmenu-menu-item-toggle-meta-ops"></i>
								<span class="megamaxmenu-menu-item-meta-ops">
									<span class="megamaxmenu-menu-item-setting">
										<label><input type="checkbox" name="megamaxmenu-meta-save-defaults" /> <?php _e( 'Set as defaults' , 'megamaxmenu' ); ?></label>
										<div class="megamaxmenu-menu-item-setting-tip"><i class="megamaxmenu-tip-icon fas fa-lightbulb"></i> <?php
											_e( 'Set this item\'s current settings as the default menu item settings for all menu items (won\'t affect menu items whose settings have already been saved).  Be careful - if these aren\'t set intelligently, it can have undesirable effects.  After saving menu item, ave/refresh menu to enable new defaults.' , 'megamaxmenu' ); ?>
										</div>
									</span>
									<span class="megamaxmenu-menu-item-setting">
										<label><input type="checkbox" name="megamaxmenu-meta-reset-defaults" /> <?php _e( 'Reset defaults' , 'megamaxmenu' ); ?></label>
										<div class="megamaxmenu-menu-item-setting-tip"><i class="megamaxmenu-tip-icon fas fa-lightbulb"></i> <?php
											_e( 'Clear the custom settings defaults and restore the standard defaults.  After saving menu item, save/refresh menu to reset defaults' , 'megamaxmenu' ); ?>
										</div>
									</span>
								</span>
							</span>
						<?php endif; ?>
					</div>

					<div class="megamaxmenu-menu-item-load-error-notice megamaxmenu-admin-notice megamaxmenu-admin-notice-warning">
						<p>If you are seeing this error, your settings have not loaded properly.  This is most commonly caused by a memory exception
							from trying to load too many posts or terms.  Please visit the <em>MegaMaxMenu Control Panel &gt; General Settings &gt; Advanced Menu Items</em> and enable the following settings:</p>
						<ul>
							<li><strong>Disable Autocomplete</strong></li>
							<li><strong>Disable Dynamic Posts Author Selection</strong></li>
						</ul>
						<p>If this does not resolve the issue, please enable WP_DEBUG and check for PHP errors in your Appearance &gt; Menus panel.</p>
					</div>

					<?php foreach( $ordered_panels as $order => $panel_id ):
							$panel = $panels[$panel_id];
							$panel_settings = $settings[$panel_id];
							ksort( $panel_settings );
							$icon = '';
							if( isset( $panel['icon'] ) ) $icon = '<i class="fas fa-'.$panel['icon'].'"></i>';
							?>
						<div class="megamaxmenu-menu-item-tab-content" data-megamaxmenu-tab-content="<?php echo $panel_id; ?>"<?php
								if( isset( $panel['apply_if'] ) ){
									?> data-megamaxmenu-apply-if="<?php echo $panel['apply_if']; ?>"<?php
								}
							?>>

							<div class="megamaxmenu-menu-item-tab-header">

								<h3><?php echo $icon; ?>
									<!--<i class="fas fa-sliders"></i>--> <?php echo $panel['title']; ?> Settings</h3>

								<?php if( isset( $panel['info'] ) ): ?>
								<div class="megamaxmenu-menu-item-tab-header-info">
									<i class="megamaxmenu-panel-info-icon fas fa-info-circle"></i>
									<?php echo $panel['info']; ?>
								</div>
								<?php endif; ?>

								<?php if( isset( $panel['tip'] ) ): ?>
								<div class="megamaxmenu-menu-item-tab-header-tip">
									<i class="megamaxmenu-panel-info-icon fas fa-lightbulb"></i>
									<?php echo $panel['tip']; ?>
								</div>
								<?php endif; ?>

								<?php if( isset( $panel['warning'] ) ): ?>
								<div class="megamaxmenu-menu-item-tab-header-warning">
									<i class="megamaxmenu-panel-info-icon fas fa-exclamation-triangle"></i>
									<?php echo $panel['warning']; ?>
								</div>
								<?php endif; ?>
							</div>

							<?php foreach( $panel_settings as $setting_id => $setting ):

								$classes = array();
								$classes[] = 'megamaxmenu-menu-item-setting';
								$classes[] = 'megamaxmenu-menu-item-setting-'.$setting['type'];
								$classes[] = 'megamaxmenu-menu-item-setting-'.$setting['id'];
								if( isset( $setting['depth'] ) ){
									$classes[] = 'megamaxmenu-menu-item-setting-depth-'.$setting['depth'];
								}
								$class = implode( ' ' , $classes );

								//$tip = isset( $setting['tip'] ) ? '<div class="megamaxmenu-menu-item-setting-tip"><i class="megamaxmenu-tip-icon fas fa-lightbulb"></i> '.$setting['tip'].'</div>' : '';
								?>

								<div class="<?php echo $class; ?>">
									<label class="megamaxmenu-menu-item-setting-label"><?php
										echo '<span class="megamaxmenu-item-setting-title">'.$setting['title'].'</span>';
										if( isset( $setting['scenario'] ) ){
											echo '<span class="megamaxmenu-item-setting-scenario"><i class="fas fa-info-circle"></i> '.$setting['scenario'].' <span class="megamaxmenu-item-setting-scenario-tip">This setting was designed specifically for this scenario.  Using this setting with a different scenario may have unpredictable results.</span></span>';
										}
										if( isset( $setting['cue'] ) ){
											echo '<span class="megamaxmenu-item-setting-cue">'.$setting['cue'].'</span>';
										}
										//echo $tip;
									?></label>
									<div class="megamaxmenu-menu-item-setting-input-wrap">
										<?php megamaxmenu_show_menu_item_setting( $setting , $defaults[$setting['id']] ); ?>
									</div>

								</div>

							<?php endforeach; ?>

						</div>


					<?php endforeach; ?>

					<span class="megamaxmenu-settings-completion-marker"></span>
				</form>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'admin_footer-nav-menus.php' , 'megamaxmenu_menu_item_settings_panel');


function megamaxmenu_show_menu_item_setting( $setting , $default ){

	if( isset( $setting['pro_only'] ) && $setting['pro_only'] ){
		echo '<a class="megamaxmenu-upgrade-link" target="_blank" href="">Upgrade to MegaMaxMenu Pro</a> to use this feature.';
		return;
	}


	$id = $setting['id'];
	$type = $setting['type'];
	//$default = $setting['default'];
	$desc = isset( $setting['desc'] ) ? '<span class="megamaxmenu-menu-item-setting-description">'.$setting['desc'].'</span>' : '';
	$tip = isset( $setting['tip'] ) ? '<div class="megamaxmenu-menu-item-setting-tip"><i class="megamaxmenu-tip-icon fas fa-lightbulb"></i> '.$setting['tip'].'</div>' : '';



	$name = 'name="'.$id.'"';
	$value = 'value="'. ( is_array( $default ) ? '' : $default ).'"';
	$data_setting = 'data-megamaxmenu-setting="'.$id.'"';

	$class_str = 'megamaxmenu-menu-item-setting-input';
	$class = 'class="'.$class_str.'"';

	$ops;
	if( isset( $setting['ops'] ) ){
		$ops = $setting['ops'];
		if( !is_array( $ops ) && function_exists( $ops ) ){
			if( isset( $setting['ops_args'] ) ){
				$ops = $ops( $setting['ops_args'] );
			}
			else $ops = $ops();
		}
	}

	switch( $type ){
		case 'checkbox': ?>
			<input <?php echo $class; ?> type="checkbox" <?php echo "$name $data_setting"; checked( $default , 'on' ); ?> />
			<?php break;

		case 'multicheck':
			?>
			<div class="megamaxmenu-multicheck-wrap">

			<?php foreach( $ops as $_val => $_name ):
				$_name_att = 'name="'.$id.'[]"';
				$_value_att = 'value="'.$_val.'"';

				$checked = '';
				if( $default == '_all_on' ){
					$checked = 'checked="checked"';
				}
				if( is_array( $default ) ){
					if( in_array( $_val , $default ) ){
						$checked = 'checked="checked"';
					}
				}
				?>
				<label class="megamaxmenu-multicheck-label" title="<?php echo $_val; ?>" >
					<input type="checkbox" class="checkbox megamaxmenu-multicheckbox" id="" <?php echo "$data_setting $_name_att $_value_att "; echo $checked; ?>  />
					<?php echo $_name; ?>
				</label>
			<?php endforeach; ?>

			</div>

			<?php break;

		case 'text': ?>
			<input <?php echo $class; ?> type="text" <?php echo "$name $value $data_setting"; ?> />
			<?php break;

		case 'textarea': ?>
			<textarea <?php echo $class; ?> <?php echo "$name $data_setting"; ?> ></textarea>
			<?php break;

		case 'select': ?>
			<select <?php echo $class; ?> <?php echo "$name $data_setting"; ?> >
				<?php foreach( $ops as $_val => $_name ): ?>
				<option value="<?php echo $_val; ?>" <?php selected( $default , $_val ); ?> ><?php echo $_name; ?></option>
				<?php endforeach; ?>
			</select>
			<?php break;

		case 'radio': ?>
			<div class="megamaxmenu-radio-group <?php if( isset($setting['type_class'] ) ) echo $setting['type_class']; ?> <?php if( count( $ops ) > 1 ) echo 'megamaxmenu-radio-multiple-subgroups'; ?>">

				<?php foreach( $ops as $_group_id => $group ): ?>

					<div class="megamaxmenu-radio-subgroup megamaxmenu-radio-subgroup-<?php echo $_group_id; ?>">

						<?php if( isset( $group['group_title'] ) ): ?>
							<h4 class="megamaxmenu-radio-group-title"><?php echo $group['group_title']; ?></h4>
						<?php endif; ?>

						<?php foreach( $group as $_val => $_data ):

							if( $_val == 'group_title' ) continue;

							$_name = isset( $_data['name'] ) ? $_data['name'] : $_val;
							$selected = false;
							if( $default == $_val ) $selected = true;

							$img = '';
							if( isset( $_data['img'] ) ) $img = $_data['img'];

							$img_icon = '';
							if( isset( $_data['img_icon'] ) ) $img_icon = $_data['img_icon'];
							?>
						<div class="megamaxmenu-radio-option">
							<label class="megamaxmenu-radio-label shift-clearfix<?php if( $img || $img_icon ) echo ' megamaxmenu-radio-label-with-image'; ?><?php if( $selected ) echo ' megamaxmenu-radio-label-selected'; ?>">
								<input <?php echo $class; ?> type="radio" value="<?php echo $_val; ?>" <?php echo "$name $data_setting"; ?> <?php checked( $default , $_val ); ?>>

								<?php if( $img ): ?>
									<img src="<?php echo $img; ?>" />
								<?php endif; ?>

								<?php if( $img_icon ): ?>
									<span class="megamaxmenu-radio-img-icon">
										<i class="<?php echo $img_icon; ?>"></i>
									</span>
								<?php endif; ?>

								<?php echo $_name; ?>

								<?php if( isset( $_data['desc'] ) ): ?>
									<div class="megamaxmenu-radio-desc">
										<?php echo $_data['desc']; ?>
									</div>
								<?php endif; ?>
							</label>


						</div>
						<?php endforeach; ?>
					</div>

				<?php endforeach; ?>

			</div>

			<?php break;

		case 'icon': ?>
			<div class="megamaxmenu-icon-settings-wrap">
				<div class="megamaxmenu-icon-selected">
					<i class="megamaxmenu-icon <?php echo $default; ?>"></i>
					<span class="megamaxmenu-icon-set-icon">Set Icon</span>
				</div>
				<div class="megamaxmenu-icons shift-clearfix">
					<div class="megamaxmenu-icons-search-wrap">
						<input class="megamaxmenu-icons-search" placeholder="Type to search" />
					</div>

				<?php foreach( $ops as $_val => $data ): if( $_val == '' ) continue; ?>
					<span class="megamaxmenu-icon-wrap" title="<?php echo $data['title']; ?>" data-megamaxmenu-search-terms="<?php echo strtolower( $data['title'] ); ?>"><i class="megamaxmenu-icon <?php echo $_val; ?>" data-megamaxmenu-icon="<?php echo $_val; ?>" ></i></span>
				<?php endforeach; ?>
					<span class="megamaxmenu-icon-wrap megamaxmenu-remove-icon" title="Remove Icon"><i class="megamaxmenu-icon" data-megamaxmenu-icon="" >Remove Icon</i></span>
				</div>
				<select <?php echo $class; ?> <?php echo "$name $data_setting"; ?> >
					<?php foreach( $ops as $_val => $data ): ?>
					<option value="<?php echo $_val; ?>" <?php selected( $default , $_val ); ?> ><?php echo $data['title']; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php break;


		case 'media': ?>
			<div class="megamaxmenu-media-wrapper">
				<div class="media-preview-wrap"></div>
				<input class="<?php echo $class_str; ?> megamaxmenu-media-id" type="text" <?php echo "$name $value $data_setting"; ?> />

				<div class="megamaxmenu-media-buttons">
					<a class="megamaxmenu-setting-button" data-uploader-title="Upload or Choose from Media Library" ><i class="fas fa-image"></i> Select</a>
					<a class="megamaxmenu-remove-button">&times; Remove</a>
					<a class="megamaxmenu-edit-media-button" target="_blank"><i class="fas fa-pencil"></i> Edit</a>
				</div>
			</div>
			<?php break;

		case 'autocomplete': ?>

			<div class="megamaxmenu-autocomplete">


				<input class="megamaxmenu-autocomplete-input" data-auto-set="true" data-current-id="" data-current-name="" placeholder="Type to filter" type="text" />
				<input class="megamaxmenu-menu-item-setting-input megamaxmenu-autocomplete-setting" placeholder="ID" type="text" <?php echo "$name $value $data_setting"; ?> />

				<span class="megamaxmenu-autocomplete-toggle"><i class="fas fa-chevron-down"></i></span>
				<span class="megamaxmenu-autocomplete-clear"><i class="fas fa-ban"></i></span>

				<div class="megamaxmenu-autocomplete-ops">
					<?php foreach( $ops as $_val => $_name ):
						$_name = strip_tags( $_name );
					?>
					<span class="megamaxmenu-autocomplete-op" data-opname="<?php echo strtolower( $_name ); ?>" data-val="<?php echo $_val; ?>" ><span class="megamaxmenu-autocomplete-op-name"><?php
						echo $_name; ?></span><span class="megamaxmenu-autocomplete-op-val"><?php echo $_val; ?></span></span>
					<?php endforeach; ?>
				</div>

			</div>

			<?php break;

		case 'color': ?>
			<input type="text" class="megamaxmenu-colorpicker" <?php echo $name . ' '. $value . ' ' . $data_setting; ?> />

			<?php break;

		default: ?>
			What's a "<?php echo $type; ?>"?
			<?php
	}

	echo $desc;
	echo $tip;

}


function megamaxmenu_menu_item_settings_panels(){
	$panels = array();

	$panels['row'] = array(
		'title'	=> __( 'Row', 'megamaxmenu' ),
		'icon'	=> 'cog',
	);

	$panels['general'] = array(
		'title'	=> __( 'General', 'megamaxmenu' ),
		'icon'	=> 'link',
		'info'	=> __( 'Settings for the item link' , 'megamaxmenu' ),
		'order'	=> 100,

	);


	$panels['layout'] = array(
		'title'	=> __( 'Layout' , 'megamaxmenu' ),
		'icon'	=> 'columns',
		'info'	=> __( 'Control the layout of this item' , 'megamaxmenu' ),
		'order'	=> 130,
	);

	$panels['column_layout'] = array(
		'title'	=> __( 'Column Layout' , 'megamaxmenu' ),
		'icon'	=> 'columns',
		'info'	=> __( 'Control the layout of this Column Item' , 'megamaxmenu' ),
		'order'	=> 131,
	);

	$panels['submenu'] = array(
		'title'	=> __( 'Submenu' , 'megamaxmenu' ),
		'icon'	=> 'list-alt',
		'info'	=> __( 'These settings control how the submenu of this item is displayed.' , 'megamaxmenu' ),
		'order'	=> 150,
	);

	$panels['deprecated'] = array(
		'title'	=> __( 'Deprecated', 'megamaxmenu' ),
		'icon'	=> 'minus-circle',
		'warning' => __( 'These settings are deprecated and only included for backwards compatibility.  It is not recommended to use them as they may be removed in a future version.' , 'megamaxmenu' ),
		'order'	=> 220
	);

	$panels['divider'] = array(
		'title'	=> __( 'Divider', 'megamaxmenu' ),
		'icon'	=> 'minus-square',
		'info' => __( 'There are no custom settings for the Divider.' , 'megamaxmenu' ),
		'order'	=> 230
	);

	$panels = apply_filters( 'megamaxmenu_menu_item_settings_panels' , $panels );

	return $panels;
}

function megamaxmenu_menu_item_settings(){

	$admin_img_assets = MEGAMAXMENU_URL . 'admin/assets/images/';

	$settings = array();
	$panels = megamaxmenu_menu_item_settings_panels();
	foreach( $panels as $id => $panel ){
		$settings[$id] = array();
	}

	$column_ops = megamaxmenu_get_item_column_ops();


	/** ROW **/
	$settings['row'][60] = array(
		'id' 		=> 'submenu_column_default',
		'title'		=> __( 'Row Column Default' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'default'	=> 'auto',
		'desc'		=> __( 'The number of columns this row should be broken into by default.  Can be overridden on individual items', 'megamaxmenu' ),
		'ops'		=> $column_ops,
	);

	$settings['row'][65] = array(
		'id' 		=> 'submenu_column_autoclear',
		'title'		=> __( 'Auto Clear' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'on',
		'desc'		=> __( 'Automatically start a new row of items every X items.  For example, if you choose a Submenu column default of 1/4, the 5th item will start a new column automatically.  Disable if you are adjusting item columns manually.' , 'megamaxmenu' ),
	);

	$settings['row'][67] = array(
		'id' 		=> 'row_content_align',
		'title'		=> __( 'Row Column Alignment' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'default'	=> 'default',
		'desc'		=> __( 'Explicitly align the row columns to the left, right, or center, relative to the row container.  If in doubt, leave this set to default.', 'megamaxmenu' ),
		'ops'		=> array(
			'group1' => array(
				'default'	=> array( 'name' => __( 'Default' , 'megamaxmenu' ) ),
				'left'		=> array( 'name' => __( 'Left' , 'megamaxmenu' ) ),
				'right'		=> array( 'name' => __( 'Right' , 'megamaxmenu' ) ),
				'center'	=> array( 'name' => __( 'Center' , 'megamaxmenu' ) ),
			)
		),
	);
  $settings['row'][70] = array(
    'id'		=> 'row_column_dividers',
    'title'		=> __( 'Row Columns Dividers' , 'megamaxmenu' ),
    'type'		=> 'color',
    'default'	=> '',
    'desc'		=> __( 'The color of the border between each column in this row.  Note this only affects columns that are children of this item, not further descendants', 'megamaxmenu' ),
    'tip'		=> __( 'You will likely want to set a Minimum Height below as well.' , 'megamaxmenu' ),
    'on_save'	=> 'submenu_column_divider_color'
  );
  $settings['row'][71] = array(
    'id'		=> 'row_column_min_height',
    'title'		=> __( 'Row Columns Minimum Height' , 'megamaxmenu' ),
    'type'		=> 'text',
    'default'	=> '',
    'desc'		=> __( 'Useful when using the Row Columns Dividers setting', 'megamaxmenu' ),
    'on_save'	=> 'submenu_column_min_height'
  );

	$settings['row'][80] = array(
		'id' 		=> 'row_padding',
		'title'		=> __( 'Row Padding' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default'	=> '',
		'desc'		=> __( 'Padding on this specific row', 'megamaxmenu' ),
		'on_save'	=> 'row_padding',
	);

	$settings['row'][90] = array(
		'id' 		=> 'grid_row',
		'title'		=> __( 'Grid Row' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
		'desc'		=> __( 'Space this row as a grid with equal padding. Useful for image grids.', 'megamaxmenu' ),
	);


	/** GENERAL **/


	$settings['general'][9] = array(
		'id' 		=> 'item_display',
		'title'		=> __( 'Item Display' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		//'depth'		=> '1-above',
		'scenario'	=> __( 'Submenu Items' , 'megamaxmenu' ),
		'default' 	=> 'auto',
		'desc'		=> __( 'No effect on Top Level Items.', 'megamaxmenu' ),
		'ops'		=> array(

			'group' => array(
				'auto'		=> array(
					'name'	=> __( 'Automatic' , 'megamaxmenu' ),
					'desc'	=> __( 'Automatically determine the appropriate display type', 'megamaxmenu' ),
				),
				'header'	=> array(
					'name'	=> __( 'Header' , 'megamaxmenu' ),
					'desc'	=> __( 'Display as a submenu column header' , 'megamaxmenu' ),
				),
				'normal'	=> array(
					'name'	=> __( 'Normal' , 'megamaxmenu' ),
					'desc'	=> __( 'Display as a normal submenu item' , 'megamaxmenu' ),
				),
			),

		),
	);

	$settings['general'][10] = array(
		'id' 		=> 'disable_link',
		'title'		=> __( 'Disable Link', 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Check this box to remove the link from this item; clicking a disabled link will not result in any URL being followed.' , 'megamaxmenu' )
	);

	$settings['general'][15] = array(
		'id' 		=> 'disable_text',
		'title'		=> __( 'Disable Text', 'megamaxmenu' ),
		'desc'		=> __( 'Do not display the text for this item' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
	);

	$settings['general'][20] = array(
		'id' 		=> 'highlight',
		'title'		=> __( 'Highlight Link', 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Highlight this menu item' , 'megamaxmenu' )
	);




























	/** LAYOUT **/


	$settings['layout'][10] = array(
		'id' 		=> 'columns',
		'title'		=> __( 'Columns Width', 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'auto',
		'desc'		=> __( 'This is the fraction of the submenu width that the item/column will occupy.' , 'megamaxmenu' ),
		'tip'		=> __( 'Remember that if you set the columns to a fraction, the wrapper for this item must be either full width or have an explicit width set.  For submenu items, that means setting an explicit or full width submenu.  For top level items, that means setting an explicit width or full width menu bar.' , 'megamaxmenu' ),
		'ops'		=> $column_ops
	);

	$settings['layout'][20] = array(
		'id' 		=> 'item_layout',
		'title'		=> __( 'Item Layout', 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'default' 	=> 'default',
		'desc'		=> __( 'Note: You can set the default for this setting in the parent item\'s Submenu Item Layout setting.  You can also set the default Image Layout globally in the Control Panel, which will be used when this setting is set to Default and an image has been set (inheriting from the parent item takes precedence).' , 'megamaxmenu' ),
		'ops'		=> 'megamaxmenu_get_item_layout_ops'
	);

	$settings['layout'][30] = array(
		'id' 		=> 'content_alignment',
		'title'		=> __( 'Item Content Alignment', 'megamaxmenu' ),
		'type'		=> 'radio',
		'scenario'	=> 'Vertically stacked layouts',
		'default' 	=> 'default',
		'desc'		=> __( 'Setting this to "Center" will allow you to have a centered image stacked above the title when used in conjunction with the "Image Above" Item Layout.  If you leave this set to Default, the value will be inherited from the parent item\'s "Submenu Item Content Alignment" setting' , 'megamaxmenu' ),
		'ops'		=> array(
			'group'	=> array(
				'default'	=> array(
					'name' 	=> __( 'Default' , 'megamaxmenu' ),
				),
				'left'		=> array(
					'name' 	=> __( 'Left' , 'megamaxmenu' ),
				),
				'center'		=> array(
					'name' 	=> __( 'Center' , 'megamaxmenu' ),
				),
				'right'		=> array(
					'name' 	=> __( 'Right' , 'megamaxmenu' ),
				),
			),
		),
	);


	$settings['layout'][40] = array(
		'id'		=> 'clear_row',
		'title'		=> __( 'New Row' , 'megamaxmenu' ),
		'desc'		=> __( 'Clear the previous row and start a new one with this item.' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
		'scenario'	=> __( 'Submenu Columns' , 'megamaxmenu' ),
	);

	//up( $settings['layout'][20]['ops'] , 3 );







	/** COLUMN LAYOUT **/

	$settings['column_layout'][10] = array(
		'id' 		=> 'columns',
		'title'		=> __( 'Column Width', 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'auto',
		'desc'		=> __( 'This is the fraction of the submenu width that the column will occupy.' , 'megamaxmenu' ),
		'tip'		=> __( 'Remember that if you set the columns to a fraction, the wrapper for this item must be either full width or have an explicit width set.  For submenu items, that means setting an explicit or full width submenu.  For top level items, that means setting an explicit width or full width menu bar.' , 'megamaxmenu' ),
		'ops'		=> $column_ops
	);

	$settings['column_layout'][20] = array(
		'id'		=> 'clear_row',
		'title'		=> __( 'New Row' , 'megamaxmenu' ),
		'desc'		=> __( 'Clear the previous row and start a new one with this item.' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
		'scenario'	=> __( 'Submenu Columns' , 'megamaxmenu' ),
	);


	$settings['column_layout'][40] = array(
		'id' 		=> 'submenu_column_default',
		'title'		=> __( 'Submenu Column Default' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'default'	=> 'auto',
		'desc'		=> __( 'The number of columns per row that the items within this column should be broken into by default.  Can be overridden on individual items', 'megamaxmenu' ),
		'ops'		=> $column_ops,
	);

	$settings['column_layout'][50] = array(
		'id' 		=> 'submenu_column_autoclear',
		'title'		=> __( 'Auto Row' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'on',
		'desc'		=> __( 'Automatically start a new row every X items.  For example, if you choose a Submenu column default of 1/4, the 5th item will start a new row automatically.  Disable if you are adjusting item columns manually.' , 'megamaxmenu' ),
		'scenario'	=> __( 'Mega Submenus' , 'megamaxmenu' ),
	);












	/** SUBMENU **/

	$settings['submenu'][20] = array(
		'id' 		=> 'submenu_type',
		'title'		=> 'Submenu Type',
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'default'	=> 'auto',
		'desc'		=> '',
		'ops'		=> array(
						'group1' => array(
							'auto'	=>	array(
								'name'	=> __( 'Automatic' , 'megamaxmenu' ),
								'desc'	=> __( 'MegaMaxMenu will attempt to automatically determine the best type of submenu for this item.' , 'megamaxmenu' ),
								'img_icon'	=> 'fas fa-bolt',
							),
							'mega'	=>	array(
								'name'	=> __( 'Mega Submenu' , 'megamaxmenu' ),
								'desc'	=> __( 'A mega submenu' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuType_mega.png',
							),
							'flyout'=>	array(
								'name' 	=> __( 'Flyout Submenu' , 'megamaxmenu' ),
								'desc'	=> __( 'A standard Flyout submenu.  Not intended for use inside Mega submenus.' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuType_flyout.png',
							),
							'stack'	=>	array(
								'name' 	=> __( 'Stack',	'megamaxmenu' ), //?
								'desc'	=> __( 'A stacked vertical submenu that is always visible' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuType_stack.png',
							),
							/*
							'accordion'	=> array(
								'name' 	=> __( 'Accordion? Reveal?',	'megamaxmenu' ), //?
								'desc'	=> __( 'A stacked vertical submenu that is revealed when activated' , 'megamaxmenu' ),
								//'img'	=> $admin_img_assets.'SubmenuType_stack.png',
							),
							*/
						),
					),
	);




	$settings['submenu'][40] = array(
		'id' 		=> 'submenu_position',
		'title'		=> __( 'Mega Submenu Position', 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'default'	=> 'full_width',
		'desc'		=> __( 'Select how the submenu should be positioned relative to this item.' , 'megamaxmenu' ),
		'ops'		=> array(
						'group1' => array(
							'full_width'=>	array(
								'name'	=> __( 'Full Width' , 'megamaxmenu' ),
								'desc'	=> __( 'The submenu will be the full width of the menu bar (or wider, if not bound by the menu bar).  <br/>' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_full.png',
							),
							'center'	=>	array(
								'name'	=> __( 'Center' , 'megamaxmenu' ),
								'desc'	=> __( 'Align mega submenu centered below the parent menu item.' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_center.png',
							),
							'left_edge_bar'	=>	array(
								'name'	=> __( 'Left Edge of Menu Bar' , 'megamaxmenu' ),
								'desc'	=> __( 'Align submenu to the left edge of the menu bar (or to the next relative wrapper if the submenus are not bound by the menu bar)' , 'megamaxmenu' ),

								'img'	=> $admin_img_assets.'SubmenuPosition_left_edge_bar.png',
							),
							'right_edge_bar'=>	array(
								'name'	=> __( 'Right Edge of Menu Bar' , 'megamaxmenu' ),
								'desc'	=> __( 'Align submenu to the right edge of the menu bar (or to the next relative wrapper if the submenus are not bound by the menu bar)' , 'megamaxmenu' ),

								'img'	=> $admin_img_assets.'SubmenuPosition_right_edge_bar.png',
							),
							'left_edge_item' =>	array(
								'name'	=> __( 'Left Edge of Parent Item' , 'megamaxmenu' ),
								'desc'	=> __( 'Align submenu to the left edge of the parent menu item.' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_left_edge_item.png',
							),
							'right_edge_item' =>	array(
								'name'	=> __( 'Right Edge of Parent Item' , 'megamaxmenu' ),
								'desc'	=> __( 'Align submenu to the right edge of the parent menu item.  Forces direction:rtl.' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_right_edge_item.png',
							),
							'vertical_full_height' =>	array(
								'name'	=> __( 'Vertical - Full Height' , 'megamaxmenu' ),
								'desc'	=> __( 'The submenu will be the full height of the menu bar (at minimum), aligned to the top. [Vertically oriented menus only]' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_vertical_full.png',
							),
							'vertical_parent_item' =>	array(
								'name'	=> __( 'Vertical - Aligned to parent' , 'megamaxmenu' ),
								'desc'	=> __( 'The submenu will be a natural height and aligned to its parent item. [Vertically oriented menus only]' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_vertical_align.png',
							),

						),
					),
		'scenario'	=> __( 'Mega Submenus Only' , 'megamaxmenu' ),
	);


	$settings['submenu'][45] = array(
		'id' 		=> 'flyout_submenu_position',
		'title'		=> __( 'Flyout Submenu Position', 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'default'	=> 'left_edge_item',
		'desc'		=> __( 'Select how the Flyout submenu should be positioned relative to this item.' , 'megamaxmenu' ),
		'ops'		=> array(
						'group1' => array(
							'left_edge_item' =>	array(
								'name'	=> __( 'Fly Right' , 'megamaxmenu' ),
								'desc'	=> __( 'For the first submenu, align to the left edge of the parent menu item. For deeper submenus, fly to the right of the previous level.' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_left_edge_item.png',
							),
							'right_edge_item' =>	array(
								'name'	=> __( 'Fly Left' , 'megamaxmenu' ),
								'desc'	=> __( 'For the first submenu, align to the right edge of the parent menu item. For deeper submenus, fly to the left of the previous level.  Forces direction:rtl.' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_right_edge_item.png',
							),
							'vertical_full_height' =>	array(
								'name'	=> __( 'Vertical - Full Height' , 'megamaxmenu' ),
								'desc'	=> __( 'The submenu will be the full height of the menu bar (at minimum), aligned to the top. [Vertically oriented menus only]' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_vertical_full.png',
							),
							'vertical_parent_item' =>	array(
								'name'	=> __( 'Vertical - Aligned to parent' , 'megamaxmenu' ),
								'desc'	=> __( 'The submenu will be a natural height and aligned to its parent item. [Vertically oriented menus only]' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'SubmenuPosition_vertical_align.png',
							),
						),
					),

		'scenario'	=> __( 'Flyout Submenus Only' , 'megamaxmenu' ),
	);

	$settings['submenu'][50] = array(
		'id' 		=> 'submenu_width',
		'title'		=> 'Submenu Width',
		'type'		=> 'text',
		'default' 	=> __( '' , 'megamaxmenu' ),
		'desc'		=> __( 'Leave blank to size to contents.  If you are not using the Full Width submenu layout, some layouts will require an explicit width.  Include the units (px/em/%).' , 'megamaxmenu' ),
		'tip'		=> __( 'If you are using a mega submenu that is not full-width, and you want to structure your submenu with the Columns settings, you should set an explicit width here.', 'megamaxmenu' ),
		'on_save'	=> 'submenu_width',
	);

	$settings['submenu'][55] = array(
		'id' 		=> 'submenu_min_width',
		'title'		=> 'Submenu Minimum Width',
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'By default, every mega submenu will be at least 50% width of the menu bar.  If you need to adjust this you can override it here.  Include the units (px/em/%).' , 'megamaxmenu' ),
		//'tip'		=> __( '', 'megamaxmenu' ),
		'on_save'	=> 'submenu_min_width',
	);

	$settings['submenu'][56] = array(
		'id' 		=> 'submenu_min_height',
		'title'		=> 'Submenu Minimum Height',
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'By default, submenus will be sized to their contents.  In 99% of cases, this will not be needed.  Note that this setting is not compatible with the Slide Reveal submenu transition.' , 'megamaxmenu' ),
		//'tip'		=> __( '', 'megamaxmenu' ),
		'on_save'	=> 'submenu_min_height',
	);


	$settings['submenu'][58] = array(
		'id' 		=> 'submenu_content_align',
		'title'		=> __( 'Submenu Column Alignment' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'default'	=> 'default',
		'desc'		=> __( 'Explicitly align the submenu columns to the left, right, or center, relative to the submenu container.  If in doubt, leave this set to default.  Note this is intended for use with the Submenu Position: Full Width.  If the submenu is not wider than the content, then this won\'t have much, if any, effect', 'megamaxmenu' ),
		'ops'		=> array(
			'group1' => array(
				'default'	=> array( 'name' => __( 'Default' , 'megamaxmenu' ) ),
				'left'		=> array( 'name' => __( 'Left' , 'megamaxmenu' ) ),
				'right'		=> array( 'name' => __( 'Right' , 'megamaxmenu' ) ),
				'center'	=> array( 'name' => __( 'Center' , 'megamaxmenu' ) ),
			)
		),
		'scenario'	=> __( 'Mega Submenus Only' , 'megamaxmenu' ),
	);


	$settings['submenu'][60] = array(
		'id' 		=> 'submenu_column_default',
		'title'		=> __( 'Submenu Column Default' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'default'	=> 'auto',
		'desc'		=> __( 'The number of columns per row that the submenu should be broken into by default.  Can be overridden on individual items', 'megamaxmenu' ),
		'ops'		=> $column_ops,
	);


	$settings['submenu'][63] = array(
		'id' 		=> 'submenu_column_autoclear',
		'title'		=> __( 'Auto Row' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'on',
		'desc'		=> __( 'Automatically start a new row every X items.  For example, if you choose a Submenu column default of 1/4, the 5th item will start a new row automatically.  Disable if you are adjusting item columns manually.' , 'megamaxmenu' ),
		'scenario'	=> __( 'Mega Submenus' , 'megamaxmenu' ),
	);

	$settings['submenu'][80] = array(
		'id' 		=> 'submenu_padded',
		'title'		=> __( 'Pad Submenu' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Add padding to submenus (doubles the edge gutters).  Useful if you need to make the spacing at the edges of a row equal to that between the columns.' , 'megamaxmenu' ),
		'scenario'	=> __( 'Mega Submenus' , 'megamaxmenu' ),
	);

	$settings['submenu'][85] = array(
		'id' 		=> 'submenu_indent',
		'title'		=> __( 'Indent Submenu' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Indents submenu.  Useful for vertically stacked content to create a visual hierarchy.' , 'megamaxmenu' ),
		'scenario'	=> __( 'Stack Submenus' , 'megamaxmenu' ),
	);

	$settings['submenu'][180] = array(
		'id' 		=> 'submenu_advanced',
		'title'		=> __( 'Advanced Submenus' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'default'	=> 'auto',
		'desc'		=> __( 'Advanced submenus are useful for layouts such as full-screen-width submenus with centered submenu contents.  You can leave this on Automatic; setting Enabled or Disabled just increases efficiency slightly.', 'megamaxmenu' ),
		'ops'		=> array(
						'group1' => array(
							'auto'	=>	array(
								'name'	=> __( 'Automatic' , 'megamaxmenu' ),
								'desc'	=> __( 'MegaMaxMenu will attempt to automatically determine whether the item should have an advanced submenu.' , 'megamaxmenu' ),
							),
							'disabled'	=>	array(
								'name'	=> __( 'Disabled' , 'megamaxmenu' ),
								'desc'	=> __( 'Disable the advanced submenu functionality.  The submenu cannot contain Rows.' , 'megamaxmenu' ),
							),
							'enabled'=>	array(
								'name' 	=> __( 'Enabled' , 'megamaxmenu' ),
								'desc'	=> __( 'Enable the advanced submenu functionality.  This allows you to add Row menu items to the submenu.  Note that all submenu items must be wrapped in Row Items (only Rows can be direct children of this item).  Or just use the Automatic setting.' , 'megamaxmenu' ),
							),
						),
					),
	);



	/** DEPRECATED **/

	$settings['deprecated'][10] = array(
		'id'		=> 'new_column',
		'title'		=> __( 'Start New Column' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
		'desc'		=> __( 'Start a new column under the same header.  This setting is now deprecated.  Instead, either (1) use the "Column" menu item, or set the Header Layout to twice the normal width and set the Submenu Column Default to 1/2 (or 1/desired number of columns) .', 'megamaxmenu' ),
		'scenario'	=> '3rd Level Menu Items'
	);

	return apply_filters( 'megamaxmenu_menu_item_settings' , $settings );

}

function megamaxmenu_menu_item_setting_defaults(){
	$defaults = array();

	$settings = megamaxmenu_menu_item_settings();
	foreach( $settings as $panel => $panel_settings ){
		foreach( $panel_settings as $setting ){
			$defaults[$setting['id']] = $setting['default'];
		}
	}

	if( !megamaxmenu_is_pro() ){
		$pro_defaults = megamaxmenu_pro_defaults();
		$defaults = array_merge( $defaults , $pro_defaults );
	}

	$defaults = apply_filters( 'megamaxmenu_menu_item_settings_defaults' , $defaults );

	return $defaults;
}

function megamaxmenu_pro_defaults(){
	$defaults = array(
		'item_align'						=> 'auto',
		'mini_item'							=> 'off',
		'scrollto'							=> '',
		//'scrollto_offset'					=> 0,
		'no_wrap'							=> 'off',
		'item_trigger'						=> 'auto',
		'disable_submenu_indicator'			=> 'off',
		'target_class'						=> '',
		'target_id'							=> '',
		'icon'								=> '',
		'icon_custom_class'					=> '',
		'item_image'						=> '',
		'inherit_featured_image'			=> 'off',
		'image_size'						=> 'inherit',
		'image_dimensions'					=> 'inherit',
		'image_width_custom'				=> '',
		'image_height_custom'				=> '',
		'disable_padding'					=> 'off',
		'hide_on_mobile'					=> 'off',
		'hide_on_desktop'					=> 'off',
		'disable_on_mobile'					=> 'off',
		'disable_on_desktop'				=> 'off',
		'disable_submenu_on_mobile'			=> 'off',
		'show_current'						=> 'off',
		'show_default'						=> 'off',
		'submenu_background_image'			=> '',
		'submenu_background_image_repeat'	=> 'no-repeat',
		'submenu_background_position'		=> 'bottom right',
		'submenu_background_size'			=> 'auto',
		'submenu_padding'					=> '',
		'submenu_footer_content'			=> '',
		'submenu_grid'						=> 'off',
		'submenu_autocolumns'				=> 'disabled',

		'tab_layout'						=> 'left',
		'tab_block_columns'					=> 'full',
		'tabs_group_layout'					=> '1-4',
		'panels_group_layout'				=> '3-4',
		'panels_padding'					=> '',
		'show_default_panel'				=> 'on',
		'tabs_trigger'						=> 'mouseover',
		'menu_segment'						=> '',
		'custom_content'					=> '',
		'pad_custom_content'				=> 'on',
		'columns'							=> 'auto',
		'item_align'						=> 'auto',

		'auto_widget_area'					=> '',
		'widget_area'						=> '',
		'widget_area_columns'				=> 'auto',


		'dp_subcontent'						=> 'none',
		'dp_view_all'						=> 'none',


	);
	return $defaults;
}


function megamaxmenu_get_menu_items_data( $menu_id = -1 ){

	if( $menu_id == -1 ){
		global $nav_menu_selected_id;
		$menu_id = $nav_menu_selected_id;
	}

	if( $menu_id == 0 ) return array();

	$megamaxmenu_menu_data = array();
	$menu_items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );

	foreach( $menu_items as $item ){
		$_item_settings = megamaxmenu_get_menu_item_data( $item->ID );
		if( $_item_settings != '' ){
			$megamaxmenu_menu_data[$item->ID] = $_item_settings;
		}
	}
	//shiftp( $megamaxmenu_menu_data );
	return $megamaxmenu_menu_data;
}

function megamaxmenu_get_menu_item_data( $item_id ){
	$meta = get_post_meta( $item_id , MEGAMAXMENU_MENU_ITEM_META_KEY , true );

	//Add URL for image
	if( !empty( $meta['item_image'] ) ){
		$src = wp_get_attachment_image_src( $meta['item_image'] );
		if( $src ){
			$meta['item_image_url'] = $src[0];
			$meta['item_image_edit'] = get_edit_post_link( $meta['item_image'], 'raw' );
		}
	}
	if( !empty( $meta['submenu_background_image'] ) ){
		$src = wp_get_attachment_image_src( $meta['submenu_background_image'] );
		if( $src ){
			$meta['submenu_background_image_url'] = $src[0];
			$meta['submenu_background_image_edit'] = get_edit_post_link( $meta['submenu_background_image'], 'raw' );
		}
	}

	//Convert icons //TODO FA5 - only do this until update has been run
	if( !empty( $meta['icon'] ) ){
		$meta['icon'] = megamaxmenu_fa5_convert( $meta['icon'] , true );
	}

	return $meta;
}



function megamaxmenu_get_item_column_ops(){
	$ops = array(
		'group1'	=> array(
				'group_title'	=> 'Basic',
				'auto'	=>	array(
								'name'	=> 'Automatic',
							),
				/*'inherit' =>array(
								'name'	=> 'Inherit',
							),*/
				'natural' => array(
								'name'	=> 'Natural',
							),

				'full'	=>	array(
								'name'	=> 'Full Width',
							),
			),
		'col-group2' 	=> array(
				'group_title'	=> 'Halves',
				'1-2'	=>	array(
								'name'	=> '1/2',
							),
			),
		'group3'	=> array(
				'group_title'	=> 'Thirds',
				'1-3'	=>	array(
								'name'	=> '1/3',
							),
				'2-3'	=>	array(
								'name'	=> '2/3',
							),
			),
		'group4'	=> array(
				'group_title'	=> 'Quarters',
				'1-4'	=>	array(
								'name'	=> '1/4',
							),
				'3-4'	=>	array(
								'name'	=> '3/4',
							),
			),
		'group5'	=> array(
				'group_title'	=> 'Fifths',
				'1-5'	=>	array(
								'name'	=> '1/5',
							),
				'2-5'	=>	array(
								'name'	=> '2/5',
							),
				'3-5'	=>	array(
								'name'	=> '3/5',
							),
				'4-5'	=>	array(
								'name'	=> '4/5',
							),
			),
		'group6'	=> array(
				'group_title'	=> 'Sixths',
				'1-6'	=>	array(
								'name'	=> '1/6',
							),
				'5-6'	=>	array(
								'name'	=> '5/6',
							),
			),
		'group7'	=> array(
				'group_title'	=> 'Sevenths',
				'1-7'	=>	array(
								'name'	=> '1/7',
							),
				'2-7'	=>	array(
								'name'	=> '2/7',
							),
				'3-7'	=>	array(
								'name'	=> '3/7',
							),
				'4-7'	=>	array(
								'name'	=> '4/7'
							),
				'5-7'	=>	array(
								'name'	=> '5/7',
							),
				'6-7'	=>	array(
								'name'	=> '6/7',
							),
			),

		'group8'	=> array(
				'group_title'	=> 'Eighths',
				'1-8'	=> 	array(
								'name'	=> '1/8'
							),
				'3-8'	=>	array(
								'name'	=> '3/8',
							),
				'5-8'	=>	array(
								'name'	=> '5/8',
							),
				'7-8'	=>	array(
								'name'	=> '7/8',
							),

			),

		'group9'	=> array(
				'group_title'	=> 'Ninths',
				'1-9'	=>	array(
								'name'	=> '1/9',
							),
				'2-9'	=>	array(
								'name'	=> '2/9',
							),
				'4-9'	=>	array(
								'name'	=> '4/9',
							),
				'5-9'	=>	array(
								'name'	=> '5/9',
							),
				'7-9'	=>	array(
								'name'	=> '7/9',
							),
				'8-9'	=>	array(
								'name'	=> '8/9',
							),

			),

		'group10'	=> array(
				'group_title'	=> 'Tenths',
				'1-10'	=>	array(
								'name'	=> '1/10',
							),
				'3-10'	=>	array(
								'name'	=> '3/10',
							),
				'7-10'	=>	array(
								'name'	=> '7/10',
							),
				'9-10'	=>	array(
								'name'	=> '9/10',
							),
			),

		'group11'	=> array(
				'group_title'	=> 'Elevenths',
				'1-11'	=> 	array(
								'name'	=> '1/11',
							),
				'2-11'	=> 	array(
								'name'	=> '2/11',
							),
				'3-11'	=> 	array(
								'name'	=> '3/11',
							),
				'4-11'	=> 	array(
								'name'	=> '4/11',
							),
				'5-11'	=> 	array(
								'name'	=> '5/11',
							),
				'6-11'	=> 	array(
								'name'	=> '6/11',
							),
				'7-11'	=> 	array(
								'name'	=> '7/11',
							),
				'8-11'	=> 	array(
								'name'	=> '8/11',
							),
				'9-11'	=> 	array(
								'name'	=> '9/11',
							),
				'10-11'	=> 	array(
								'name'	=> '10/11',
							),
			),
		'group12'	=> array(
				'group_title'	=> 'Twelfths',
				'1-12'	=> 	array(
								'name'	=> '1/12',
							),
				'5-12'	=> 	array(
								'name'	=> '5/12',
							),
				'7-12'	=> 	array(
								'name'	=> '7/12',
							),
				'11-12'	=> 	array(
								'name'	=> '11/12',
							),
			),

		);


	//TODO: Filter
	return $ops;
}

function megamaxmenu_get_column_complement( $columns ){

	//3-5

	$m = explode( '-' , $columns );
	if( count( $m ) < 2 ) return $columns;

	// ( 5 - 3 ) - 5
	return ( $m[1] - $m[0] ) .'-'.$m[1];


	/*
	$map = array(

		'auto'		=> 'auto',
		'natural' 	=> 'natural',
		'full'		=> 'full',
		'1-2'		=>
		'1-3'	=>
		'2-3'	=>
		'1-4'	=>
		'3-4'	=>
		'1-5'	=>
		'2-5'	=>
		'3-5'	=>
		'4-5'	=>
		'1-6'	=>
		'5-6'	=>
		'1-7'	=>
		'2-7'	=>
		'3-7'	=>
		'4-7'	=>
		'5-7'	=>
		'6-7'	=>
		'1-8'	=>
		'3-8'	=>
		'5-8'	=>
		'7-8'	=>
		'1-9'	=>
		'2-9'	=>
		'4-9'	=>
		'5-9'	=>
		'7-9'	=>
		'8-9'	=>
		'1-10'	=>
		'3-10'	=>
		'7-10'	=>
		'9-10'	=>
		'1-11'	=>
		'2-11'	=>
		'3-11'	=>
		'4-11'	=>
		'5-11'	=>
		'6-11'	=>
		'7-11'	=>
		'8-11'	=>
		'9-11'	=>
		'10-11'	=>
		'1-12'	=>
		'5-12'	=>
		'7-12'	=>
		'11-12'	=>
		); */


}

function megamaxmenu_get_item_layout_ops(){

	$admin_img_assets = MEGAMAXMENU_URL . 'admin/assets/images/';

	$ops = array();

	$ops['core'] = array(
		'default' => array(
			'name'	=> __( 'Default', 'megamaxmenu' ),
			'desc'	=> __( 'Layout will be automatically determined' , 'megamaxmenu' ),
		),
		'text_only'	=> array(
			'name'	=> __( 'Text Only', 'megamaxmenu' ),
			'desc'	=> __( 'No image or icon, just the text' , 'megamaxmenu' ),
		),
	);

	$ops['icons']	= array(
		'group_title'	=> __( 'Icon Layouts', 'megamaxmenu' ),

		'icon_left' => array(
			'name'	=> __( 'Icon Left', 'megamaxmenu' ),
		),
	);

	return apply_filters( 'megamaxmenu_item_layout_ops' , $ops );
}

function megamaxmenu_get_item_layouts( $layout_id = 0 ){

	$layouts = array();

	$layouts['text_only'] = array(

		'order'	=> array(
			'title',
			'description',
		),
	);

	$layouts['image_left'] = array(

		'order'	=> array(
			'image',
			'title',
			'description',
		),

	);

	$layouts['image_above'] = array(

		'order'	=> array(
			'image',
			'title',
			'description',
		),

	);

	$layouts['image_right'] = array(

		'order'	=> array(

			'image',			//because we float it right
			'title',
			'description',

		),

	);

	$layouts['image_below'] = array(

		'order'	=> array(
			'title',
			'description',
			'image',
		),

	);

	$layouts['image_only'] = array(

		'order'	=> array(
			'image',
		),

	);

	$layouts['icon_left'] = array(
		'order'	=> array(
			'icon',
			'title',
			'description',
		),
	);

	$layouts = apply_filters( 'megamaxmenu_item_layouts' , $layouts );

	if( $layout_id ){
		if( isset( $layouts[$layout_id] ) ){
			return $layouts[$layout_id];
		}

		return false;
	}

	return $layouts;

}


function megamaxmenu_get_item_button_ops(){

	$ops = array();

	$ops['core'] = array(

		'off'	=> array(
			'name'	=> 'Disabled'
		),
		'custom'	=> array(
			'name'	=> 'Custom',
		),
		'red'	=> array(
			'name'	=> 'Red',
		),
		'blue'	=> array(
			'name'	=> 'Blue',
		),

	);

	return apply_filters( 'megamaxmenu_item_button_ops' , $ops );

}

function megamaxmenu_get_image_size_ops_inherit(){
	return megamaxmenu_get_image_size_ops( array( 'inherit' ) );
}

function megamaxmenu_get_image_size_dimensions( $s ){

	if( $s == 'inherit' || $s == 'full' ) return false;

	$available_sizes = get_intermediate_image_sizes();

	$w = $h = $c = 0;

	if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
		$w = get_option( $s . '_size_w' );
		$h = get_option( $s . '_size_h' );
		$c = (bool) get_option( $s . '_crop' );
	}
	else if ( isset( $_wp_additional_image_sizes[ $s ] ) ) {

		$w = $_wp_additional_image_sizes[ $s ]['width'];
		$h = $_wp_additional_image_sizes[ $s ]['height'];
		$c = $_wp_additional_image_sizes[ $s ]['crop'];
	}

	return compact( 'w', 'h', 'c' );
}

function megamaxmenu_get_image_size_ops( $exclude = array() ){


		$o = array(
			'inherit'	=> array(
				'name'	=> __( 'Inherit' , 'megamaxmenu' ),
				'desc'	=> __( 'Inherit settings from the menu Configuration settings' , 'megamaxmenu' )
			),
			'full'	=> array(
				'name'	=> __( 'Full' , 'megamaxmenu' ),
				'desc'	=> __( 'Display image at natural dimensions' , 'megamaxmenu' )
			),

		);

		$available_sizes = get_intermediate_image_sizes();

		//$o = array_merge( $o , $available_sizes );
		foreach( $available_sizes as $s ){

			$name = ucfirst( $s );
			$desc = '<small>'.__( 'Registered image size ' , 'megamaxmenu' ) . '<code>'.$s.'</code></small>';

			global $_wp_additional_image_sizes;

			$w = false;
			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
				$w = get_option( $s . '_size_w' );
				$h = get_option( $s . '_size_h' );
				$c = (bool) get_option( $s . '_crop' );
			}
			else if ( isset( $_wp_additional_image_sizes[ $s ] ) ) {

				$w = $_wp_additional_image_sizes[ $s ]['width'];
				$h = $_wp_additional_image_sizes[ $s ]['height'];
				$c = $_wp_additional_image_sizes[ $s ]['crop'];
			}
			if( $w ){
				 $desc = "($w &times; $h)" . ( $c ? ' [cropped]' : '' ).'<br/>' . $desc;
			}

			$o[$s] = array(
				'name'	=> $name,
				'desc'	=> $desc,
			);
		}

		foreach( $exclude as $ex ){
			unset( $o[$ex] );
		}

		$ops = array(
			'group'	=> $o,
		);

		return $ops;
}
//up( get_intermediate_image_sizes();
//full


/** AJAXY! **/

function megamaxmenu_menu_item_settings_nonce(){
	return wp_create_nonce( 'megamaxmenu-menu-item-settings' );
}

add_action( 'wp_ajax_megamaxmenu_save_menu_item', 'megamaxmenu_save_menu_item_callback' );

function megamaxmenu_save_menu_item_callback() {
	global $wpdb; // this is how you get access to the database

	//CHECK NONCE
	check_ajax_referer( 'megamaxmenu-menu-item-settings' , 'megamaxmenu_nonce' );

	$menu_item_id = sanitize_text_field($_POST['menu_item_id']);
	$menu_item_id = substr( $menu_item_id , 10 );

	$serialized_settings = _MEGAMAXMENU()->sanitize_data($_POST['settings'], '');
	$dirty_settings = array();
	parse_str( $serialized_settings, $dirty_settings );


	//CHECKBOXES
	//Since unchecked checkboxes won't be submitted, detect them and set the 'off' value
	$_defined_settings = megamaxmenu_menu_item_settings();
	foreach( $_defined_settings as $panel => $panel_settings ){
		foreach( $panel_settings as $_priority => $_setting ){
			if( $_setting['type'] == 'checkbox' ){
				$_id = $_setting['id'];
				if( !isset( $dirty_settings[$_id] ) ){
					$dirty_settings[$_id] = 'off';
				}
			}
		}
	}
//up( $dirty_settings );
//die();

	//ONLY ALLOW SETTINGS WE'VE DEFINED
	$settings = wp_parse_args( $dirty_settings, megamaxmenu_menu_item_setting_defaults() );

	//SAVE THE SETTINGS
	update_post_meta( $menu_item_id, MEGAMAXMENU_MENU_ITEM_META_KEY , $settings );

	//RUN CALLBACKS
	//Reset styles for this menu item here
	//megamaxmenu_reset_item_styles( $menu_item_id );

	foreach( $_defined_settings as $panel => $panel_settings ){
		foreach( $panel_settings as $_priority => $_setting ){
			if( isset( $_setting['on_save'] ) ){
				$callback = 'megamaxmenu_item_save_'.$_setting['on_save'];
				if( function_exists( $callback ) ){
					$callback( $menu_item_id , $_setting , $settings[$_setting['id']] , $settings );
				}
			}
		}
	}

	//Set Defaults
	if( isset( $dirty_settings['megamaxmenu-meta-save-defaults'] ) &&
		$dirty_settings['megamaxmenu-meta-save-defaults'] == 'on' ){
		update_option( MEGAMAXMENU_MENU_ITEM_DEFAULTS_OPTION_KEY , $settings );
	}

	//Reset Defaults
	if( isset( $dirty_settings['megamaxmenu-meta-reset-defaults'] ) &&
		$dirty_settings['megamaxmenu-meta-reset-defaults'] == 'on' ){
		delete_option( MEGAMAXMENU_MENU_ITEM_DEFAULTS_OPTION_KEY );
	}


	do_action( 'megamaxmenu_after_menu_item_save' , $menu_item_id );

	$response = array();

	$response['settings'] = $settings;
	$response['menu_item_id'] = $menu_item_id;

	//send back nonce
	$response['nonce'] = megamaxmenu_menu_item_settings_nonce();

	//print_r( $response );
	echo json_encode( $response );

	//echo $data;

	die(); // this is required to return a proper result
}


/** Helper Callbacks **/
function megamaxmenu_dp_category_ops(){
	return megamaxmenu_get_term_ops_by_tax( 'category' , array(
		'-1' 	=> 'Inherit Parent Category Item',
	));
}
function megamaxmenu_dp_tag_ops(){
	return megamaxmenu_get_term_ops_by_tax( 'post_tag' , array(
		'-1' 	=> 'Inherit Parent Tag Item',
	));
}
function megamaxmenu_dp_post_parent_ops(){
	return megamaxmenu_get_post_parent_ops( array(
		'0'		=> '[Top level posts only]',
		'-1' 	=> 'Inherit Parent Menu Item',
	));
}

function megamaxmenu_dp_custom_tax_ops( $args ){
	$tax = $args['tax'];
	return megamaxmenu_get_term_ops_by_tax( $args['tax_id'] , array(
		'-1' 	=> 'Inherit Parent '.$tax->label.' Item',
	));
}

function megamaxmenu_dt_parent_ops(){
	return megamaxmenu_get_term_ops( array(
		'-1' 	=> 'Automatic: Inherit Parent',
		'0'		=> '[Top Level Terms Only]'
	));
}

function megamaxmenu_dt_child_of_ops(){
	return megamaxmenu_get_term_ops( array(
		'-1' 	=> 'Automatic: Inherit Parent',
	));
}






function megamaxmenu_custom_menu_item_defaults( $defaults ){
	$cdefs = get_option( MEGAMAXMENU_MENU_ITEM_DEFAULTS_OPTION_KEY );
	if( $cdefs ){
		if( is_array( $cdefs ) ){
			foreach( $cdefs as $key => $val ){
				$defaults[$key] = $val;
			}
		}
	}
	return $defaults;
}
add_filter( 'megamaxmenu_menu_item_settings_defaults' , 'megamaxmenu_custom_menu_item_defaults' );
