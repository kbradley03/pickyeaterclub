<?php
/***************************************************************************
 *  					Theme Functions
 * 	----------------------------------------------------------------------
 * 						DO NOT EDIT THIS FILE
 *	----------------------------------------------------------------------
 * 
 *  					Copyright (C) Themify
 * 						http://themify.me
 *
 *  To add custom PHP functions to the theme, create a new 'custom-functions.php' file in the theme folder.
 *  They will be added to the theme automatically.
 * 
 ***************************************************************************/

/////// Actions ////////
// Enqueue scripts and styles required by theme
add_action( 'wp_enqueue_scripts', 'themify_theme_enqueue_scripts', 11 );

// Browser compatibility
add_action( 'wp_head', 'themify_ie_enhancements' );
add_action( 'wp_head', 'themify_viewport_tag' );
add_action( 'wp_head', 'themify_ie_standards_compliant');

// Register custom menu
add_action( 'init', 'themify_register_custom_nav');

// Register sidebars
add_action( 'widgets_init', 'themify_theme_register_sidebars' );

// Theme Action
add_action('themify_builder_override_loop_themify_vars', 'themify_theme_portfolio_builder_vars', 10, 2 );

/**
 * Enqueue Stylesheets and Scripts
 * @since 1.0.0
 */
function themify_theme_enqueue_scripts(){

	// Get theme version for Themify theme scripts and styles
	$theme_version = wp_get_theme()->display('Version');

	///////////////////
	//Enqueue styles
	///////////////////
	
	// Themify base styling
	wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array(), $theme_version);

	// Themify Media Queries CSS
	wp_enqueue_style( 'themify-media-queries', THEME_URI . '/media-queries.css', array(), $theme_version);
	
	// Google Web Fonts embedding
	wp_enqueue_style( 'google-fonts', themify_https_esc('http://fonts.googleapis.com/css'). '?family=Abril+Fatface|Quicksand:400,700|Sorts+Mill+Goudy:400,400italic&subset=latin,latin-ext');

	// Themify Icons
	wp_enqueue_style( 'themify-icons', THEME_URI . '/themify/themify-icons/themify-icons.css', array(), $theme_version);

	///////////////////
	//Enqueue scripts
	///////////////////

	// Excanvas
	wp_enqueue_script( 'themify-excanvas', THEME_URI . '/js/excanvas.js', array(), false, true );

	// Waypoints plugin
	wp_enqueue_script( 'themify-waypoints', THEME_URI . '/js/waypoints.min.js', array('jquery'), $theme_version, true );

	// Slide mobile navigation menu
	wp_enqueue_script( 'slide-nav', THEME_URI . '/js/off-canvas.js', array( 'jquery' ), $theme_version, true );

	// Isotope
	wp_enqueue_script( 'themify-isotope', THEME_URI . '/js/jquery.isotope.min.js', array(), $theme_version, true );
	
	// Smart Resize for events debouncedresize and throttledresize
	wp_enqueue_script( 'themify-smartresize', THEME_URI.'/js/jquery.smartresize.js', array('jquery'), $theme_version, true );

	// Web fonts loader
	wp_enqueue_script( 'webfontsloader', '//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js', null, '1.4.7' );

	// Themify internal scripts
	wp_enqueue_script( 'theme-script', THEME_URI . '/js/themify.script.js', array('jquery'), $theme_version, true );

	// Themify gallery
	wp_enqueue_script( 'themify-gallery', THEMIFY_URI . '/js/themify.gallery.js', array('jquery'), false, true );
	
	$heading1_font = json_decode( get_theme_mod( 'heading1_font', '' ) );
	$heading2_font = json_decode( get_theme_mod( 'heading2_font', '' ) );

	// Prepare JS variables
	$themify_script_vars = array(
		'lightbox' => themify_lightbox_vars_init(),
		'lightboxContext' => apply_filters('themify_lightbox_context', '#pagewrap'),
		'isTouch' => themify_is_touch()? 'true': 'false',
		'fixedHeader' => themify_check('setting-fixed_header_disabled')? '': 'fixed-header',
		'chart' => apply_filters('themify_chart_init_vars', array(
			'trackColor' => '#f2f2f2',
			'scaleColor' => false,
			'lineCap' => 'butt',
			'rotate' => 0,
			'size' => 170,
			'lineWidth' => 22,
			'animate' => 2000
		)),
		'fittext_selector' => 'h1, h2',
		'h1_font' => ( isset( $heading1_font->family->fonttype ) && 'google' == $heading1_font->family->fonttype ) ? $heading1_font->family->name : 'Sorts Mill Goudy',
		'h2_font' => ( isset( $heading2_font->family->fonttype ) && 'google' == $heading2_font->family->fonttype ) ? $heading2_font->family->name : 'Sorts Mill Goudy',
	);

	// Inject variable values in gallery script
	wp_localize_script( 'theme-script', 'themifyScript', apply_filters('themify_script_vars', $themify_script_vars ) );
	
	// WordPress internal script to move the comment box to the right place when replying to a user
	if ( is_single() || is_page() ) wp_enqueue_script( 'comment-reply' );
}

