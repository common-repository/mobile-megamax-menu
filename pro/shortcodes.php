<?php

/** this allows shortcodes in widgets **/
add_filter('widget_text', 'do_shortcode');

/*
 * Recent Posts Shortcodes - optional Image (via "Featured Image" functionality).
 */
function megamaxmenu_recent_posts($atts){
	
	global $megaMaxMenu;
	
	extract(shortcode_atts(array(
		'num'		=>	3,		
		'img'		=>	'on',
		'img_size'	=> 'inherit',
		'img_width'	=> 	45,
		'img_height'=>	45,
		'excerpt'	=>	'off',
		'category'	=>	'',
		'default_img' => false,
		'offset'	=>	0,

	), $atts));
	
	$args = array(
		'numberposts'	=>	$num,
		'offset'		=>	$offset,
		'suppress_filters' => false
	);
	
	if(!empty($category)){
		if(is_numeric($category)){
			$args['category'] = $category;
		}
		else $args['category_name'] = $category;		
	}
	
	$posts = get_posts($args);
	
	$class = 'megamaxmenu-postlist';
	if($img == 'on') $class.= ' megamaxmenu-postlist-w-img';
	
	$html= '<ul class="'.$class.'">';
	foreach($posts as $post){
	  		
		$ex = $post->post_excerpt;
		if( $ex == '' && function_exists( 'wp_trim_words' ) ){	//wp_trim_words is a WP3.3 function
			$ex = $post->post_content;
			$ex = strip_shortcodes( $ex );

			$ex = str_replace(']]>', ']]&gt;', $ex);
			$excerpt_length = apply_filters('excerpt_length', 55);
			$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
			$ex = wp_trim_words( $ex, $excerpt_length, $excerpt_more );
		}
		//$ex = apply_filters('get_the_excerpt', $post->post_excerpt);
		
		$post_url = get_permalink($post->ID);
		
		$image = '';
		$w = $img_width;
		$h = $img_height;

		$content_styles = array();

		//if($img == 'on') $image = $megaMaxMenu->getPostImage($post->ID, $w, $h, $default_img);
		if( $img == 'on' ){
			$img_data = megamaxmenu_get_image( false , $post->ID , true ,
				array( 'img_w' => $w , 'img_h' => $h , 'img_size' => $img_size , 'default_img' => $default_img ) );
			if( isset( $img_data['img'] ) ){
				$image = $img_data['img'];
				$content_styles['padding-left'] = ( $img_data['w'] + 10 ) .'px';
			}
		}

		$_content_styles = '';
		if( !empty( $content_styles ) ){
			$_content_styles = 'style="';
			foreach( $content_styles as $prop => $val ){
				$_content_styles.= $prop.':'.$val.';';
			}
			$_content_styles.= '"';
		}
						
    	$html.= '<li class="megamaxmenu-postlist-item">'.	$image.
    				'<div class="megamaxmenu-postlist-title" '.$_content_styles.'><a href="'.$post_url.'">'.$post->post_title.'</a></div>';
    				
    	if($excerpt == 'on')
    		$html.= '<div class="megamaxmenu-postlist-content" '.$_content_styles.'>'.$ex.'</div>';
    		
    	$html.= 	
    			'</li>';
	}
	$html.= '</ul>';
	
	return $html;
}
add_shortcode('wpmega-recent-posts', 'megamaxmenu_recent_posts');	//legacy
add_shortcode('megamaxmenu-recent-posts', 'megamaxmenu_recent_posts');






/*
 * Column Group Shortcode - must wrap [wpmega-col] shortcode
 */
