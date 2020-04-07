<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


// Default globals
global $us_b_potential_bootstrap_color_classes, $us_b_heading_sizes, $builder_default_spacings;
$us_b_potential_bootstrap_color_classes = array('Primary'   => '#6761A8', 
                                           'Secondary' => '#EEB902', 
                                           'Success'   => '#97CC04', 
                                           'Info'      => '#187BCA', 
                                           'Warning'   => '#F45D01', 
                                           'Danger'    => '#FE4A49', 
                                           'Light'     => '#FBFFF1', 
                                           'Dark'      => '#2A2D34');
$us_b_heading_sizes = array('H1' => array('default' => '2.5rem'), 
                            'H2' => array('default' => '2rem'), 
                            'H3' => array('default' => '1.75rem'), 
                            'H4' => array('default' => '1.5rem'), 
                            'H5' => array('default' => '1.25rem'), 
                            'H6' => array('default' => '1rem'));
$builder_default_spacings = '{"mt": "", "mr": "", "mb": "", "ml": "", "pt": "", "pr": "", "pb": "", "pl": ""}';



/* Actions */
add_action( 'wp_enqueue_scripts', 'understrap_builder_remove_scripts', 20 ); // Remove UnderStrap Defaults
add_action( 'wp_enqueue_scripts', 'understrap_builder_enqueue_styles' ); // Add in UnderStrap BUIDLER styles & scripts
add_action( 'after_setup_theme', 'understrap_builder_add_child_theme_textdomain' ); // Assign language folder



/* Includes */
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/customizer.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/builder_template_functions.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/onpage_styles.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/onpage_scripts.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/additional_menus.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/builder_wpadmin_functions.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/builder_options_page.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/builder_importables.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/builder-custom-comments.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/builder_admin_bar.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/post_page_meta.php' );
require_once( trailingslashit( get_stylesheet_directory() ). 'inc/builder_custom_customizers.php' );

require_once( trailingslashit( get_stylesheet_directory() ). 'inc/TGM-Plugin-Activation/class-tgm-plugin-activation.php' );

require_once( trailingslashit( get_stylesheet_directory() ). 'inc/Customizer-Custom-Controls/custom-controls.php' );
//include(get_stylesheet_directory() . 'inc/init_helper.php');
//include(get_stylesheet_directory() . 'inc/filter_callback.php');
//require_once(  get_stylesheet_directory() . 'inc/init_helper.php' );
//require_once(  get_stylesheet_directory() . 'inc/filter_callback.php' );



/* PUC Update For BUILDER*/
// https://github.com/YahnisElsts/plugin-update-checker
require( trailingslashit( get_stylesheet_directory() ). 'inc/plugin-update-checker.php' );
global $BUILDERUpdateChecker;
$BUILDERUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://understrap.com/update/understrap_builder_latest.json#'.urlencode(get_home_url()),
	__FILE__, 
	'understrap-builder'
);



/* Remove UnderStrap Defaults */
function understrap_builder_remove_scripts() {
    wp_dequeue_style( 'understrap-styles' );
    wp_deregister_style( 'understrap-styles' );
    wp_dequeue_script( 'understrap-scripts' );
    wp_deregister_script( 'understrap-scripts' );
}



/* Remove some UnderStrap page templates */
function understrap_builder_remove_page_templates( $templates ) {
  unset( $templates['page-templates/blank.php'] );
  unset( $templates['page-templates/empty.php'] );
  return $templates;
}
add_filter( 'theme_page_templates', 'understrap_builder_remove_page_templates' );



/* Remove some UnderStrap sidebar locations */
function understrap_builder_unregister_sidebars(){
  unregister_sidebar( 'hero' );
  unregister_sidebar( 'herocanvas' );
  unregister_sidebar( 'statichero' );
}
add_action( 'widgets_init', 'understrap_builder_unregister_sidebars', 99 );



