<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://huttartsites.co.nz
 * @since      1.0.0
 *
 * @package    Sket_Sketch_Manager
 * @subpackage Sket_Sketch_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sket_Sketch_Manager
 * @subpackage Sket_Sketch_Manager/includes
 * @author     Owen McCarthy <onmccarthy@gmail.com>
 */
class Sket_Sketch_Manager {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Sket_Sketch_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;
    
    /**
     * The public class that's responsible for managing frontend functions of the
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Sket_Sketch_Manager_Public    $plugin_public    .
     */
    protected $plugin_public;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;
    
    /**
     * posttpye classes
     */
    protected $sketch;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {


        $this->plugin_name = 'sket-sketch-manager';
        $this->version = '0.4';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_shortcodes();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Sket_Sketch_Manager_Loader. Orchestrates the hooks of the plugin.
     * - Sket_Sketch_Manager_i18n. Defines internationalization functionality.
     * - Sket_Sketch_Manager_Admin. Defines all hooks for the admin area.
     * - Sket_Sketch_Manager_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        
        

        

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sket-sketch-manager-loader.php';
        
        $this->loader = new Sket_Sketch_Manager_Loader();

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sket-sketch-manager-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sket-sketch-manager-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-sket-sketch-manager-public.php';

        
        

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class_sket_google_api_interface.php';

       
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Sket_Sketch_Manager_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Sket_Sketch_Manager_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Sket_Sketch_Manager_Admin($this->get_plugin_name(), $this->get_version());
        //    
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('widgets_init', $plugin_admin, 'register_widgets');
        //
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_options_page');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_setting');

        $sket_geo = new Sket_Geo($this->get_plugin_name(), $this->get_version());

        // Sketch Post type
        $this->sketch = new Sket_Sketch_Manager_Sketch($this->get_plugin_name(), $this->get_version());
        $this->loader->add_filter('template_include', $this->sketch, 'get_template', 11, 1);
        // Sketch list custom columns
        $this->loader->add_filter('manage_edit-sket_sketch_columns', $this->sketch, 'add_columns');
        $this->loader->add_filter('manage_edit-sket_sketch_sortable_columns', $this->sketch, 'sortable_columns');
        $this->loader->add_action('manage_sket_sketch_posts_custom_column', $this->sketch, 'display_columns', 10, 2);
        // Standard post type hooksom_create_location_taxonomy
        $this->loader->add_action('init', $this->sketch, 'create_post_type');
        $this->loader->add_action('init', $this->sketch, 'create_location_taxonomy');
        $this->loader->add_action('add_meta_boxes', $this->sketch, 'create_metabox');
        $this->loader->add_action('save_post', $this->sketch, 'save_meta_data', 20);
        $this->loader->add_filter('widget_post_args', $this->sketch, 'sket_widget_posts_args_add_custom_type');
        $this->loader->add_filter('acatw_allowed_taxonomies', $this->sketch, 'sket_add_location_taxonomy');
        

        $sketch_db = new SKET_db($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $sketch_db, 'sket_register_artist_sketch_relationship', 1);
        $this->loader->add_action('switch_blog', $sketch_db, 'sket_register_artist_sketch_relationship');
        $this->loader->add_action('init', $sketch_db, 'sket_register_geo', 1);
        $this->loader->add_action('switch_blog', $sketch_db, 'sket_register_geo');

        $sket_artist = new Sket_Artist($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $sket_artist, 'create_artist_post_type');
        add_shortcode('sketch-images', array($sket_artist, 'display_artist_sketches'));
        add_shortcode('sketch-posts', array($sket_artist, 'display_sketch_posts'));

        $this->loader->add_filter('manage_media_columns', $sket_artist, 'add_artist_column');
        $this->loader->add_action('manage_media_custom_column', $sket_artist, 'artist_value', 10, 2);
        $this->loader->add_filter('manage_upload_sortable_columns', $sket_artist, 'artist_column_sortable');


        $sketch_tests = new Sket_Sketch_Tests($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $sketch_tests, 'sket_do_tests', 999);
        
        $sketchCrawls = new Sket_SketchCrawls($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_filter('template_include', $sketchCrawls, 'get_template', 11, 1);
        $this->loader->add_action('init', $sketchCrawls, 'create_post_type');
        $this->loader->add_action('cmb2_admin_init', $sketchCrawls, 'create_sketchcrawl_metabox', 999);
        $this->loader->add_action('init', $sketchCrawls, 'remove_post_edit_features');
        $this->loader->add_action('add_meta_boxes', $sketchCrawls, 'remove_yoast_seo_metabox',11);
       
        
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Sket_Sketch_Manager_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 99);
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        // add posttype to list of post types to be displayed on category archive
        $this->loader->add_action('pre_get_posts', $plugin_public, 'include_posttype_on_category_archive');
        // Include custom posts on blog page
        $this->loader->add_filter('pre_get_posts', $plugin_public, 'add_postypes_to_blogpost');
        $this->loader->add_filter('pre_get_posts', $plugin_public, 'query_post_type');
        // Add async defer parametersto google maps script definition to control load
        $this->loader->add_filter('script_loader_tag', $plugin_public, 'sket_add_google_maps_async_defer', 10);
        // Map location selection 
        add_shortcode('location-select', array($plugin_public,'sket_select_location'));
        $this->loader->add_action('wp_ajax_nopriv_sket_map_select_handler', $this->sketch, 'sket_map_select_handler');
        $this->loader->add_action('wp_ajax_sket_map_select_handler', $this->sketch, 'sket_map_select_handler');
    }
    /**
     * Define Shortcodes
     */
    private function define_shortcodes() {
        
        //add_shortcode('location-select', $this->plugin_public->sket_select_location);
        
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Sket_Sketch_Manager_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