function megamaxmenu_colgroup($atts, $data){

	$col_index = 0;
	
	$pattern = get_shortcode_regex();
		
	$pat = '/\[megamaxmenu\-col(?<atts>.*?)\]'.'(?<data>.*?)'.'\[\/megamaxmenu\-col\]/s';		//trailing /s makes dot (.) match newlines
	preg_match_all($pat, $data, $matches, PREG_SET_ORDER);
	
	if( empty( $matches ) ){
		$pat = '/\[wpmega\-col(?<atts>.*?)\]'.'(?<data>.*?)'.'\[\/wpmega\-col\]/s';		//trailing /s makes dot (.) match newlines
		preg_match_all($pat, $data, $matches, PREG_SET_ORDER);
	}
	
	$columns = array();
	
	foreach($matches as $m){
		//get the colspan
		$colspan_pat = '/colspan="(?<colspan>[\d]*?)"/';
		preg_match($colspan_pat, $m['atts'], $match);
		$colspan = isset($match['colspan']) ? $match['colspan'] : 1;
		$col_index += $colspan;	//increment by colspan, so if we have 2 cols in a 2/3rds format it's a 3-col with a 2-span and a 1-span
	}

	$cols = $col_index;
	$col_index = 0;

	foreach($matches as $m){
		
		//get the colspan
		$colspan_pat = '/colspan="(?<colspan>[\d]*?)"/';
		preg_match($colspan_pat, $m['atts'], $match);
		$colspan = isset($match['colspan']) ? $match['colspan'] : 1;
		
		$col_index += $colspan;	//increment by colspan, so if we have 2 cols in a 2/3rds format it's a 3-col with a 2-span and a 1-span
		
		$_cols = $cols;
		if( $colspan > 1 ){
			$fraction = megamaxmenu_simplify( $colspan , $cols );
			$colspan = $fraction['num'];
			$_cols = $fraction['den'];
		}

		$columns[] = '[megamaxmenu-col '.$m['atts'].' cols="'.$_cols.'" colspan="'.$colspan.'" col_index="'.$col_index.'" ]'.$m['data'].'[/megamaxmenu-col]';
	}
	
	$html ='<div class="megamaxmenu-colgroup megamaxmenu-colgroup-'.$col_index.'">';
	
	foreach($columns as $c){
		$html.= do_shortcode($c);		
	}
	
	$html.= '</div>';	//<div class="clear"></div>
	
	return $html;
}
add_shortcode( 'wpmega-colgroup', 	'megamaxmenu_colgroup');	//legacy
add_shortcode( 'megamaxmenu-colgroup', 'megamaxmenu_colgroup');

/*
 * Column Shortcode
 */
function megamaxmenu_col($atts, $data){
	extract(shortcode_atts(array(
		'cols'			=>  1,
		'colspan'		=>	1,
		'col_index'		=>	0,
	), $atts));
	
	$col_index;
	$data = do_shortcode($data);
	$data = megamaxmenu_trim_tag($data, array('br', 'br/', 'br /'));
	//return '<div class="ss-col ss-col-'.$col_index.' ss-colspan-'.$colspan.'">'.$data.'</div>';
	return '<div class="megamaxmenu-column megamaxmenu-column-'.$colspan .'-'.$cols.' ">'.$data.'</div>'; //ss-colspan-'.$colspan.'
}
add_shortcode( 'wpmega-col', 	'megamaxmenu_col' );	//legacy
add_shortcode( 'megamaxmenu-col', 	'megamaxmenu_col' );

function megamaxmenu_gcd($a,$b) {
    $a = abs($a); $b = abs($b);
    if( $a < $b) list($b,$a) = Array($a,$b);
    if( $b == 0) return $a;
    $r = $a % $b;
    while($r > 0) {
        $a = $b;
        $b = $r;
        $r = $a % $b;
    }
    return $b;
}
function megamaxmenu_simplify( $num , $den ) {
    $g = megamaxmenu_gcd( $num , $den );
    return array( 'num' => $num/$g , 'den' => $den/$g );
}

/* Tag Trimming Helper Function */
function megamaxmenu_trim_tag($s, $tags){
	$s = trim($s);
	foreach($tags as $tag){
		$tag = '<'.$tag.'>';
		if(strpos($s, $tag) === 0){
			$s = substr($s, strlen($tag));	
		}
		if(strpos($s, $tag) === strlen($s) - strlen($tag)){
			$s = substr($s, 0, strlen($s) - strlen($tag));
		}		
	}	
	return $s;
}