/**
 * Add JavaScript files if IE version is lower than 9
 * @since 1.0.0
 */
function themify_ie_enhancements(){
	echo "\n".'
	<!-- media-queries.js -->
	<!--[if lt IE 9]>
		<script src="' . THEME_URI . '/js/respond.js"></script>
	<![endif]-->
	
	<!-- html5.js -->
	<!--[if lt IE 9]>
		<script src="'.themify_https_esc('http://html5shim.googlecode.com/svn/trunk/html5.js').'"></script>
	<![endif]-->
	'."\n";
}

/**
 * Add viewport tag for responsive layouts
 * @since 1.0.0
 */
function themify_viewport_tag(){
	echo "\n".'<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">'."\n";
}

/**
 * Make IE behave like a standards-compliant browser
 * @since 1.0.0 
 */
function themify_ie_standards_compliant() {
	echo "\n".'
	<!--[if lt IE 9]>
	<script src="'.themify_https_esc('http://s3.amazonaws.com/nwapi/nwmatcher/nwmatcher-1.2.5-min.js').'"></script>
	<script type="text/javascript" src="'.themify_https_esc('http://cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js').'"></script> 
	<![endif]-->
	'."\n";
}

/* Custom Write Panels
/***************************************************************************/

///////////////////////////////////////
// Build Write Panels
///////////////////////////////////////

if ( ! function_exists( 'themify_theme_init_types' ) ) {
	/**
	 * Initialize custom panel with its definitions
	 * Custom panel definitions are located in admin/post-type-TYPE.php
	 * @since 1.0.7
	 */
	function themify_theme_init_types() {
		// Load required files for post, page and custom post types where it applies
		foreach ( array( 'post', 'page', 'team', 'portfolio' ) as $type ) {
			require_once "admin/post-type-$type.php";
		}
		/**
		 * Navigation menus used in page custom panel to specify a custom menu for the page.
		 * @var array
		 * @since 1.2.7
		 */
		$nav_menus = array(	array( 'name' => '', 'value' => '', 'selected' => true ) );
		foreach ( get_terms( 'nav_menu' ) as $menu ) {
			$nav_menus[] = array( 'name' => $menu->name, 'value' => $menu->slug );
		}

		themify_build_write_panels( apply_filters('themify_theme_meta_boxes',
			array(
				array(
					'name'		=> __('Post Options', 'themify'),
					'id' 		=> 'post-options',
					'options'	=> themify_theme_post_meta_box(),
					'pages'		=> 'post'
				),
				array(
					'name'		=> __('Page Options', 'themify'),
					'id' 		=> 'page-options',
					'options'	=> themify_theme_page_meta_box( array( 'nav_menus' => $nav_menus ) ),
					'pages'		=> 'page'
				),
				array(
					"name"		=> __('Query Posts', 'themify'),
					'id'		=> 'query-posts',
					"options"	=> themify_theme_query_post_meta_box(),
					"pages"		=> "page"
				),
				array(
					"name"		=> __('Query Portfolios', 'themify'),
					'id' 		=> 'query-portfolio',
					"options"	=> themify_theme_query_portfolio_meta_box(),
					"pages"		=> "page"
				),
				array(
					'name'		=> __('Portfolio Options', 'themify'),
					'id' 		=> 'portfolio-options',
					'options' 	=> themify_theme_portfolio_meta_box(),
					'pages'		=> 'portfolio'
				),
				array(
					'name'		=> __('Team Options', 'themify'),
					'id' 		=> 'team-options',
					'options' 	=> themify_theme_team_meta_box(),
					'pages'		=> 'team'
				),
			)
		));
	}
}
add_action( 'after_setup_theme', 'themify_theme_init_types' );