/* Add in UnderStrap BUIDLER Styles & scripts */
function understrap_builder_enqueue_styles() {
  
	$the_theme = wp_get_theme();
  
  wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . '/css/child-theme.min.css', array(), $the_theme->get( 'Version' ) );
  wp_enqueue_style( 'main-child-understrap-styles', get_stylesheet_directory_uri() . '/css/main.css', array() );
  wp_enqueue_style( 'fonts-child-understrap', 'https://fonts.googleapis.com/css2?family=Assistant:wght@200;300;400;600;700;800&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap', array() );
  wp_enqueue_script( 'jquery');
  wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . '/js/child-theme.min.js', array(), $the_theme->get( 'Version' ), true );
  wp_enqueue_style( 'understrap-builder-styles', get_stylesheet_directory_uri() . '/css/understrap-builder.min.css', array(), $the_theme->get( 'Version' ) );
  //wp_enqueue_script( 'understrap-builder-scripts', get_stylesheet_directory_uri() . '/js/understrap-builder.min.js', array(), $the_theme->get( 'Version' ), true );
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
  // BUILDER Google fonts
  $us_b_font_families = array();
  $us_b_subsets = 'latin';
  $understrap_builder_typography_default_font = json_decode(get_theme_mod( 'understrap_builder_typography_default_font', '{"font":"Open Sans","regularweight":"regular","italicweight":"italic","boldweight":"700","category":"sans-serif"}' ), true);
  $understrap_builder_typography_heading_font_custom = get_theme_mod('understrap_builder_typography_heading_font_custom', 1);
  $understrap_builder_typography_heading_font = json_decode(get_theme_mod( 'understrap_builder_typography_heading_font', '{"font":"Open Sans","regularweight":"regular","italicweight":"italic","boldweight":"700","category":"sans-serif"}' ), true);
  if('off' !== $understrap_builder_typography_default_font){
    $us_b_font_families[] = $understrap_builder_typography_default_font['font'] . ':' . $understrap_builder_typography_default_font['regularweight'] . ',' . $understrap_builder_typography_default_font['italicweight'] . ',' . $understrap_builder_typography_default_font['boldweight'];
  }
	if('off' !== $understrap_builder_typography_heading_font && $understrap_builder_typography_heading_font_custom == 0){
    $us_b_font_families[] = $understrap_builder_typography_heading_font['font'] . ':' . $understrap_builder_typography_heading_font['regularweight'] . ',' . $understrap_builder_typography_heading_font['italicweight'] . ',' . $understrap_builder_typography_heading_font['boldweight'];
  }	
  $us_b_query_args = array(
    'family' => urlencode(implode( '|', $us_b_font_families)),
    'subset' => urlencode($us_b_subsets),
    'display' => urlencode('fallback')
  );
  $us_b_fonts_url = add_query_arg( $us_b_query_args, "https://fonts.googleapis.com/css" );
  if (!empty( $us_b_fonts_url)){
		wp_enqueue_style( 'builder-fonts', esc_url_raw($us_b_fonts_url), array(), null );
	}  
  
}

/* Assign language folder */
function understrap_builder_add_child_theme_textdomain() {
    load_child_theme_textdomain( 'understrap-builder', get_stylesheet_directory() . '/languages' );
}


/* Allow HTML in Gutenberg HTML Block */
add_filter( 'wp_kses_allowed_html', 'understrap_builder_allow_iframe_in_editor', 10, 2 );
function understrap_builder_allow_iframe_in_editor( $tags, $context ) {
	if( 'post' === $context ) {
		$tags['iframe'] = array(
			'allowfullscreen' => TRUE,
			'frameborder' => TRUE,
			'height' => TRUE,
			'src' => TRUE,
			'style' => TRUE,
			'width' => TRUE,
		);
	}
	return $tags;
}



/* Convert BUILDER shortcodes to live data in string */
function understrap_builder_convert_text_date($original_string){
  $new_string_to_return = $original_string;
  $this_year = date('Y', time());
  $new_string_to_return = str_replace('[builder_current_year]', $this_year, $original_string);
  return $new_string_to_return;
}



/* Tidy the archive title for PRO headers */
add_filter( 'get_the_archive_title', function ($title) {    
  if ( is_category() ) {   
          $title = single_cat_title( '', false );    
      } elseif ( is_tag() ) {    
          $title = single_tag_title( '', false );    
      } elseif ( is_author() ) {    
          $title = '<span class="vcard">' . get_the_author() . '</span>' ;    
      } elseif ( is_tax() ) { //for custom post types
          $title = sprintf( __( '%1$s', 'understrap-builder' ), single_term_title( '', false ) );
      }    
  return $title;    
});



// Disable Post Formats for BUILDER */
add_action('after_setup_theme', 'understrap_builder_remove_formats', 100);
function understrap_builder_remove_formats(){
  remove_theme_support('post-formats');
}



