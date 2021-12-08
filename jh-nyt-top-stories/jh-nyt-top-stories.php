<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.janushenderson.com/
 * @since             1.0.0
 * @package           Jh_Nyt_Top_Stories
 *
 * @wordpress-plugin
 * Plugin Name:       Janus Henderson NYT Top Stories
 * Plugin URI:        https://github.com/JanusHenderson/wp-skills-assessment
 * Description:       Imports the New Yorks Times Top Stories
 * Version:           1.0.0
 * Author:            Nathan Pitts
 * Author URI:        https://www.janushenderson.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jh-nyt-top-stories
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'JH_NYT_TOP_STORIES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jh-nyt-top-stories-activator.php
 */
function activate_jh_nyt_top_stories() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jh-nyt-top-stories-activator.php';
	Jh_Nyt_Top_Stories_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jh-nyt-top-stories-deactivator.php
 */
function deactivate_jh_nyt_top_stories() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jh-nyt-top-stories-deactivator.php';
	Jh_Nyt_Top_Stories_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_jh_nyt_top_stories' );
register_deactivation_hook( __FILE__, 'deactivate_jh_nyt_top_stories' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jh-nyt-top-stories.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

/*
* Creating a function to create our CPT
*/
 
function custom_post_type() {
 
// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'NYT Top Stories', 'Post Type General Name', 'jh-nyt-top-stories' ),
        'singular_name'       => _x( 'NYT Top Story', 'Post Type Singular Name', 'jh-nyt-top-stories' ),
        'menu_name'           => __( 'NYT Top Stories', 'jh-nyt-top-stories' ),
        'parent_item_colon'   => __( 'Parent NYT Top Story', 'jh-nyt-top-stories' ),
        'all_items'           => __( 'All NYT Top Stories', 'jh-nyt-top-stories' ),
        'view_item'           => __( 'View NYT Top Story', 'jh-nyt-top-stories' ),
        'add_new_item'        => __( 'Add New NYT Top Story', 'jh-nyt-top-stories' ),
        'add_new'             => __( 'Add New', 'jh-nyt-top-stories' ),
        'edit_item'           => __( 'Edit NYT Top Story', 'jh-nyt-top-stories' ),
        'update_item'         => __( 'Update NYT Top Story', 'jh-nyt-top-stories' ),
        'search_items'        => __( 'Search NYT Top Story', 'jh-nyt-top-stories' ),
        'not_found'           => __( 'Not Found', 'jh-nyt-top-stories' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'jh-nyt-top-stories' ),
    );
     
// Set other options for Custom Post Type
     
    $args = array(
        'label'               => __( 'NYT Top Story', 'jh-nyt-top-stories' ),
        'description'         => __( 'New York Time Top Stories', 'jh-nyt-top-stories' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'category', 'post_tag' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */ 
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true,
 
    );
     
    // Registering your Custom Post Type
    register_post_type( 'NYT Top Stories', $args );
 
}
 
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
 
add_action( 'init', 'custom_post_type', 0 );



// Enqueue stylesheet
function nyttopstories_scripts() {
    wp_enqueue_style( 'nyttopstories__styles', plugins_url( 'public/css/jh-nyt-top-stories-public.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'nyttopstories_scripts' );

// Create shortcode
add_shortcode( 'nyttopstories', 'nyttopstories_articles' );
function nyttopstories_init(){
    function nyttopstories_articles() {
        // Define API call variables
        $feedOptions = get_option('nytfeed_options');
        $newsSection = $feedOptions['nytfeed_field_section'];//start of admin page, TODO: page to pick selection, home, business, ect...
        $apiKey = 'lE1UHvJhx6Xr0blgHzK9R5hIDqW208q7';
        $apiURL = 'https://api.nytimes.com/svc/topstories/v2/' . $newsSection . '.json?api-key=' . $apiKey . '';
        $numResults = 5; // TODO: add this option to settings page

        // Make API call
        $apiResponse = wp_remote_get( esc_url_raw( $apiURL ) );
        $parsedResponse = json_decode( wp_remote_retrieve_body( $apiResponse ), true);
        $parsedResults = $parsedResponse['results'];

        // Loop through response, build array of HTML blocks
       $fullFeed = [];
       for ( $i = 0; $i < $numResults; $i++ ) {
           // Variables captured from API response
           $articleTitle = $parsedResults[$i]['title'];
           $articleDesc = $parsedResults[$i]['abstract'];
           $articleDate = $parsedResults[$i]['published_date'];
           $articleURL = $parsedResults[$i]['url'];
           $articleAuthor = $parsedResults[$i]['byline'];
           $articleCategories = $parsedResults[$i]['section'];
           $articleTags = $parsedResults[$i]['des_facet'];
           $articleImage = $parsedResults[$i]['multimedia'][0]['url'];
           $articleAlt = $parsedResults[$i]['multimedia'][0]['caption'];

           // Build HTML block with captured variables           
           $htmlBlock = "<li><div class='nyttopstories__article'><h3 class='nyttopstories__title'><a href='" . $articleURL . "' target='_blank' class='nyttopstories__link'>" . $articleTitle . "</a></h3><p class='nyttopstories__desc'>" . $articleDesc . "</p><p class='nyttopstories__date'>" . $articleDate . "</p><a href='" . $articleURL . "' target='_blank' class='nyttopstories__link'></a><p class='nyttopstories__byline'>" . $articleAuthor . "</p><p class='nyttopstories__catagory'>" . $articleCategories . "</p><p class='nyttopstories__tag'>" . $articleTags[$i] . "</p></div></li>";
           
    
           // Push HTML block into new array of blocks
           array_push( $fullFeed, $htmlBlock );
       }

        //sort of the array
        asort( $fullFeed, $articleDate );
        // Show selected section of HTML blocks
       echo "<section class='nyttopstories'><ul class='nyttopstories__list'>";
       foreach ( $fullFeed as $value ) {
           echo $value;
       }
       echo "</ul></section>";
    }
}
add_action('init', 'nyttopstories_init');



function run_jh_nyt_top_stories() {

	$plugin = new Jh_Nyt_Top_Stories();
	$plugin->run();

}
run_jh_nyt_top_stories();