///////////////////////////////////////
// Enable WordPress feature image
///////////////////////////////////////
add_theme_support( 'post-thumbnails' );
remove_post_type_support( 'page', 'thumbnail' );
	
/**
 * Register Custom Menu Function
 * @since 1.0.0
 */
function themify_register_custom_nav() {
	register_nav_menus( array(
		'main-nav' => __( 'Main Navigation', 'themify' ),
	));
}

/**
 * Default Main Nav Function
 * @since 1.0.0
 */
function themify_default_main_nav() {
	echo '<ul id="main-nav" class="main-nav clearfix">';
		wp_list_pages('title_li=');
	echo '</ul>';
}

/**
 * Sets custom menu selected in page custom panel as navigation, otherwise sets the default.
 *
 * @since 1.0.0
 */
function themify_theme_menu_nav() {
	$custom_menu = themify_get( 'custom_menu' );
	if ( isset( $custom_menu ) && '' != $custom_menu ) {
		wp_nav_menu( array( 'menu' => $custom_menu, 'fallback_cb' => 'themify_default_main_nav' , 'container'  => '' , 'menu_id' => 'main-nav' , 'menu_class' => 'main-nav clearfix' ) );
	} else {
		wp_nav_menu( array( 'theme_location' => 'main-nav' , 'fallback_cb' => 'themify_default_main_nav' , 'container'  => '' , 'menu_id' => 'main-nav' , 'menu_class' => 'main-nav clearfix' ) );
	}
}

/**
 * Checks if the browser is a mobile device
 * @return boolean 
 */
