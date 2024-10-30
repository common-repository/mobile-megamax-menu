<?php

add_filter( 'megamaxmenu_menu_item_settings_panels' , 'megamaxmenu_pro_item_settings_panels' );
function megamaxmenu_pro_item_settings_panels( $panels ){

	$panels['row'] = array(
		'title'	=> __( 'Row', 'megamaxmenu' ),
		'icon'	=> 'cog',
		'order'	=> 10,
	);

	$panels['dynamic_terms'] = array(
		'title'	=> __( 'Dynamic Terms', 'megamaxmenu' ),
		'icon'	=> 'sitemap',	//bolt // magic
		'info'	=> __( 'Filter settings for the terms to be displayed.  Dynamic items can only be placed in a submenu.' , 'megamaxmenu' ),
		'order'	=> 20,
	);

	$panels['dynamic_posts'] = array(
		'title'	=> __( 'Dynamic Posts', 'megamaxmenu' ),
		'icon'	=> 'sitemap',	//bolt // magic
		'info'	=> __( 'Filter settings for the posts to be displayed.  Dynamic items can only be placed in a submenu.' , 'megamaxmenu' ),
		'order'	=> 30,
	);



	$panels['tabs'] = array(
		'title'	=> __( 'Tabs', 'megamaxmenu' ),
		'icon'	=> 'columns',
		'info'	=> __( 'This is a wrapper item, which does not produce any content on its own.  Add child items to this Tabs item to create the Tab Toggles.  Add child items to those Toggles to create the Content Panels that are revealed when those toggles are activated.' , 'megamaxmenu' ),
		'order'	=> 40,
	);

	$panels['menu_segment'] = array(
		'title'	=> __( 'Menu Segment', 'megamaxmenu' ),
		'icon'	=> 'paperclip',
		'order'	=> 50,
		'warning' => __( 'Make sure not to nest a menu segment within the same menu, or you\'ll create an infinite recursion' , 'megamaxmenu' ),
	);

	//General

	$panels['icon']	= array(
		'title'	=> __( 'Icon' , 'megamaxmenu' ),
		'icon'	=> 'plus-circle',
		'order'	=> 110,
	);

	$panels['image'] = array(
		'title'	=> __( 'Image', 'megamaxmenu' ),
		'icon'	=> 'image',
		'info'	=> __( 'Set an image for this menu item and control its size' , 'megamaxmenu' ),
		'order'	=> 120
	);


	//Layout


	//Submenu

	$panels['custom_content'] = array(
		'title'	=> __( 'Custom Content' , 'megamaxmenu' ),
		'icon'	=> 'cogs',
		'order'	=> 160,
	);
	$panels['custom_content_layout'] = array(
		'title'	=> __( 'Layout' , 'megamaxmenu' ),
		'icon'	=> 'columns',
		'order'	=> 170,
	);

	$panels['widgets'] = array(
		'title'	=> __( 'Widgets' , 'megamaxmenu' ),
		'icon'	=> 'puzzle-piece',
		'info'	=> __( 'Insert a widget area into your menu item.  The content of the Widget Area (widgets) are added via the standard WordPress Widgets Panel' , 'megamaxmenu' ),
		'tip'	=> __( 'Choose either the Custom Widget Area or Reusable Widget Area setting, but not both' , 'megamaxmenu' ),
		'warning'	=> __( 'Remember, if you want a widget to appear in a submenu, it should be attached to a submenu item, not a top level item.' , 'megamaxmenu' ),
		'order'	=> 180,
	);

	$panels['widget_layout'] = array(
		'title'	=> __( 'Layout' , 'megamaxmenu' ),
		'icon'	=> 'columns',
		'order'	=> 190,
	);

	$panels['customize'] = array(
		'title'	=> __( 'Customize Style', 'megamaxmenu' ),
		'icon'	=> 'eye', //'pencil',
		'info'	=> __( 'Set the styles for this specific menu item.' , 'megamaxmenu' ),
		'tip'	=> __( 'To style the menu more generally, visit the ' , 'megamaxmenu' ) . ' <a target="_blank" href="'.admin_url( 'customize.php' ). '">Customizer</a>',
		'order'	=> 200,
	);

	$panels['customize_column'] = array(
		'title'	=> __( 'Customize Style', 'megamaxmenu' ),
		'icon'	=> 'eye', //'pencil',
		'info'	=> __( 'Set the styles for this specific column.' , 'megamaxmenu' ),
		'tip'	=> __( 'To style the menu more generally, visit the ' , 'megamaxmenu' ) . ' <a target="_blank" href="'.admin_url( 'customize.php' ). '">Customizer</a>',
		'order'	=> 201,
	);

	$panels['responsive'] = array(
		'title'	=> __( 'Responsive', 'megamaxmenu' ),
		'icon'	=> 'mobile-alt',
		'info'	=> __( 'Control the display of this item (and its children) on mobile vs. desktop.', 'megamaxmenu' ),
		'order'	=> 210,
	);

	//Deprecated

	return $panels;
}



add_filter( 'megamaxmenu_menu_item_settings_panels_map' , 'megamaxmenu_pro_item_settings_panels_map' );
function megamaxmenu_pro_item_settings_panels_map( $map ){

	foreach( $map as $id => $panels ){


	}

	return $map;
}