// Suggested plugins
add_action( 'tgmpa_register', 'understrap_builder_register_required_plugins' );
function understrap_builder_register_required_plugins() {
	$plugins = array(
		array(
			'name'      => 'Bootstrap Blocks',
			'slug'      => 'wp-bootstrap-blocks',
			'required'  => false
		),
    array(
			'name'      => 'Contact Form 7',
			'slug'      => 'contact-form-7',
			'required'  => false
		),
    array(
			'name'      => 'One Click Demo Import',
			'slug'      => 'one-click-demo-import',
			'required'  => false
		)
	);
	tgmpa( $plugins, array() );
}


/* BUILDER Image Sizes */

add_image_size( 'us_b_banner', 1600, 500, true);
add_image_size( 'us_b_button', 350, 350, true);


/* SkyRocket Sex Up Customizer Controls */
// https://github.com/maddisondesigns/customizer-custom-controls

// Enqueue scripts for Customizer preview
if ( ! function_exists( 'skyrocket_customizer_preview_scripts' ) ) {
	function skyrocket_customizer_preview_scripts() {
		wp_enqueue_script( 'skyrocket-customizer-preview', trailingslashit( get_stylesheet_directory_uri() ) . 'js/customizer-preview.js', array( 'customize-preview', 'jquery' ) );
	}
}
add_action( 'customize_preview_init', 'skyrocket_customizer_preview_scripts' );