function themify_is_mobile(){
	return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

/**
 * Register sidebars
 * @since 1.0.0
 */
function themify_theme_register_sidebars() {
	$sidebars = array(
		array(
			'name' => __('Sidebar', 'themify'),
			'id' => 'sidebar-main',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		),
		array(
			'name' => __('Social Widget', 'themify'),
			'id' => 'social-widget',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<strong class="widgettitle">',
			'after_title' => '</strong>',
		),
		array(
			'name' => __('Footer Social Widget', 'themify'),
			'id' => 'footer-social-widget',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<strong class="widgettitle">',
			'after_title' => '</strong>',
		),
	);
	foreach( $sidebars as $sidebar ) {
		register_sidebar( $sidebar );
	}

	// Footer Sidebars
	themify_register_grouped_widgets();
}

if ( ! function_exists( 'themify_theme_default_social_links' ) ) {
	/**
	 * Replace default squared social link icons with circular versions
	 * @param $data
	 * @return mixed
	 * @since 1.0.0
	 */
	function themify_theme_default_social_links( $data ) {
		$pre = 'setting-link_img_themify-link-';
		$data[$pre.'0'] = THEME_URI . '/images/twitter.png';
		$data[$pre.'1'] = THEME_URI . '/images/facebook.png';
		$data[$pre.'2'] = THEME_URI . '/images/google-plus.png';
		$data[$pre.'3'] = THEME_URI . '/images/youtube.png';
		$data[$pre.'4'] = THEME_URI . '/images/pinterest.png';
		return $data;
	}
	add_filter( 'themify_default_social_links', 'themify_theme_default_social_links' );
}

if ( ! function_exists( 'themify_theme_custom_post_css' ) ) {
	/**
	 * Outputs custom post CSS at the end of a post
	 * @since 1.0.0
	 */
	function themify_theme_custom_post_css() {
		global $themify;
		if ( in_array( get_post_type(), array( 'page', 'portfolio' ) ) ) {
			$post_id = get_the_ID();
			if ( is_page() ) {
				$entry_id = '.page-id-' . $post_id;
				$headerwrap = $entry_id . ' #headerwrap';
			} else {
				$entry_id = '.postid-' . $post_id;
				$headerwrap = $entry_id . ' #headerwrap';
			}
			$css = array();
			$style = '';
			$rules = array();

			if ( 'transparent' != themify_get( 'header_wrap' ) ) {
				$rules = array(
					$headerwrap => array(
						array(
							'prop' => 'background-color',
							'key'  => 'background_color'
						),
						array(
							'prop' => 'background-image',
							'key'  => 'background_image'
						),
						array(
							'prop' => 'background-repeat',
							'key'  => 'background_repeat'
						),
						array(
							'prop' => 'color',
							'key'  => 'headerwrap_text_color'
						),
					),
					"$entry_id #site-logo span:after, $entry_id #headerwrap #searchform, $entry_id #main-nav .current_page_item a, $entry_id #main-nav .current-menu-item a" => array(
							array(
								'prop' => 'border-color',
								'key'  => 'headerwrap_text_color'
							),
					),
				);
			}

			$rules[$headerwrap . ' a, '
				 . $headerwrap . ' #searchform .icon-search:before,'
				 . $headerwrap . ' #main-nav ul a'] = array(
				array(
					'prop' => 'color',
					'key'  => 'headerwrap_link_color'
				),
			);

			if ( is_singular( 'portfolio' ) ) {
				$rules['.postid-' . $post_id . ' .featured-area'] =	array(
					array(	'prop' => 'background-color',
							'key' => 'featured_area_background_color'
					),
					array(	'prop' => 'background-image',
							'key' => 'featured_area_background_image'
					),
					array(	'prop' => 'background-repeat',
							'key' => 'featured_area_background_repeat'
					),
				);
				$rules['.postid-' . $post_id . ' .portfolio-post-wrap, .postid-' . $post_id . ' .portfolio-post-wrap .post-date'] = array(
					array(	'prop' => 'color',
							'key' => 'featured_area_text_color'
					),
				);
				$rules['.postid-' . $post_id . ' .portfolio-post-wrap a'] =	array(
					array(	'prop' => 'color',
							'key' => 'featured_area_link_color'
					),
				);
			}
			foreach ( $rules as $selector => $property ) {
				foreach ( $property as $val ) {
					$prop = $val['prop'];
					$key = $val['key'];
					if ( is_array( $key ) ) {
						if ( $prop == 'font-size' && themify_check( $key[0] ) ) {
							$css[$selector][$prop] = $prop . ': ' . themify_get( $key[0] ) . themify_get( $key[1] );
						}
					} elseif ( themify_check( $key ) && 'default' != themify_get( $key ) ) {
						if ( $prop == 'color' || stripos( $prop, 'color' ) ) {
							$css[$selector][$prop] = $prop . ': #' . themify_get( $key );
						}
						elseif ( $prop == 'background-image' && 'default' != themify_get( $key ) ) {
							$css[$selector][$prop] = $prop .': url(' . themify_get( $key ) . ')';
						}
						elseif ( $prop == 'background-repeat' && 'fullcover' == themify_get( $key ) ) {
							$css[$selector]['background-size'] = 'background-size: cover';
						}
						elseif ( $prop == 'font-family' ) {
							$font = themify_get( $key );
							$css[$selector][$prop] = $prop .': '. $font;
							if ( ! in_array( $font, themify_get_web_safe_font_list( true ) ) ) {
								$themify->google_fonts .= str_replace( ' ', '+', $font.'|' );
							}
						}
						else {
							$css[$selector][$prop] = $prop .': '. themify_get( $key );
						}
					}
				}
				if ( ! empty( $css[$selector] ) ) {
					$style .= "$selector {\n\t" . implode( ";\n\t", $css[$selector] ) . "\n}\n";
				}
			}

			if ( '' != $style ) {
				echo "\n<!-- Entry Style -->\n<style>\n$style</style>\n<!-- End Entry Style -->\n";
			}
		}
	}
	add_action( 'themify_content_after', 'themify_theme_custom_post_css' );
}

if ( ! function_exists( 'themify_theme_enqueue_google_fonts' ) ) {
	/**
	 * Enqueue Google Fonts
	 * @since 1.0.0
	 */
	function themify_theme_enqueue_google_fonts() {
		global $themify;
		if ( ! isset( $themify->google_fonts ) || '' == $themify->google_fonts ) return;
		$themify->google_fonts = substr( $themify->google_fonts, 0, -1 );
		wp_enqueue_style( 'section-styling-google-fonts', themify_https_esc( 'http://fonts.googleapis.com/css' ). '?family='.$themify->google_fonts );
	}
	add_action( 'wp_footer', 'themify_theme_enqueue_google_fonts' );
}

if ( ! function_exists('themify_theme_comment') ) {
	/**
	 * Custom Theme Comment
	 * @param object $comment Current comment.
	 * @param array $args Parameters for comment reply link.
	 * @param int $depth Maximum comment nesting depth.
	 * @since 1.0.0
	 */
	function themify_theme_comment( $comment, $args, $depth ) {
	   $GLOBALS['comment'] = $comment; ?>

		<li id="comment-<?php comment_ID() ?>" <?php comment_class(); ?>>
			<p class="comment-author">
				<?php printf( '%s <cite>%s</cite>', get_avatar( $comment, $size='85' ), get_comment_author_link() ); ?>
				<br />
				<small class="comment-time">
					<strong>
					<?php comment_date( apply_filters('themify_comment_date', '') ); ?>
					</strong> @
					<?php comment_time( apply_filters('themify_comment_time', '') ); ?>
					<?php edit_comment_link( __('Edit', 'themify' ),' [',']' ); ?>
				</small>
			</p>
			<div class="commententry">
				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p><em><?php _e( 'Your comment is awaiting moderation.', 'themify' ); ?></em></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>
			<p class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'add_below' => 'comment', 'depth' => $depth, 'reply_text' => __( 'Reply', 'themify' ), 'max_depth' => $args['max_depth'] ) ) ) ?>
			</p>
		<?php
	}
}