add_filter( 'megamaxmenu_menu_item_settings' , 'megamaxmenu_pro_item_settings' );
function megamaxmenu_pro_item_settings( $settings ){

	$column_ops = megamaxmenu_get_item_column_ops();
	$admin_img_assets = MEGAMAXMENU_URL . 'admin/assets/images/';


	/** DYNAMIC POSTS **/
	$settings['dynamic_posts'][10] = array(
		'id'	=> 'dp_posts_per_page',
		'title'	=> __( 'Limit' , 'megamaxmenu' ),
		'desc'	=> __( 'Maximum number of posts to return.  Set to -1 for unlimited.', 'megamaxmenu' ),
		'type'	=> 'text',
		'default' => -1,
	);


	$settings['dynamic_posts'][20] = array(
		'id'	=> 'dp_post_type',
		'title'	=> __( 'Post Type' , 'megamaxmenu' ),
		'desc'	=> __( 'Type of posts to return.', 'megamaxmenu' ),
		'type'	=> 'multicheck',
		'default' => array( 'post' , 'page' ), // '_all_on',	//can also take array of enabled
		'ops'	=> 'megamaxmenu_get_post_type_ops',
	);

	$settings['dynamic_posts'][30] = array(
		'id'	=> 'dp_category',
		'title'	=> __( 'Category' , 'megamaxmenu' ),
		'desc'	=> __( 'The category to return posts from.  To inherit from a parent, it must be a Category taxonomy menu item, or a Dynamic Term Item that has returned a Category.', 'megamaxmenu' ),
		'type'	=> 'autocomplete',
		'default' => '', // '_all_on',	//can also take array of enabled
		'ops'	=> 'megamaxmenu_dp_category_ops',
		'tip'	=> __( 'For performance purposes, the number of autocomplete results is limited to 100 by default.  You can adjust this in the MegaMaxMenu Control Panel > General Settings > Advanced Menu Items, or just enter the ID in the ID field.', 'megamaxmenu' ),
	);

	$settings['dynamic_posts'][40] = array(
		'id'	=> 'dp_tag',
		'title'	=> __( 'Tag' , 'megamaxmenu' ),
		'desc'	=> __( 'The tag to return posts from.  To inherit from a parent, it must be a Tag taxonomy menu item, or a Dynamic Term Item that has returned a Tag.', 'megamaxmenu' ),
		'type'	=> 'autocomplete',
		'default' => '', // '_all_on',	//can also take array of enabled
		'ops'	=> 'megamaxmenu_dp_tag_ops',
		'tip'	=> __( 'For performance purposes, the number of autocomplete results is limited to 100 by default.  You can adjust this in the MegaMaxMenu Control Panel > General Settings > Advanced Menu Items, or just enter the ID in the ID field.', 'megamaxmenu' ),
	);

	$taxonomies = get_taxonomies( array(
		'public'	=> true,
		'_builtin'	=> false,
		) , 'objects' );

	$taxonomy_weight = 41;

	foreach( $taxonomies as $tax_id => $tax ){
		$settings['dynamic_posts'][$taxonomy_weight++] = array(
			'id'	=> 'dp_'.$tax_id,
			'title'	=> $tax->labels->singular_name . '<br/><small>[' . $tax_id . ']</small>',
			'desc'	=> __( 'Custom taxonomy slug', 'megamaxmenu' ) . ': <strong>' . $tax_id.'</strong>',
			'type'	=> 'autocomplete',
			'default' 	=> '', // '_all_on',	//can also take array of enabled
			'ops'		=> 'megamaxmenu_dp_custom_tax_ops',
			'ops_args' 	=> array(
						'tax_id'	=> $tax_id,
						'tax'		=> $tax,
					),
			'tip' 	=> __( 'For performance purposes, the number of autocomplete results is limited to 100 by default.  You can adjust this in the MegaMaxMenu Control Panel > General Settings > Advanced Menu Items, or just enter the ID in the ID field.', 'megamaxmenu' ),
		);
	}


	$settings['dynamic_posts'][70] = array(
		'id'	=> 'dp_post_parent',
		'title'	=> __( 'Parent Post' , 'megamaxmenu' ),
		'desc'	=> __( 'Display children of this post from the post hierarchy.', 'megamaxmenu' ),
		'type'	=> 'autocomplete',
		'default' => '', // '_all_on',	//can also take array of enabled
		'ops'	=> 'megamaxmenu_dp_post_parent_ops',
		'tip'	=> __( 'For performance purposes, the number of autocomplete results is limited to 100 by default.  You can adjust this in the MegaMaxMenu Control Panel > General Settings > Advanced Menu Items, or just enter the ID in the ID field.', 'megamaxmenu' ),
	);


	$settings['dynamic_posts'][80] = array(
		'id'	=> 'dp_author',
		'title'	=> __( 'Post Author' , 'megamaxmenu' ),
		'desc'	=> __( 'Display posts by a specific author.  Check none to query all authors.', 'megamaxmenu' ),
		'type'	=> 'multicheck',
		'default' => '', // '_all_on',	//can also take array of enabled
		'ops'	=> 'megamaxmenu_get_author_ops',
	);


	//Exclude
	$settings['dynamic_posts'][85] = array(
		'id'	=> 'dp_exclude',
		'title'	=> __( 'Exclude' , 'megamaxmenu' ),
		'desc'	=> __( 'Comma separated list of post IDs to exclude.  For example, <em>25,67,152</em>', 'megamaxmenu' ),
		'type'	=> 'text',
		'default' => '',
	);



	$settings['dynamic_posts'][100] = array(
		'id'	=> 'dp_orderby',
		'title'	=> __( 'Sort by' , 'megamaxmenu' ),
		'desc'	=> __( 'Choose how to sort your post results', 'megamaxmenu' ),
		'type'	=> 'radio',
		'default' => 'title',
		'ops'	=> array(
			'group'	=> array(
				'ID'	=> array(
					'name'	=> __( 'Post ID', 'megamaxmenu' ),
				),
				'author'	=> array(
					'name'	=> __( 'Author', 'megamaxmenu' ),
				),
				'title'	=> array(
					'name'	=> __( 'Post Title', 'megamaxmenu' ),
				),
				'name'	=> array(
					'name'	=> __( 'Post Slug', 'megamaxmenu' ),
				),
				'date'	=> array(
					'name'	=> __( 'Publish Date', 'megamaxmenu' ),
				),
				'modified'	=> array(
					'name'	=> __( 'Last Modified Date', 'megamaxmenu' ),
				),
				'parent'	=> array(
					'name'	=> __( 'Parent', 'megamaxmenu' ),
				),
				'comment_count'	=> array(
					'name'	=> __( 'Comment Count', 'megamaxmenu' ),
				),
				'menu_order'	=> array(
					'name'	=> __( 'Page Order', 'megamaxmenu' ),
				),
				'none'	=> array(
					'name'	=> __( 'None', 'megamaxmenu' ),
				),
			)
		),

	);


	$settings['dynamic_posts'][110] = array(
		'id'	=> 'dp_order',
		'title'	=> __( 'Sort' , 'megamaxmenu' ),
		'desc'	=> __( 'Sort your items in normal order (ascending 1-10, A-Z) or reverse order (descending 10-1, Z-A)', 'megamaxmenu' ),
		'type'	=> 'radio',
		'default' => 'ASC',
		'ops'	=> array(
			'group'	=> array(
				'ASC'	=> array(
					'name'	=> __( 'Ascending', 'megamaxmenu' ),
				),
				'DESC'	=> array(
					'name'	=> __( 'Descending' , 'megamaxmenu' ),
				),
			)
		),

	);



	$settings['dynamic_posts'][120] = array(
		'id'	=> 'dp_autocolumns',
		'title'	=> __( 'Automatic Columns' , 'megamaxmenu' ),
		'desc'	=> __( 'Automatically divide posts evenly across columns.', 'megamaxmenu' ),
		'type'	=> 'radio',
		'ops'	=> array( 'group' => array(
			'disabled'	=> array(
				'name'	=> __( 'Disabled' , 'megamaxmenu' ),
			),
			1 	=> 1,
			2 	=> 2,
			3 	=> 3,
			4 	=> 4,
			5 	=> 5,
			6 	=> 6,
			7 	=> 7,
			8 	=> 8,
			9 	=> 9,
			10 	=> 10
		)),
		'default' => 'disabled',
	);


	$settings['dynamic_posts'][130] = array(
		'id'	=> 'dp_subcontent',
		'title'	=> __( 'Dynamic Subcontent' , 'megamaxmenu' ),
		'desc'	=> __( 'Subcontent will appear below the Dynamic Post title' , 'megamaxmenu' ),
		'type'	=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'ops'	=> 'megamaxmenu_get_dp_subcontent_ops',
		'default'	=> 'none',
	);


	$settings['dynamic_posts'][131] = array(
		'id'	=> 'dp_view_all',
		'title'	=> __( 'View All Link' , 'megamaxmenu' ),
		'desc'	=> __( 'Include a View All Link for the term based on this taxonomy.  (You must have selected a term above for this taxonomy)' , 'megamaxmenu' ),
		'type'	=> 'select',
		//'type_class'=> 'megamaxmenu-radio-blocks',
		'ops'	=> 'megamaxmenu_get_dp_view_all_ops',
		'default'	=> 'none',
	);

	$settings['dynamic_posts'][132] = array(
		'id'	=> 'dp_view_all_text',
		'title'	=> __( 'View All Link Text' , 'megamaxmenu' ),
		'desc'	=> __( 'If you are using a View All Link, you can override the default text here' , 'megamaxmenu' ),
		'type'	=> 'text',
		'default'	=> '',
	);


	$settings['dynamic_posts'][135] = array(
		'id'	=> 'empty_results_message',
		'title'	=> __( 'Empty Results Message' , 'megamaxmenu' ),
		'desc'	=> __( 'Optional message to display to users if there are 0 results returned from your query' , 'megamaxmenu' ),
		'type'	=> 'text',
		'default'	=> '',
	);
	$settings['dynamic_posts'][136] = array(
		'id'	=> 'dp_ignore_empty_results',
		'title'	=> __( 'Ignore Empty Results' , 'megamaxmenu' ),
		'desc'	=> __( 'If enabled, no "No Results Found" admin notice will be generated.' , 'megamaxmenu' ),
		'type'	=> 'checkbox',
		'default'	=> 'off',
	);






	/** DYNAMIC TERMS **/

	$settings['dynamic_terms'][10] = array(
		'id'	=> 'dt_taxonomy',
		'title'	=> __( 'Taxonomy' , 'megamaxmenu' ),
		'desc'	=> __( 'Check the taxonomies to pull terms from.  If you do not check any taxonomies, no terms will be displayed.  Hover over the taxonomy label to see the slug.', 'megamaxmenu' ),
		'type'	=> 'multicheck',
		'default' => '', //megamaxmenu_get_taxonomies(), // '_all_on',	//can also take array of enabled
		'ops'	=> 'megamaxmenu_get_taxonomy_ops',

	);

	//Number

	$settings['dynamic_terms'][20] = array(
		'id'	=> 'dt_number',
		'title'	=> __( 'Number' , 'megamaxmenu' ),
		'desc'	=> __( 'The maximum number of terms to display.  Blank for unlimited.', 'megamaxmenu' ),
		'type'	=> 'text',
		'default' => '',
	);

	//Parent -- include 'Item Parent'

	$settings['dynamic_terms'][30] = array(
		'id'	=> 'dt_parent',
		'title'	=> __( 'Parent Term' , 'megamaxmenu' ),
		'desc'	=> __( 'Select a term from the dropdown to use as a parent term.  Display direct children of this term only.' , 'megamaxmenu' ),
		'type'	=> 'autocomplete',
		'default' => '',
		'ops'	=> 'megamaxmenu_dt_parent_ops',
		'tip'	=> __( 'For performance purposes, the number of autocomplete results is limited to 100 by default.  You can adjust this in the MegaMaxMenu Control Panel > General Settings > Advanced Menu Items, or just enter the ID in the ID field.', 'megamaxmenu' ),
	);

	//Child of
	$settings['dynamic_terms'][40] = array(
		'id'	=> 'dt_child_of',
		'title'	=> __( 'Ancestor Term' , 'megamaxmenu' ),
		'desc'	=> __( 'Select a term from the dropdown to use as an ancestor term.  All descendants of this term well be displayed (child_of).  Overridden if Parent is set.' , 'megamaxmenu' ),
		'type'	=> 'autocomplete',
		'default' => '',
		'ops'	=> 'megamaxmenu_dt_child_of_ops',
		'tip'	=> __( 'For performance purposes, the number of autocomplete results is limited to 100 by default.  You can adjust this in the MegaMaxMenu Control Panel > General Settings > Advanced Menu Items, or just enter the ID in the ID field.', 'megamaxmenu' ),
	);

	//Exclude
	$settings['dynamic_terms'][45] = array(
		'id'	=> 'dt_exclude',
		'title'	=> __( 'Exclude' , 'megamaxmenu' ),
		'desc'	=> __( 'Comma separated list of term IDs to exclude.  For example, <em>25,67,152</em>', 'megamaxmenu' ),
		'type'	=> 'text',
		'default' => '',
	);

	//Exclude Tree
	// $settings['dynamic_terms'][46] = array(
	// 	'id'	=> 'dt_exclude_tree',
	// 	'title'	=> __( 'Exclude Tree' , 'megamaxmenu' ),
	// 	'desc'	=> __( 'Comma separated list of term IDs to exclude.  All descendants (subcategories) of these terms will also be excluded.  For example, <em>25,67,152</em>', 'megamaxmenu' ),
	// 	'type'	=> 'text',
	// 	'default' => '',
	// );


	//Sort
	$settings['dynamic_terms'][50] = array(
		'id'	=> 'dt_orderby',
		'title'	=> __( 'Sort by' , 'megamaxmenu' ),
		'desc'	=> __( 'Choose how to sort your terms results', 'megamaxmenu' ),
		'type'	=> 'radio',
		'default' => 'name',
		'ops'	=> array(
			'group'	=> array(
				'id'	=> array(
					'name'	=> __( 'Term ID', 'megamaxmenu' ),
				),
				'count'	=> array(
					'name'	=> __( 'Count', 'megamaxmenu' ),
				),
				'name'	=> array(
					'name'	=> __( 'Term Name', 'megamaxmenu' ),
				),
				'slug'	=> array(
					'name'	=> __( 'Term Slug', 'megamaxmenu' ),
				),
				'term_order' => array(
					'name'	=> __( 'Term Order' , 'megamaxmenu' ),
				),
				'none'	=> array(
					'name'	=> __( 'None', 'megamaxmenu' ),
				),
			)
		),

	);


	$settings['dynamic_terms'][60] = array(
		'id'	=> 'dt_order',
		'title'	=> __( 'Sort' , 'megamaxmenu' ),
		'desc'	=> __( 'Sort your items in normal order (ascending 1-10, A-Z) or reverse order (descending 10-1, Z-A)', 'megamaxmenu' ),
		'type'	=> 'radio',
		'default' => 'ASC',
		'ops'	=> array(
			'group'	=> array(
				'ASC'	=> array(
					'name'	=> __( 'Ascending', 'megamaxmenu' ),
				),
				'DESC'	=> array(
					'name'	=> __( 'Descending' , 'megamaxmenu' ),
				),
			)
		),

	);

	//Hide Empty
	$settings['dynamic_terms'][80] = array(
		'id'	=> 'dt_hide_empty',
		'title'	=> __( 'Hide Empty' , 'megamaxmenu' ),
		'desc'	=> __( 'Do not display terms that don\'t have any associated posts', 'megamaxmenu' ),
		'type'	=> 'checkbox',
		'default' => 'off',
	);


	//Hierarchical

	$settings['dynamic_terms'][90] = array(
		'id'	=> 'dt_hierarchical',
		'title'	=> __( 'Hierarchical' , 'megamaxmenu' ),
		'desc'	=> __( 'Display terms that have non-empty descendants (even if Hide Empty is enabled).', 'megamaxmenu' ),
		'type'	=> 'checkbox',
		'default' => 'on',
	);



	$settings['dynamic_terms'][100] = array(
		'id'	=> 'dt_autocolumns',
		'title'	=> __( 'Automatic Columns' , 'megamaxmenu' ),
		'desc'	=> __( 'Automatically divide terms evenly across columns.', 'megamaxmenu' ),
		'type'	=> 'radio',
		'ops'	=> array( 'group' => array(
			'disabled'	=> array(
				'name'	=> __( 'Disabled' , 'megamaxmenu' ),
			),
			1 	=> 1,
			2 	=> 2,
			3 	=> 3,
			4 	=> 4,
			5 	=> 5,
			6 	=> 6,
			7 	=> 7,
			8 	=> 8,
			9 	=> 9,
			10 	=> 10
		)),
		'default' => 'disabled',
	);


	$settings['dynamic_terms'][120] = array(
		'id'	=> 'dt_display_term_counts',
		'title'	=> __( 'Display Term Counts' , 'megamaxmenu' ),
		'desc'	=> __( 'Display the number of posts with this term after the term name.' , 'megamaxmenu' ),
		'type'	=> 'checkbox',
		'default' => 'off',
	);

	$settings['dynamic_terms'][125] = array(
		'id'	=> 'dt_display_term_description',
		'title'	=> __( 'Display Term Descriptions' , 'megamaxmenu' ),
		'desc'	=> __( 'Display the description for each term (set in the term edit screen).' , 'megamaxmenu' ),
		'type'	=> 'checkbox',
		'default' => 'off',
	);





	$settings['dynamic_terms'][131] = array(
		'id'	=> 'dt_view_all',
		'title'	=> __( 'View All Link' , 'megamaxmenu' ),
		'desc'	=> __( 'Include a View All Link for the term based on this taxonomy.  (You must have selected a parent or child term above for this taxonomy)' , 'megamaxmenu' ),
		'type'	=> 'select',
		//'type_class'=> 'megamaxmenu-radio-blocks',
		'ops'	=> 'megamaxmenu_get_dp_view_all_ops',
		'default'	=> 'none',
	);

	$settings['dynamic_terms'][132] = array(
		'id'	=> 'dt_view_all_text',
		'title'	=> __( 'View All Link Text' , 'megamaxmenu' ),
		'desc'	=> __( 'If you are using a View All Link, you can override the default text here' , 'megamaxmenu' ),
		'type'	=> 'text',
		'default'	=> '',
	);

	$settings['dynamic_terms'][135] = array(
		'id'	=> 'empty_results_message',
		'title'	=> __( 'Empty Results Message' , 'megamaxmenu' ),
		'desc'	=> __( 'Optional message to display to users if there are 0 results returned from your query' , 'megamaxmenu' ),
		'type'	=> 'text',
		'default'	=> '',
	);

	$settings['dynamic_terms'][136] = array(
		'id'	=> 'dt_ignore_empty_results',
		'title'	=> __( 'Ignore Empty Results' , 'megamaxmenu' ),
		'desc'	=> __( 'If enabled, no "No Results Found" admin notice will be generated.' , 'megamaxmenu' ),
		'type'	=> 'checkbox',
		'default'	=> 'off',
	);





	/** WIDGETS **/

	$settings['widgets'][10] = array(
		'id' 		=> 'auto_widget_area',
		'title'		=> __( 'Custom Widget Area' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'Enter a name for your Widget Area, and a widget area specifically for this menu item will be automatically be created in the ' , 'megamaxmenu' ) . '<a target="_blank" href="'.admin_url('widgets.php').'">'.__( 'Widgets Screen' , 'megamaxmenu' ).'</a>',
		'tip'		=> __( 'This is generally the simplest way to add widgets, assuming you don\'t intend to reuse this widget area' , 'megamaxmenu' ),
		'on_save'	=> 'create_auto_widget_area'
	);

	$settings['widgets'][20] = array(
		'id' 		=> 'widget_area',
		'title'		=> __( 'Reusable Widget Area' , 'megamaxmenu' ),
		'type'		=> 'select',
		'default' 	=> '',
		'ops'		=> 'megamaxmenu_get_widget_area_ops',
		'desc'		=> '<ul class="megamaxmenu-desc-list"><li>'.__( 'Select a reusable widget area to display in this menu item.  This will be overridden if you use the "Custom Widget Area" setting and assign a widget to it.' , 'megamaxmenu' ). ' </li>' .
						'<li>'.__( 'Create reusable widget areas via the ' , 'megamaxmenu' ) . '<a target="_blank" href="'.admin_url('themes.php?page=megamaxmenu-settings#megamaxmenu_general').'">MegaMaxMenu Control Panel</a>. </li>' .
						'<li>'.__( 'Assign widgets via the ' , 'megamaxmenu' ) . '<a target="_blank" href="'.admin_url('widgets.php').'">'.__( 'Widgets Screen' , 'megamaxmenu' ).'</a> </li>'.
						'</ul>',
	);

	$settings['widgets'][30] = array(
		'id' 		=> 'widget_area_columns',
		'title'		=> __( 'Widget Area Columns', 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'auto',
		'desc'		=> __( 'Automatic will divide the widgets into even columns.  So 5 widgets will produce 5 columns.' , 'megamaxmenu' ),
		'ops'		=> array(
			'group'	=> array(
				'auto'	=> array(
					'name' => __( 'Automatic' , 'megamaxmenu' ),
				),
				1 		=> array( 'name' => 1 ),
				2 		=> array( 'name' => 2 ),
				3 		=> array( 'name' => 3 ),
				4 		=> array( 'name' => 4 ),
				5 		=> array( 'name' => 5 ),
				6 		=> array( 'name' => 6 ),
				7 		=> array( 'name' => 7 ),
				8 		=> array( 'name' => 8 ),
				9 		=> array( 'name' => 9 ),
				10 		=> array( 'name' => 10 ),

			),
		),
	);

	/** WIDGET LAYOUT **/
	$settings['widget_layout'][10] = array(
		'id' 		=> 'columns',
		'title'		=> __( 'Columns Width', 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'auto',
		'desc'		=> __( 'This is the fraction of the submenu width that the widget area will occupy.  Auto will be full width by default.' , 'megamaxmenu' ),
		'ops'		=> $column_ops
	);


	$settings['widget_layout'][30] = array(
		'id' 		=> 'item_align',
		'title'		=> __( 'Alignment', 'megamaxmenu' ),
		'type'		=> 'select',
		'default' 	=> 'auto',
		'desc'		=> __( 'Alignment of this menu item within the menu bar' , 'megamaxmenu' ),
		'ops'		=> array(
			'auto'	=> __( 'Automatic' , 'megamaxmenu' ),
			'left'	=> __( 'Left' , 'megamaxmenu' ),
			'right'	=> __( 'Right', 'megamaxmenu' ),
		),
		'scenario'	=> __( 'Top Level Menu Items' , 'megamaxmenu' ),

	);






	/** ROW (core) **/






	/** GENERAL **/

	$settings['general'][30] = array(
		'id' 		=> 'item_align',
		'title'		=> __( 'Alignment', 'megamaxmenu' ),
		'type'		=> 'select',
		'default' 	=> 'auto',
		'desc'		=> __( 'Alignment of this menu item within the menu bar' , 'megamaxmenu' ),
		'ops'		=> array(
			'auto'	=> __( 'Automatic' , 'megamaxmenu' ),
			'left'	=> __( 'Left' , 'megamaxmenu' ),
			'right'	=> __( 'Right', 'megamaxmenu' ),
		),
		'scenario'	=> __( 'Top Level Menu Items' , 'megamaxmenu' ),

	);

	$settings['general'][35] = array(
		'id' 		=> 'mini_item',
		'title'		=> __( 'Mini Item', 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Makes the item narrower padding-wise than other items.  Useful for grouping icons.' , 'megamaxmenu' ),
		'scenario'	=> __( 'Top Level Menu Items' , 'megamaxmenu' ),
	);

	/*
	$settings['general'][40] = array(
		'id' 		=> 'button',
		'title'		=> __( 'Button Link', 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'off',
		'desc'		=> __( '', 'megamaxmenu' ),
		'ops'		=> megamaxmenu_get_item_button_ops()
	);
	*/

	$settings['general'][45] = array(
		'id' 		=> 'custom_url',
		'title'		=> 'Custom URL Override',
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'Override this item\'s URL.  Shortcodes in this field will be processed.' , 'megamaxmenu' ),
	);

	$settings['general'][50] = array(
		'id' 		=> 'scrollto',
		'title'		=> 'Scroll To',
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'The selector for an item to scroll to when clicked, if present.  Example: <code>#section-1</code>' , 'megamaxmenu' ),
	);

	$settings['general'][60] = array(
		'id' 		=> 'no_wrap',
		'title'		=> 'Don\'t wrap title/label text',
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Prevent the text from wrapping to a new line.' , 'megamaxmenu' ),
	);

	$settings['general'][70] = array(
		'id' 		=> 'item_trigger',
		'title'		=> __( 'Trigger Override', 'megamaxmenu' ),
		'type'		=> 'select',
		'default' 	=> 'auto',
		'desc'		=> __( 'Switch to something other than "Automatic" to trigger this item\'s submenu differently from other items.  The default Trigger for the menu can be configured in the Menu Settings.' , 'megamaxmenu' ),
		'ops'		=> array(
			'auto'	=> __( 'Automatic' , 'megamaxmenu' ),
			'hover'	=> __( 'Hover' , 'megamaxmenu' ),
			'hover_intent'	=> __( 'Hover Intent' , 'megamaxmenu' ),
			'click'	=> __( 'Click', 'megamaxmenu' ),
		),

	);

	$settings['general'][80] = array(
		'id' 		=> 'disable_submenu_indicator',
		'title'		=> __( 'Disable Submenu Indicator (Arrow)', 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Useful for items like a search dropdown.' , 'megamaxmenu' ),
	);

	$settings['general'][85] = array(
		'id' 		=> 'disable_current',
		'title'		=> __( 'Disable Current', 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Disable the current menu item classes for this item' , 'megamaxmenu' ),
	);

	$settings['general'][90] = array(
		'id' 		=> 'target_class',
		'title'		=> 'Anchor Class',
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'A class to be applied to the target/anchor/span element.' , 'megamaxmenu' ),
	);


	$settings['general'][100] = array(
		'id' 		=> 'target_id',
		'title'		=> 'Anchor ID',
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'An ID to be applied to the target/anchor/span element.' , 'megamaxmenu' ),
	);



	/** ICONS **/
	$icon_desc = __( 'Select a font icon to use with this item.' , 'megamaxmenu' );
	$settings['icon'][10] = array(
		'id' 		=> 'icon',
		'title'		=> __( 'Icon', 'megamaxmenu' ),
		'type'		=> 'icon',
		'default' 	=> '',
		'desc'		=> $icon_desc,
		'ops'		=> 'megamaxmenu_get_icon_ops'
	);
	$settings['icon'][11] = array(
		'id' 		=> 'icon_title',
		'title'		=> __( 'Icon Title', 'megamaxmenu' ),
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'This setting is for accessibility.  If your icon is purely decorative, leave this blank.  If your icon is semantic - especially if it is not accompanied by other text - it is important to set this field for screen readers.' , 'megamaxmenu' ),
	);

	/*
	$settings['icon'][20] = array(
		'id' 		=> 'icon_width',
		'title'		=> 'Icon Width',
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( '', 'Leave blank to use the default icon width' ),
	);
	*/



	/** LAYOUTS **/





	/** IMAGES **/


	$settings['image'][10] = array(
		'id'		=> 'item_image',
		'title'		=> __( 'Image' , 'megamaxmenu' ),
		'desc'		=> __( 'Click "Select" to upload or choose a new image.  Click "Remove" to remove the image.  Click "Edit" to edit the currently selected image. For Dynamic Posts, this image is the optional fallback for when a Post does not have a featured image.' , 'megamaxmenu' ),
		'type'		=> 'media',
		'default'	=> '',

	);

	$settings['image'][15] = array(
		'id'		=> 'inherit_featured_image',
		'title'		=> __( 'Inherit Featured Image' , 'megamaxmenu' ),
		'desc'		=> __( 'For Post Menu Items, automatically inherit the Post\'s featured image for this item.' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'ops'		=> array(
			'group'	=> array(
				'off'	=> array(
					'name'	=> __( 'Disabled' , 'megamaxmenu' ),
					'desc'	=> __( 'Do not inherit the image' , 'megamaxmenu' )
				),
				'cache'	=> array(
					'name'	=> __( 'Assign Featured Image on Save' , 'megamaxmenu' ),
					'desc'	=> __( '[More efficient] When this item is saved, the current featured image from the post will be assigned.  It will not be updated until you save this item again.', 'megamaxmenu' )
				),
				'on'	=> array(
					'name'	=> __( 'Dynamically Inherit' , 'megamaxmenu' ),
					'desc'	=> __( '[Less efficient] Each time the menu item is displayed, dynamically find the item\'s featured image.' , 'megamaxmenu' )
				),
			)
		),
		'default'	=> 'off',
		'scenario'	=> __( 'Page/Post Menu Items' , 'megamaxmenu' ),
		'on_save'	=> 'inherit_featured_image',

	);

	$settings['image'][20] = array(
		'id'		=> 'image_size',
		'title'		=> __( 'Image Size' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'default'	=> 'inherit',
		'ops'		=> 'megamaxmenu_get_image_size_ops',
		'desc'		=> __( 'This is the size of the actual file that will be served.  You can choose from any registered image size in your setup.  You can set a default to be inherited globally in the Control Panel.' , 'megamaxmenu' ),
	);


	$settings['image'][30] = array(
		'id'		=> 'image_dimensions',
		'title'		=> __( 'Image Dimensions' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'default'	=> 'inherit',
		'ops'		=> array(
			'group'	=> array(
				'inherit'	=> array(
					'name'	=> __( 'Inherit' , 'megamaxmenu' ),
					'desc'	=> __( 'Inherit settings from the menu instance settings' , 'megamaxmenu' )
				),
				'natural'	=> array(
					'name'	=> __( 'Natural' , 'megamaxmenu' ),
					'desc'	=> __( 'Display image at natural dimensions' , 'megamaxmenu' )
				),
				'custom'	=> array(
					'name'	=> __( 'Custom' , 'megamaxmenu' ),
					'desc'	=> __( 'Use a custom size, defined below' )
				),
			)
		),
		'on_save'	=> 'image_dimensions',
	);

	$settings['image'][40] = array(
		'id'		=> 'image_width_custom',
		'title'		=> __( 'Custom Image Width' , 'megamaxmenu' ),
		'desc'		=> __( 'Image width attribute (px).  Do not include units.  Only valid if "Image Dimensions" is set to "Custom" above.' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default'	=> '',
		'on_save'	=> 'image_width_custom',
	);

	$settings['image'][50] = array(
		'id'		=> 'image_height_custom',
		'title'		=> __( 'Custom Image Height' , 'megamaxmenu' ),
		'desc'		=> __( 'Image height attribute (px).  Do not include units.  Only valid if "Image Dimensions" is set to "Custom" above.  Leave blank to maintain aspect ratio.' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default'	=> '',
	);

	$settings['image'][55] = array(
		'id'		=> 'image_text_top_padding',
		'title'		=> __( 'Image Text Top Padding' , 'megamaxmenu' ),
		'desc'		=> __( 'The top padding for the accompanying text when Image Left or Image Right layouts are displayed.  This allows control over the vertical alignment of the text relative to the image.', 'megamaxmenu' ),
		'type'		=> 'text',
		'default'	=> '',
		'on_save'	=> 'image_text_top_padding',
	);

	$settings['image'][60] = array(
		'id'		=> 'disable_padding',
		'title'		=> __( 'Disable Item Padding' , 'megamaxmenu' ),
		'desc'		=> __( 'Disable the padding on this item.  Useful for image-only menu items where the image should extend to the extents of the item.' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
	);







	/** RESPONSIVE **/

	$settings['responsive'][10]	= array(
		'id'		=> 'hide_on_mobile',
		'title'		=> __( 'Hide below breakpoint' , 'megamaxmenu' ),
		'desc'		=> __( 'Hides this item below the responsive breakpoint via CSS.' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
	);

	$settings['responsive'][20]	= array(
		'id'		=> 'hide_on_desktop',
		'title'		=> __( 'Hide above breakpoint' , 'megamaxmenu' ),
		'desc'		=> __( 'Hides this item above the responsive breakpoint via CSS.' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
	);

	$settings['responsive'][30]	= array(
		'id'		=> 'disable_on_mobile',
		'title'		=> __( 'Disable on mobile' , 'megamaxmenu' ),
		'desc'		=> __( 'Removes this item when mobile device is detected via wp_is_mobile().' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
		'tip'		=> __( 'Please note that if you are using server-side caching, you will need to configure it properly to support a separate mobile cache for this setting to work' , 'megamaxmenu' ),
	);

	$settings['responsive'][40]	= array(
		'id'		=> 'disable_on_desktop',
		'title'		=> __( 'Disable on desktop' , 'megamaxmenu' ),
		'desc'		=> __( 'Removes this item when mobile device is NOT detected via wp_is_mobile().' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
		'tip'		=> __( 'Please note that if you are using server-side caching, you will need to configure it properly to support a separate mobile cache for this setting to work' , 'megamaxmenu' ),
	);

	$settings['responsive'][50]	= array(
		'id'		=> 'disable_submenu_on_mobile',
		'title'		=> __( 'Disable submenu on mobile' , 'megamaxmenu' ),
		'desc'		=> __( 'Removes this item\'s submenu when mobile device is detected via wp_is_mobile().' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
		'tip'		=> __( 'Please note that if you are using server-side caching, you will need to configure it properly to support a separate mobile cache for this setting to work' , 'megamaxmenu' ),
	);






	/** SUBMENU **/

	$settings['submenu'][65] = array(
		'id'		=> 'submenu_autocolumns',
		'title'		=> __( 'Submenu Automatic Columns' , 'megamaxmenu' ),
		'desc'		=> __( 'Automatically divide posts evenly across columns, organized vertically - top to bottom.  Useful for creating columns without headers when all submenu items are second level items.  Leave this disabled if you are using Column Headers (second and third level items), or if you want to use uneven columns, in which case you can use advanced [Column] items to organize your menu.', 'megamaxmenu' ),
		'tip'		=> __( 'How does this differ from "Submenu Column Default"? If you set "Submenu Automatic Columns" to 5, it\'ll create 5 even submenu columns and automatically divide the child items of this item into those columns, organized top to bottom.  If you set "Submenu Column Default" to 1/5, each child item will be 20% (1/5) of the submenu width and be organized left to right.  In practice, you\'d use "Submenu Automtic Columns" for submenus without column headers, and the "Submenu Column Default" setting for submenus with column headers', 'megamaxmenu' ),
		'type'		=> 'radio',
		'ops'		=> array( 'group' => array(
						'disabled'	=> array(
							'name'	=> __( 'Disabled' , 'megamaxmenu' ),
						),
						1 	=> 1,
						2 	=> 2,
						3 	=> 3,
						4 	=> 4,
						5 	=> 5,
						6 	=> 6,
						7 	=> 7,
						8 	=> 8,
						9 	=> 9,
						10 	=> 10
					)),
		'default' => 'disabled',

	);

	$settings['submenu'][67] = array(
		'id' 		=> 'submenu_item_layout',
		'title'		=> __( 'Submenu Item Layout', 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'default' 	=> 'default',
		'desc'		=> __( 'This will set the default Item Layout for any children of this item.  Useful if all your children will be uniform in layout.' , 'megamaxmenu' ),
		'ops'		=> 'megamaxmenu_get_item_layout_ops'
	);

	$settings['submenu'][68] = array(
		'id' 		=> 'submenu_item_content_alignment',
		'title'		=> __( 'Submenu Item Content Alignment', 'megamaxmenu' ),
		'type'		=> 'radio',
		'scenario'	=> 'Vertically stacked layouts',
		'default' 	=> 'default',
		'desc'		=> __( 'This setting can be inherited by children of this item for the "Item Content Alignment" setting.  For example, setting this to "Center" will allow you to have a centered image stacked above the title on your submenu items when used in conjunction with the "Image Above" item layout' , 'megamaxmenu' ),
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

	$settings['submenu'][70] = array(
		'id'		=> 'submenu_column_dividers',
		'title'		=> __( 'Submenu Columns Dividers' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> __( 'The color of the border between each submenu column.  Note this only affects columns that are children of this item, not further descendants', 'megamaxmenu' ),
		'scenario'	=> __( 'Mega Submenu' , 'megamaxmenu' ),
		'tip'		=> __( 'You will likely want to set a Minimum Height below as well.' , 'megamaxmenu' ),
		'on_save'	=> 'submenu_column_divider_color'
	);
	$settings['submenu'][71] = array(
		'id'		=> 'submenu_column_min_height',
		'title'		=> __( 'Submenu Columns Minimum Height' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default'	=> '',
		'desc'		=> __( 'Useful when using the Submenu Columns Dividers setting', 'megamaxmenu' ),
		'scenario'	=> __( 'Mega Submenu' , 'megamaxmenu' ),
		'on_save'	=> 'submenu_column_min_height'
	);

	$settings['submenu'][75] = array(
		'id' 		=> 'submenu_grid',
		'title'		=> __( 'Grid Submenu' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'scenario'	=> __( 'Mega Submenus' , 'megamaxmenu' ),
		'desc'		=> __( 'Useful for image grids.  Makes the space between the targets equal to the space on the submenu edges.' , 'megamaxmenu' ),
	);

	$settings['submenu'][90] = array(
		'id' 		=> 'show_current',
		'title'		=> __( 'Show submenu when current' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Show this submenu on page load by default when its parent is current.  Only relevant for submenu types that are hidden by default.' , 'megamaxmenu' ),
	);

	$settings['submenu'][95] = array(
		'id' 		=> 'show_default',
		'title'		=> __( 'Show submenu by default' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Show this submenu on page load by default on every page.  Only relevant for submenu types that are hidden by default.' , 'megamaxmenu' ),
	);


	$settings['submenu'][100] = array(
		'id' 		=> 'submenu_background_image',
		'title'		=> __( 'Submenu Background Image' , 'megamaxmenu' ),
		'type'		=> 'media',
		'default' 	=> '',
		'desc'		=> __( '' , 'megamaxmenu' ),
		'on_save'	=> 'submenu_background_image',
		'scenario'	=> __( 'Mega Submenus' , 'megamaxmenu' ),
	);

	$settings['submenu'][110] = array(
		'id' 		=> 'submenu_background_image_repeat',
		'title'		=> __( 'Repeat Background Image' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'no-repeat',
		'ops'		=> array(
			'group'	=> array(
				'no-repeat'	=> array(
					'name'	=> __( 'No Repeat' , 'megamaxmenu' ),
				),
				'repeat'	=> array(
					'name'	=> __( 'Repeat' , 'megamaxmenu' ),
				),
				'repeat-x'	=> array(
					'name'	=> __( 'Repeat X (Horizontal)' , 'megamaxmenu' ),
				),
				'repeat-y'	=> array(
					'name'	=> __( 'Repeat Y (Vertical)' , 'megamaxmenu' ),
				),
				'space'	=> array(
					'name'	=> __( 'Space' , 'megamaxmenu' ),
				),
				'round'	=> array(
					'name'	=> __( 'Round' , 'megamaxmenu' ),
				),

			),
		),
		'desc'		=> __( '' , 'megamaxmenu' ),
	);

	$settings['submenu'][120] = array(
		'id' 		=> 'submenu_background_position',
		'title'		=> __( 'Background Position' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default' 	=> 'bottom right',
		'desc'		=> __( '' , 'megamaxmenu' ),
	);

	$settings['submenu'][130] = array(
		'id' 		=> 'submenu_background_size',
		'title'		=> __( 'Background Size' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default' 	=> 'auto',
		'desc'		=> __( 'Try "cover" to cover the entire submenu.' , 'megamaxmenu' ),
	);

	$settings['submenu'][140] = array(
		'id' 		=> 'submenu_padding',
		'title'		=> __( 'Submenu Padding' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> '<code>0 0 50px 0</code> '.__( 'would add 50px of padding to the bottom of the submenu.' , 'megamaxmenu' ),
		'on_save'	=> 'submenu_padding',
		'scenario'	=> __( 'Mega Submenus' , 'megamaxmenu' ),
	);

	$settings['submenu'][150] = array(
		'id' 		=> 'submenu_footer_content',
		'title'		=> __( 'Submenu Footer Content' , 'megamaxmenu' ),
		'type'		=> 'textarea',
		'default' 	=> '',
		'desc'		=> __( 'Add HTML to the footer of the menu.' , 'megamaxmenu' ),
		'scenario'	=> __( 'Mega Submenus' , 'megamaxmenu' ),
	);

	/*
	$settings['submenu'][140] = array(
		'id' 		=> 'submenu_footer_content_align',
		'title'		=> __( 'Background Size' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default' 	=> 'auto',
		'desc'		=> __( '' , 'megamaxmenu' ),
	);
	*/




	/** TABS **/

	$settings['tabs'][10] = array(
		'id'		=> 'tab_layout',
		'title'		=> __( 'Tab Layout', 'megamaxmenu' ),
		'type'		=> 'radio',
		'type_class'=> 'megamaxmenu-radio-blocks',
		'default'	=> 'left',
		'ops'		=> array(
						'group'	=> array(
							'left'	=> array(
								'name'	=> __( 'Left' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'Tabs_left.png',
								'desc'	=> __( 'Tabs on the left, content panels on the right' , 'megamaxmenu' ),
							),
							'right'	=> array(
								'name'	=> __( 'Right' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'Tabs_right.png',
								'desc'	=> __( 'Tabs on the right, content panels on the left' , 'megamaxmenu' ),
							),
							'top'	=> array(
								'name'	=> __( 'Top' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'Tabs_top.png',
								'desc'	=> __( 'Tabs on the top, content panels on the bottom' , 'megamaxmenu' ),
							),
							'bottom'	=> array(
								'name'	=> __( 'Bottom' , 'megamaxmenu' ),
								'img'	=> $admin_img_assets.'Tabs_bottom.png',
								'desc'	=> __( 'Tabs on the bottom, content panels on the top' , 'megamaxmenu' ),
							),
						)
		),


	);

	$settings['tabs'][15] = array(
		'id' 		=> 'tab_block_columns',
		'title'		=> __( 'Tab Block Width', 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'full',
		'desc'		=> __( 'This is the width of the whole tab block.  In the majority of cases, you will leave this set to "Full Width"' , 'megamaxmenu' ),
		'ops'		=> $column_ops,
		'cue'		=> '<img src="'.$admin_img_assets.'TabsDiagram_block.png" />',
	);

	$settings['tabs'][20] = array(
		'id' 		=> 'tabs_group_layout',
		'title'		=> __( 'Tabs Toggles Layout Width', 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'auto',
		'desc'		=> __( 'The width of the tab toggles within the tab block.  If using Tab Layouts "Top" or "Bottom", set this to Full.' , 'megamaxmenu' ),
		'ops'		=> $column_ops,
		'tip'		=> __( 'If your Tab Layout is Left or Right, you\'ll want this to complement your Panels Layout Width.  If your Tab Layout is Top or Bottom, you\'ll want this to be set to Full', 'megamaxmenu' ),
		'cue'		=> '<img src="'.$admin_img_assets.'TabsDiagram_tabs.png" /> '.
						'<img src="'.$admin_img_assets.'TabsDiagram_tabs_top.png" />',
	);

	$settings['tabs'][30] = array(
		'id' 		=> 'panels_group_layout',
		'title'		=> __( 'Panels Layout Width' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'auto',
		'desc'		=> __( 'The width of the content panels within the tab block.   If using Tab Layouts "Top" or "Bottom", set this to Full.' , 'megamaxmenu' ),
		'ops'		=> $column_ops,
		'tip'		=> __( 'If your Tab Layout is Left or Right, you\'ll want this to complement your Tabs Toggles Layout Width.  If your Tab Layout is Top or Bottom, you\'ll want this to be set to Full', 'megamaxmenu' ),
		'cue'		=> '<img src="'.$admin_img_assets.'TabsDiagram_panels.png" /> '.
						'<img src="'.$admin_img_assets.'TabsDiagram_panels_bottom.png" />',
	);

	$settings['tabs'][33] = array(
		'id' 		=> 'submenu_column_default',
		'title'		=> __( 'Panels Columns Default' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'default'	=> 'auto',
		'desc'		=> __( 'The number of columns per row that the content panel should be broken into by default.  Can be overridden on individual items', 'megamaxmenu' ),
		'ops'		=> $column_ops,
	);



	$settings['tabs'][34] = array(
		'id' 		=> 'panels_grid',
		'title'		=> __( 'Grid Panels' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Useful for image grids in the Tab Content Panel.  Makes the space between the targets equal to the space on the content panel edges.' , 'megamaxmenu' ),
	);

	$settings['tabs'][35] = array(
		'id' 		=> 'panels_padding',
		'title'		=> __( 'Panel Padding' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'Add padding to panels.  Useful if you need to make the spacing at the edges of a row equal to that between the columns (by doubling it).' , 'megamaxmenu' ),
		'on_save'	=> 'panels_padding',
	);

	/*
	$settings['tabs'][35] = array(
		'id' 		=> 'panels_padded',
		'title'		=> __( 'Pad Panels' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Add padding to panels (doubles the edge gutters).  Useful if you need to make the spacing at the edges of a row equal to that between the columns.' , 'megamaxmenu' ),
	);
	*/


	$settings['tabs'][40] = array(
		'id' 		=> 'show_default_panel',
		'title'		=> __( 'Show Default Panel' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'on',
		'desc'		=> __( 'Show the first tab\'s panel by default.  Otherwise a blank area will display.' , 'megamaxmenu' ),
	);
	$settings['tabs'][41] = array(
		'id' 		=> 'show_current_panel',
		'title'		=> __( 'Show Current Panel' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'on',
		'desc'		=> __( 'If there is a current item in the tabs block, display that panel by default.' , 'megamaxmenu' ),
	);

	$settings['tabs'][50] = array(
		'id' 		=> 'tabs_trigger',
		'title'		=> __( 'Trigger' , 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'mouseover',
		'desc'		=> __( 'You can override the standard trigger which is set in the Control Panel for this set of tabs.' , 'megamaxmenu' ),
		'ops'		=> array(
			'group'	=> array(
				'auto'		=> array(
					'name'	=> __( 'Automatic' , 'megamaxmenu' ),
				),
				'mouseover'	=> array(
					'name'	=> __( 'Hover' , 'megamaxmenu' ),
				),
				'click'	=> array(
					'name'	=> __( 'Click' , 'megamaxmenu' ),
				),
			)
		),
	);

	$settings['tabs'][60] = array(
		'id' 		=> 'dynamic_panel_sizing',
		'title'		=> __( 'Dynamic Panel Sizing (Experimental)' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'By default, all content panels within the tabs block will be given the height of the tallest tab.  Enable this setting to instead dynamically resize the tabs block to the height of the open tab panel.  Not intended for use on nested tabs.' , 'megamaxmenu' ),
	);
	$settings['tabs'][62] = array(
		'id' 		=> 'dynamic_panel_animation',
		'title'		=> __( 'Dynamic Panel Animation (Experimental)' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Smoothly transition between tab content panel heights when using Dynamic Panel Sizing.' , 'megamaxmenu' ),
	);



	/** MENU SEGMENT **/

	$settings['menu_segment'][10] = array(
		'id'		=> 'menu_segment',
		'title'		=> __( 'Menu', 'megamaxmenu' ),
		'type'		=> 'select',
		'ops'		=> 'megamaxmenu_get_nav_menu_ops',
		'default'	=> '',
		'desc'		=> __( 'Select a menu to insert its items into the menu tree.' , 'megamaxmenu' ),

	);

	$settings['menu_segment'][15] = array(
		'id'		=> 'submenu_autocolumns',
		'title'		=> __( 'Automatic Columns' , 'megamaxmenu' ),
		'desc'		=> __( 'Automatically divide the first level of items in this menu segment evenly across columns, organized vertically - top to bottom.  Note that the Column Submenu Default can be inherited through Menu Segments from their parents, but this setting must be set on the Menu Segment.', 'megamaxmenu' ),
		//'tip'		=> __( 'How does this differ from "Submenu Column Default"? If you set "Submenu Automatic Columns" to 5, it\'ll create 5 even submenu columns and automatically divide the child items of this item into those columns, organized top to bottom.  If you set "Submenu Column Default" to 1/5, each child item will be 20% (1/5) of the submenu width and be organized left to right.  In practice, you\'d use "Submenu Automtic Columns" for submenus without column headers, and the "Submenu Column Default" setting for submenus with column headers', 'megamaxmenu' ),
		'type'		=> 'radio',
		'ops'		=> array( 'group' => array(
						'disabled'	=> array(
							'name'	=> __( 'Disabled' , 'megamaxmenu' ),
						),
						1 	=> 1,
						2 	=> 2,
						3 	=> 3,
						4 	=> 4,
						5 	=> 5,
						6 	=> 6,
						7 	=> 7,
						8 	=> 8,
						9 	=> 9,
						10 	=> 10
					)),
		'default' => 'disabled',

	);
	$settings['menu_segment'][20] = array(
		'id'		=> 'segment_transient_cache',
		'title'		=> __( 'Cache Segment (Experimental)', 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
		'desc'		=> __( 'Cache this menu segment via transients.  Note that saving this menu item will flush the cache and the segment will be rebuilt the next time the menu is generated.' , 'megamaxmenu' ),
	);
	$settings['menu_segment'][30] = array(
		'id'		=> 'segment_transient_cache_expiry',
		'title'		=> __( 'Cache Expiration', 'megamaxmenu' ),
		'type'		=> 'text',
		'default'	=> '',
		'desc'		=> __( '(Hours) How long until the transient expires and the cache refreshes.  Defaults to 12 hours.' , 'megamaxmenu' ),
	);





	/** CUSTOM CONTENT **/

	$settings['custom_content'][10] = array(
		'id'		=> 'custom_content',
		'title'		=> __( 'Custom Content' , 'megamaxmenu' ),
		'type'		=> 'textarea',
		'default'	=> '',
		'desc'		=> __( 'Can contain HTML and shortcodes', 'megamaxmenu' ),
	);


	$settings['custom_content'][20] = array(
		'id'		=> 'pad_custom_content',
		'title'		=> __( 'Pad Custom Content' , 'megamaxmenu' ),
		'type'		=> 'checkbox',
		'default'	=> 'on',
		'desc'		=> __( 'Pad the content area so that it aligns similarly to other menu items', 'megamaxmenu' ),
	);

	$settings['custom_content'][30] = array(
		'id' 		=> 'custom_content_class',
		'title'		=> 'Custom Class',
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'A class to be applied to the custom content wrapper.' , 'megamaxmenu' ),
	);



	/** CUSTOM CONTENT LAYOUT **/
	$settings['custom_content_layout'][10] = array(
		'id' 		=> 'columns',
		'title'		=> __( 'Columns Width', 'megamaxmenu' ),
		'type'		=> 'radio',
		'default' 	=> 'auto',
		'desc'		=> __( 'This is the fraction of the submenu width that the custom content area will occupy.' , 'megamaxmenu' ),
		'ops'		=> $column_ops
	);

	$settings['custom_content_layout'][30] = array(
		'id' 		=> 'item_align',
		'title'		=> __( 'Alignment', 'megamaxmenu' ),
		'type'		=> 'select',
		'default' 	=> 'auto',
		'desc'		=> __( 'Alignment of this menu item within the menu bar' , 'megamaxmenu' ),
		'ops'		=> array(
			'auto'	=> __( 'Automatic' , 'megamaxmenu' ),
			'left'	=> __( 'Left' , 'megamaxmenu' ),
			'right'	=> __( 'Right', 'megamaxmenu' ),
		),
		'scenario'	=> __( 'Top Level Menu Items' , 'megamaxmenu' ),

	);









	/** CUSTOMIZE **/

	$settings['customize'][10] = array(
		'id'		=> 'background_color',
		'title'		=> __( 'Background Color' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> '',
		'on_save'	=> 'background_color'

	);

	$settings['customize'][20] = array(
		'id'		=> 'font_color',
		'title'		=> __( 'Font Color' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> '',
		'on_save'	=> 'font_color'

	);

	$settings['customize'][25] = array(
		'id'		=> 'background_color_active',
		'title'		=> __( 'Background Color [Active]' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> '',
		'on_save'	=> 'background_color_active'

	);

	$settings['customize'][30] = array(
		'id'		=> 'font_color_active',
		'title'		=> __( 'Font Color [Active]' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> '',
		'on_save'	=> 'font_color_active'

	);

	$settings['customize'][35] = array(
		'id'		=> 'background_color_current',
		'title'		=> __( 'Background Color [Current]' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> '',
		'on_save'	=> 'background_color_current'

	);

	$settings['customize'][40] = array(
		'id'		=> 'font_color_current',
		'title'		=> __( 'Font Color [Current]' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> '',
		'on_save'	=> 'font_color_current'

	);

	$settings['customize'][50] = array(
		'id'		=> 'padding',
		'title'		=> __( 'Padding' , 'megamaxmenu' ),
		'type'		=> 'text',
		'default'	=> '',
		'desc'		=> __( 'Set the padding for this specific item' , 'megamaxmenu' ),
		'on_save'	=> 'padding'

	);

	$settings['customize'][60] = array(
		'id'		=> 'submenu_background_color',
		'title'		=> __( 'Submenu Background Color' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> '',
		'on_save'	=> 'submenu_background_color'

	);

	$settings['customize'][70] = array(
		'id'		=> 'submenu_color',
		'title'		=> __( 'Submenu Font Color' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> '',
		'on_save'	=> 'submenu_color'

	);

	$settings['customize'][80] = array(
		'id'		=> 'column_background_color',
		'title'		=> __( 'Column Background Color' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> 'Specific to Column Header items which surround a column',
		'on_save'	=> 'column_background_color'
	);


	/** CUSTOMIZE COLUMN **/

	$settings['customize_column'][10] = array(
		'id'		=> 'column_background_color',
		'title'		=> __( 'Background Color' , 'megamaxmenu' ),
		'type'		=> 'color',
		'default'	=> '',
		'desc'		=> '',
		'on_save'	=> 'column_background_color'
	);





	return $settings;

}

function megamaxmenu_get_dp_subcontent_ops(){
	$ops = array(
			'standard'	=> array(
				'none'	=> array(
					'name'	=> __( 'None' , 'megamaxmenu' ),
					'desc'	=> __( 'Dynamic subcontent disabled' , 'megamaxmenu' ),
				),
				'date' => array(
					'name'	=> __( 'Date' , 'megamaxmenu' ),
					'desc'	=> __( 'Display the post date.' , 'megamaxmenu' ),
				),
				'author' => array(
					'name'	=> __( 'Author' , 'megamaxmenu' ),
					'desc'	=> __( 'Display the post author.' , 'megamaxmenu' ),
				),
				'excerpt' => array(
					'name'	=> __( 'Excerpt' , 'megamaxmenu' ),
					'desc'	=> __( 'Display the excerpt from the post.  You must manually enter your excerpt on the Post page, it will not be generated automatically.' , 'megamaxmenu' ),
				),
				'custom'	=> array(
					'name'	=> __( 'Custom' , 'megamaxmenu' ),
					'desc'	=> __( 'Write a custom filter - megamaxmenu_dp_subcontent' , 'megamaxmenu' ),
				),
			),
		);
	return $ops;

	//return apply_filters( 'megamaxmenu_dp_subcontent_ops' , $ops );
}

function megamaxmenu_get_dp_view_all_ops(){
	$ops = array(
		'none'		=> __( 'None' , 'megamaxmenu' ),
		'category'	=> __( 'Category' , 'megamaxmenu' ),
		'tag'		=> __( 'Tag' , 'megamaxmenu' ),
	);

	$taxonomies = get_taxonomies( array(
		'public'	=> true,
		'_builtin'	=> false,
		) , 'objects' );

	foreach( $taxonomies as $tax_id => $tax ){
		$ops[$tax_id] = $tax->labels->singular_name;
		// $settings['dynamic_posts'][$taxonomy_weight++] = array(
		// 	'id'	=> 'dp_'.$tax_id,
		// 	'title'	=> $tax->labels->singular_name . '<br/><small>[' . $tax_id . ']</small>',
		// 	'desc'	=> __( 'Custom taxonomy slug', 'megamaxmenu' ) . ': <strong>' . $tax_id.'</strong>',
		// 	'type'	=> 'autocomplete',
		// 	'default' 	=> '', // '_all_on',	//can also take array of enabled
		// 	'ops'		=> 'megamaxmenu_dp_custom_tax_ops',
		// 	'ops_args' 	=> array(
		// 				'tax_id'	=> $tax_id,
		// 				'tax'		=> $tax,
		// 			),
		// 	'tip' 	=> __( 'For performance purposes, the number of autocomplete results is limited to 100 by default.  You can adjust this in the MegaMaxMenu Control Panel > General Settings > Advanced Menu Items, or just enter the ID in the ID field.', 'megamaxmenu' ),
		// );
	}

	return $ops;

}


//Pro Layouts
function megamaxmenu_pro_item_layout_ops( $ops ){

	$admin_img_assets = MEGAMAXMENU_URL . 'admin/assets/images/';

	$ops['images']	= array(
		'group_title'	=> __( 'Image Layouts', 'megamaxmenu' ),

		'image_left' => array(
			'name'	=> __( 'Image Left', 'megamaxmenu' ),
			'img'	=> $admin_img_assets.'ItemLayout_ImageLeft.png',
		),
		'image_right' => array(
			'name' => __( 'Image Right', 'megamaxmenu' ),
			'img'	=> $admin_img_assets.'ItemLayout_ImageRight.png',
		),
		'image_above' => array(
			'name' => __( 'Image Above', 'megamaxmenu' ),
			'img'	=> $admin_img_assets.'ItemLayout_ImageAbove.png',
		),
		'image_below' => array(
			'name' => __( 'Image Below', 'megamaxmenu' ),
			'img'	=> $admin_img_assets.'ItemLayout_ImageBelow.png',
		),
		'image_only' => array(
			'name' => 'Image Only',
			'img'	=> $admin_img_assets.'ItemLayout_ImageOnly.png',
		),

	);

	return $ops;
}
add_filter( 'megamaxmenu_item_layout_ops' , 'megamaxmenu_pro_item_layout_ops' );