function init_register_my_cpts_vehicle() {

	/**
	 * Post Type: Vehicle.
	 */

	$labels = [
		"name" => __( "Vehicle", "understrap-builder" ),
		"singular_name" => __( "Vehicle", "understrap-builder" ),
		"menu_name" => __( "My Vehicle", "understrap-builder" ),
		"all_items" => __( "All Vehicle", "understrap-builder" ),
		"add_new" => __( "Add new", "understrap-builder" ),
		"add_new_item" => __( "Add new Vehicle", "understrap-builder" ),
		"edit_item" => __( "Edit Vehicle", "understrap-builder" ),
		"new_item" => __( "New Vehicle", "understrap-builder" ),
		"view_item" => __( "View Vehicle", "understrap-builder" ),
		"view_items" => __( "View Vehicle", "understrap-builder" ),
		"search_items" => __( "Search Vehicle", "understrap-builder" ),
		"not_found" => __( "No Vehicle found", "understrap-builder" ),
		"not_found_in_trash" => __( "No Vehicle found in trash", "understrap-builder" ),
		"parent" => __( "Parent Vehicle:", "understrap-builder" ),
		"featured_image" => __( "Featured image for this Vehicle", "understrap-builder" ),
		"set_featured_image" => __( "Set featured image for this Vehicle", "understrap-builder" ),
		"remove_featured_image" => __( "Remove featured image for this Vehicle", "understrap-builder" ),
		"use_featured_image" => __( "Use as featured image for this Vehicle", "understrap-builder" ),
		"archives" => __( "Vehicle archives", "understrap-builder" ),
		"insert_into_item" => __( "Insert into Vehicle", "understrap-builder" ),
		"uploaded_to_this_item" => __( "Upload to this Vehicle", "understrap-builder" ),
		"filter_items_list" => __( "Filter Vehicle list", "understrap-builder" ),
		"items_list_navigation" => __( "Vehicle list navigation", "understrap-builder" ),
		"items_list" => __( "Vehicle list", "understrap-builder" ),
		"attributes" => __( "Vehicle attributes", "understrap-builder" ),
		"name_admin_bar" => __( "Vehicle", "understrap-builder" ),
		"item_published" => __( "Vehicle published", "understrap-builder" ),
		"item_published_privately" => __( "Vehicle published privately.", "understrap-builder" ),
		"item_reverted_to_draft" => __( "Vehicle reverted to draft.", "understrap-builder" ),
		"item_scheduled" => __( "Vehicle scheduled", "understrap-builder" ),
		"item_updated" => __( "Vehicle updated.", "understrap-builder" ),
		"parent_item_colon" => __( "Parent Vehicle:", "understrap-builder" ),
	];

	$args = [
		"label" => __( "Vehicle", "understrap-builder" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "vehicle", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
	];

	register_post_type( "vehicle", $args );
}

add_action( 'init', 'init_register_my_cpts_vehicle' );

function init_register_my_taxes_manufacturer() {

	/**
	 * Taxonomy: Manufacturers.
	 */

	$labels = [
		"name" => __( "Manufacturers", "understrap-builder" ),
		"singular_name" => __( "Manufacturer", "understrap-builder" ),
		"menu_name" => __( "Manufacturers", "understrap-builder" ),
		"all_items" => __( "All Manufacturers", "understrap-builder" ),
		"edit_item" => __( "Edit Manufacturer", "understrap-builder" ),
		"view_item" => __( "View Manufacturer", "understrap-builder" ),
		"update_item" => __( "Update Manufacturer name", "understrap-builder" ),
		"add_new_item" => __( "Add new Manufacturer", "understrap-builder" ),
		"new_item_name" => __( "New Manufacturer name", "understrap-builder" ),
		"parent_item" => __( "Parent Manufacturer", "understrap-builder" ),
		"parent_item_colon" => __( "Parent Manufacturer:", "understrap-builder" ),
		"search_items" => __( "Search Manufacturers", "understrap-builder" ),
		"popular_items" => __( "Popular Manufacturers", "understrap-builder" ),
		"separate_items_with_commas" => __( "Separate Manufacturers with commas", "understrap-builder" ),
		"add_or_remove_items" => __( "Add or remove Manufacturers", "understrap-builder" ),
		"choose_from_most_used" => __( "Choose from the most used Manufacturers", "understrap-builder" ),
		"not_found" => __( "No Manufacturers found", "understrap-builder" ),
		"no_terms" => __( "No Manufacturers", "understrap-builder" ),
		"items_list_navigation" => __( "Manufacturers list navigation", "understrap-builder" ),
		"items_list" => __( "Manufacturers list", "understrap-builder" ),
	];

	$args = [
		"label" => __( "Manufacturers", "understrap-builder" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'manufacturer', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "manufacturer",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
		];
	register_taxonomy( "manufacturer", [ "vehicle" ], $args );
}
add_action( 'init', 'init_register_my_taxes_manufacturer' );

function init_register_my_taxes_class() {

	/**
	 * Taxonomy: Classes.
	 */

	$labels = [
		"name" => __( "Classes", "understrap-builder" ),
		"singular_name" => __( "Class", "understrap-builder" ),
		"menu_name" => __( "Classes", "understrap-builder" ),
		"all_items" => __( "All Classes", "understrap-builder" ),
		"edit_item" => __( "Edit Class", "understrap-builder" ),
		"view_item" => __( "View Class", "understrap-builder" ),
		"update_item" => __( "Update Class name", "understrap-builder" ),
		"add_new_item" => __( "Add new Class", "understrap-builder" ),
		"new_item_name" => __( "New Class name", "understrap-builder" ),
		"parent_item" => __( "Parent Class", "understrap-builder" ),
		"parent_item_colon" => __( "Parent Class:", "understrap-builder" ),
		"search_items" => __( "Search Classes", "understrap-builder" ),
		"popular_items" => __( "Popular Classes", "understrap-builder" ),
		"separate_items_with_commas" => __( "Separate Classes with commas", "understrap-builder" ),
		"add_or_remove_items" => __( "Add or remove Classes", "understrap-builder" ),
		"choose_from_most_used" => __( "Choose from the most used Classes", "understrap-builder" ),
		"not_found" => __( "No Classes found", "understrap-builder" ),
		"no_terms" => __( "No Classes", "understrap-builder" ),
		"items_list_navigation" => __( "Classes list navigation", "understrap-builder" ),
		"items_list" => __( "Classes list", "understrap-builder" ),
	];

	$args = [
		"label" => __( "Classes", "understrap-builder" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'class', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "class",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
		];
	register_taxonomy( "class", [ "vehicle" ], $args );
}
add_action( 'init', 'init_register_my_taxes_class' );


add_action( 'wp_ajax_filter_callback', 'filter_callback' );
add_action( 'wp_ajax_nopriv_filter_callback', 'filter_callback' );
function filter_callback(){
	$manuf = $_POST['manuf'];
    $class = $_POST['class'];
    $paged = $_POST['paged'];
    if(!$paged){
        if($manuf==='all' && $class==='all'){
            $args=array(
                'posts_per_page' => 6,
                'post_status' => 'publish',
                'post_type' => 'vehicle',
                'meta_key' => 'price',
                'orderby' => 'meta_value',
                'order' => 'ASC'
            );
        }else{
            if($manuf!='all' && $class!='all'){
                $args=array(
                    'posts_per_page'=>6,
                    'post_status'=>'publish',
                    'post_type'=>'vehicle',
                    'meta_key' => 'price',
                    'orderby' => 'meta_value',
                    'order' => 'ASC',
                    'tax_query' => array(
                        'relation' => 'AND',
						array(
                            'taxonomy' => 'class',
                            'field' => 'slug',
                            'terms' => $class,
							'operator' => 'IN'
                        ),
                        array(
                            'taxonomy' => 'manufacturer',
                            'field' => 'term_id',
                            'terms' => $manuf,
							'operator' => 'IN'
                        )
                   )
                );
            }else{
                if($manuf==='all'){
					$args=array(
                        'posts_per_page'=>6,
                        'post_status'=>'publish',
                        'post_type'=>'vehicle',
                        'meta_key' => 'price',
                        'orderby' => 'meta_value',
                        'order' => 'ASC',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'class',
                                'field' => 'slug',
                                'terms' => $class
                            )
                       )
                    );
                }else{
                    $args=array(
                        'posts_per_page'=>6,
                        'post_status'=>'publish',
                        'post_type'=>'vehicle',
                        'meta_key' => 'price',
                        'orderby' => 'meta_value',
                        'order' => 'ASC',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'manufacturer',
                                'field' => 'term_id',
                                'terms' => $manuf
                            )
                       )
                    );
                }
            }
        }
    }else{
        if($manuf==='all' && $class==='all'){
            $args=array(
                'posts_per_page' => 6,
                'post_status' => 'publish',
                'post_type' => 'vehicle',
                'meta_key' => 'price',
                'orderby' => 'meta_value',
                'order' => 'ASC',
                'paged' => $paged
            );
        }else{
            if($manuf!='all' && $class!='all'){
                $args=array(
                    'posts_per_page'=>6,
                    'post_status'=>'publish',
                    'post_type'=>'vehicle',
                    'meta_key' => 'price',
                    'orderby' => 'meta_value',
                    'order' => 'ASC',
                    'paged' => $paged,
                    'tax_query' => array(
                        'relation' => 'AND',
						array(
                            'taxonomy' => 'class',
                            'field' => 'slug',
                            'terms' => $class,
							'operator' => 'IN'
                        ),
                        array(
                            'taxonomy' => 'manufacturer',
                            'field' => 'term_id',
                            'terms' => $manuf,
							'operator' => 'IN'
                        )
                   )
                );
            }else{
                if($manuf==='all'){
                    $args=array(
                        'posts_per_page'=>6,
                        'post_status'=>'publish',
                        'post_type'=>'vehicle',
                        'meta_key' => 'price',
                        'orderby' => 'meta_value',
                        'order' => 'ASC',
                        'paged' => $paged,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'class',
                                'field' => 'slug',
                                'terms' => $class
                            )
                       )
                    );
                }else{
					$args=array(
                        'posts_per_page'=>6,
                        'post_status'=>'publish',
                        'post_type'=>'vehicle',
                        'meta_key' => 'price',
                        'orderby' => 'meta_value',
                        'order' => 'ASC',
                        'paged' => $paged,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'manufacturer',
                                'field' => 'term_id',
                                'terms' => $manuf
                            )
                       )
                    );
                }
            }
        }
    }
    $query = new WP_Query($args);
    if ($query->have_posts()):
    while ($query->have_posts()): $query->the_post();
    $id = get_the_ID();
    $manuf = wp_get_post_terms($id, 'manufacturer', array("fields" => "all"));
    $class = wp_get_post_terms($id, 'class', array("fields" => "all"));
    ?>
    <div class="col-4 vehicle-item">
        <div class="wrap">
            <div class="img-block">
                <?php echo get_the_post_thumbnail($post); ?>
            </div>
            <div class="row desc-block">
                <div class="col-12 title">
                    <?php the_title(); ?>
                </div>
                <div class="col-7 desc-items">
                    <div class="year"><span>Year:</span> <?php the_field('year'); ?></div>
                    <div class="manufacturer"><span>Manufacturer:</span> <?php echo $manuf[0]->name; ?></div>
                    <div class="class"><span>Class:</span> <?php echo $class[0]->name; ?></div>
                </div>
                <div class="col-5  price">
                    <span>$<?php the_field('price'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php
    endwhile;
    endif;
    die();
}