if ( ! function_exists( 'themify_theme_featured_area_style' ) ) {
	/**
	 * Returns the background repeat style as a class.
	 *
	 * @return mixed|string
	 */
	function themify_theme_featured_area_style() {
		return themify_check( 'background_repeat' ) ? themify_get( 'background_repeat' ) : '';
	}
}

/**
 * Adds body classes for special theme features: text align and image filters.
 *
 * @param $args
 *
 * @return array
 */
function themify_theme_body_class( $args ) {
	// Checks if the single post or page has default or full width.
	$classes[] = themify_check( 'content_width' ) ? themify_get( 'content_width' ) : 'default_width';

	// Add transparent-header class to body if user selected it in custom panel
	if ( ( is_single() || is_page() ) && 'transparent' == themify_get( 'header_wrap' ) ) {
		$classes[] = 'transparent-header';
	}

	// Content Alignment
	$content_text_align = '';
	$post_type = get_post_type();
	if ( is_single() ) {
		$value = themify_get( 'content_text_align' );
		if ( !empty( $value ) && 'initial' != $value ) {
			$content_text_align = $value;
		} else {
			$theme_setting = ( 'post' == $post_type ) ? 'setting-default_page_' . $post_type . '_content_text_align' : 'setting-default_' . $post_type . '_single_' . $post_type . '_content_text_align';
			if ( $value = themify_get( $theme_setting ) ) {
				$content_text_align = $value;
			}
		}
	} elseif ( is_page() ) {
		$value = themify_get( 'content_text_align' );
		if ( !empty( $value ) && 'initial' != $value ) {
			$content_text_align = $value;
		} else {
			if ( themify_is_query_page() ) {
				global $themify;
				$post_type = $themify->query_post_type;
				$theme_setting = ( 'post' == $post_type ) ? 'setting-default_content_text_align' : 'setting-default_' . $post_type . '_index_' . $post_type . '_content_text_align';
				if ( $value = themify_get( $theme_setting ) ) {
					$content_text_align = $value;
				}
			} elseif ( $value = themify_get( 'setting-default_page_content_text_align' ) ) {
				$content_text_align = $value;
			}
		}
	} else {
		$theme_setting = ( 'post' == $post_type ) ? 'setting-default_content_text_align' : 'setting-default_' . $post_type . '_index_' . $post_type . '_content_text_align';
		if ( $value = themify_get( $theme_setting ) ) {
			$content_text_align = $value;
		}
	}
	if ( '' == $content_text_align ) {
		if ( $value = themify_get( 'setting-default_content_text_align' ) ) {
			$content_text_align = $value;
		}
	}
	if ( 'center' == $content_text_align ) {
		$args[] = 'content-center';
	}

	// Image Filters
	$filter = '';
	$filter_hover = '';
	$apply_to = '';

	if ( is_page() ) {

		if ( $do_filter = themify_get( 'imagefilter_options' ) ) {
			if ( 'initial' != $do_filter ) {
				$filter = 'filter-' . $do_filter;
			}
		}

		if ( $do_hover_filter = themify_get( 'imagefilter_options_hover' ) ) {
			if ( 'initial' != $do_hover_filter ) {
				$filter_hover = 'filter-hover-' . $do_hover_filter;
			}
		}

		if ( $apply_here = themify_get('imagefilter_applyto') ) {
			if ( 'initial' != $apply_here ) {
				$apply_to = 'filter-' . $apply_here;
			}
		}

	} elseif ( is_singular() ) {

		if ( $do_filter = themify_get( 'imagefilter_options' ) ) {
			if ( 'initial' != $do_filter ) {
				$filter = 'filter-' . $do_filter;
			}
		}

		if ( $do_hover_filter = themify_get( 'imagefilter_options_hover' ) ) {
			if ( 'initial' != $do_hover_filter ) {
				$filter_hover = 'filter-hover-' . $do_hover_filter;
			}
		}

		if ( $apply_here = themify_get('imagefilter_applyto') ) {
			if ( 'initial' != $apply_here ) {
				$apply_to = 'filter-' . $apply_here;
			}
		}

	}

	if ( '' == $filter ) {
		if ( $do_filter = themify_get( 'setting-imagefilter_options' ) ) {
			$filter = 'filter-' . $do_filter;
		}
	}

	if ( '' == $filter_hover ) {
		if ( $do_hover_filter = themify_get( 'setting-imagefilter_options_hover' ) ) {
			$filter_hover = 'filter-hover-' . $do_hover_filter;
		} else {
			$filter_hover = 'filter-hover-none';
		}
	}

	if ( '' == $apply_to ) {
		if ( '' != $filter || '' != $filter_hover ) {
			if ( 'allimages' == themify_get('setting-imagefilter_applyto') ) {
				$apply_to = 'filter-all';
			} else {
				$apply_to = 'filter-featured-only';
			}
		}
	}

	$args[] = $filter;
	$args[] = $filter_hover;
	$args[] = $apply_to;

	// Disable Masonry
	$do_masonry = '';
	if ( themify_is_query_page() ) {
		global $themify;
		if ( isset( $themify->query_post_type ) && ! in_array( $themify->query_post_type, array( 'post', 'page' ) ) ) {
			$post_type = $themify->query_post_type;
		} else {
			$post_type = '';
		}
		$do_masonry = 'no' != themify_get( $post_type . '_enable_masonry' ) ? 'masonry-enabled' : '';
	} else {
		if ( ! themify_check( 'setting-disable_masonry' ) ) {
			$do_masonry = 'masonry-enabled';
		}
	}
	$args[] = $do_masonry;

	return $args;
}
add_filter('body_class', 'themify_theme_body_class', 99);

/**
 * Check if portfolio in builder_loop
 * Passed parameter to $themify object
 * @param object $themify 
 * @param string $module_name 
 */
function themify_theme_portfolio_builder_vars( $themify, $module_name ) {
	if ( 'portfolio' == $module_name ) {
		$themify->is_builder_portfolio_loop = true;
	}
}