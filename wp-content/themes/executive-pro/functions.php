<?php
//* Start the engine
require_once( get_template_directory() . '/lib/init.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'executive', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'executive' ) );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', __( 'Executive Pro Theme', 'executive' ) );
define( 'CHILD_THEME_URL', 'http://my.studiopress.com/themes/executive/' );
define( 'CHILD_THEME_VERSION', '3.0.0' );

//* Add HTML5 markup structure
add_theme_support( 'html5' );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Enqueue Google fonts
add_action( 'wp_enqueue_scripts', 'executive_google_fonts' );
function executive_google_fonts() {

	wp_enqueue_style( 'google-font', '//fonts.googleapis.com/css?family=Gentium+Basic|Open+Sans:400italic,700italic,400,700', array(), CHILD_THEME_VERSION );

}

//* Add new image sizes
add_image_size( 'featured', 300, 100, TRUE );
add_image_size( 'portfolio', 300, 200, TRUE );
add_image_size( 'slider', 1140, 445, TRUE );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for custom header
add_theme_support( 'custom-header', array(
	'width'           => 259,
	'height'          => 89,
	'header-selector' => '.site-title a',
	'header-text'     => false
) );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Unregister layout settings
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

//* Unregister secondary sidebar
unregister_sidebar( 'sidebar-alt' );

//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_before_header', 'genesis_do_nav' );

//* Unregister secondary navigation menu
add_theme_support( 'genesis-menus', array( 'primary' => __( 'Primary Navigation Menu', 'genesis' ) ) );

//* Load Admin Stylesheet
add_action( 'admin_enqueue_scripts', 'executive_load_admin_styles' );
function executive_load_admin_styles() {

	wp_register_style( 'custom_wp_admin_css', get_stylesheet_directory_uri() . '/lib/admin-style.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );

}

//* Create Portfolio Type custom taxonomy
add_action( 'init', 'executive_type_taxonomy' );
function executive_type_taxonomy() {

	register_taxonomy( 'portfolio-type', 'portfolio',
		array(
			'labels' => array(
				'name'          => _x( 'Types', 'taxonomy general name', 'executive' ),
				'add_new_item'  => __( 'Add New Portfolio Type', 'executive' ),
				'new_item_name' => __( 'New Portfolio Type', 'executive' ),
			),
			'exclude_from_search' => true,
			'has_archive'         => true,
			'hierarchical'        => true,
			'rewrite'             => array( 'slug' => 'portfolio-type', 'with_front' => false ),
			'show_ui'             => true,
			'show_tagcloud'       => false,
		)
	);

}


//* Create portfolio custom post type
add_action( 'init', 'executive_portfolio_post_type' );
function executive_portfolio_post_type() {

	register_post_type( 'portfolio',
		array(
			'labels' => array(
				'name'          => __( 'Portfolio', 'executive' ),
				'singular_name' => __( 'Portfolio', 'executive' ),
			),
			'has_archive'  => true,
			'hierarchical' => true,
			'menu_icon'    => get_stylesheet_directory_uri() . '/lib/icons/portfolio.png',
			'public'       => true,
			'rewrite'      => array( 'slug' => 'portfolio', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'revisions', 'page-attributes', 'genesis-seo', 'genesis-cpt-archives-settings' ),
			'taxonomies'   => array( 'portfolio-type' ),

		)
	);
	
}

//* Add Portfolio Type Taxonomy to columns
add_filter( 'manage_taxonomies_for_portfolio_columns', 'portfolio_columns' );
function portfolio_columns( $taxonomies ) {

    $taxonomies[] = 'portfolio-type';
    return $taxonomies;

}

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Set Genesis Responsive Slider defaults
add_filter( 'genesis_responsive_slider_settings_defaults', 'executive_responsive_slider_defaults' );
function executive_responsive_slider_defaults( $defaults ) {

	$args = array(
		'location_horizontal'             => 'Left',
		'location_vertical'               => 'Top',
		'posts_num'                       => '3',
		'slideshow_excerpt_content_limit' => '100',
		'slideshow_excerpt_content'       => 'full',
		'slideshow_excerpt_width'         => '30',
		'slideshow_height'                => '445',
		'slideshow_more_text'             => __( 'Continue Reading&hellip;', 'executive' ),
		'slideshow_title_show'            => 1,
		'slideshow_width'                 => '1140',
	);

	$args = wp_parse_args( $args, $defaults );
	
	return $args;
}

//* Relocate the post info
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'genesis_post_info', 5 );

//* Change the number of portfolio items to be displayed (props Bill Erickson)
add_action( 'pre_get_posts', 'executive_portfolio_items' );
function executive_portfolio_items( $query ) {

	if( $query->is_main_query() && !is_admin() && is_post_type_archive( 'portfolio' ) ) {
		$query->set( 'posts_per_page', '12' );
	}

}

//* Customize Portfolio post info and post meta
add_filter( 'genesis_post_info', 'executive_portfolio_post_info_meta' );
add_filter( 'genesis_post_meta', 'executive_portfolio_post_info_meta' );
function executive_portfolio_post_info_meta( $output ) {

     if( 'portfolio' == get_post_type() )
        return '';

    return $output;

}

//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'home-slider',
	'name'        => __( 'Home - Slider', 'executive' ),
	'description' => __( 'This is the slider section on the home page.', 'executive' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-cta',
	'name'        => __( 'Home - Call To Action', 'executive' ),
	'description' => __( 'This is the call to action section on the home page.', 'executive' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-top',
	'name'        => __( 'Home - Top', 'executive' ),
	'description' => __( 'This is the top section of the home page.', 'executive' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-middle',
	'name'        => __( 'Home - Middle', 'executive' ),
	'description' => __( 'This is the middle section of the home page.', 'executive' ),
) );

/** Register the page slider section */genesis_register_sidebar( array(
     'id' => 'page-images',
     'name' => __( 'Page Images', 'genesis' ),
     'description' => __( 'This is the page images widget.', 'news' ),
) );

/** Add the page slider section */
     add_action( 'genesis_after_header', 'custom_page_slider' );
     function custom_page_slider() {
          genesis_widget_area( 'page-images', array(
          'before' => '<div class="page-images widget-area">',
) );
}

//* Change the footer text
add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');
function sp_footer_creds_filter( $creds ) {
	$creds = '[footer_copyright] &middot; <a href="http://mydomain.com">Americana Inspection Services</a> &middot; Site Created by <a href="http://www.marketingpress.com" title="Marketing Press">Marketing Press</a>';
	return $creds;
}
