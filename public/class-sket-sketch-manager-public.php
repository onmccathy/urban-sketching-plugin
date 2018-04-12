<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://huttartsites.co.nz
 * @since      1.0.0
 *
 * @package    Sket_Sketch_Manager
 * @subpackage Sket_Sketch_Manager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sket_Sketch_Manager
 * @subpackage Sket_Sketch_Manager/public
 * @author     Owen McCarthy <onmccarthy@gmail.com>
 */
class Sket_Sketch_Manager_Public {

    const SKETCH = 'sketch';

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    private $listPosttypes = array(
        'post', self::SKETCH,
    );

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Sket_Sketch_Manager_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sket_Sketch_Manager_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/sket-sketch-manager-public.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/colorbox.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        global $post;

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Sket_Sketch_Manager_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sket_Sketch_Manager_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script('sket-colorbox-script', plugin_dir_url(__FILE__) . 'js/jquery.colorbox-min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/sket-sketch-manager-public.js', array('jquery'), $this->version, true);

        wp_enqueue_script('sket-google-maps-single-api', 'http://maps.googleapis.com/maps/api/js?key=' . get_option('sket_google_api_key'), array(), null, true);
        $geo_data = SKET_Db::sket_get_geo_data_object($post->ID);
        if (is_single() && ($post->post_type == 'sketch')) {
            wp_enqueue_script('sket-google-maps-single', plugin_dir_url(__FILE__) . 'js/google-maps-single.js', array('jquery'), $this->version, true);
            wp_localize_script('sket-google-maps-single', 'sketch_single_data', array(
                'lat' => $geo_data->lat,
                'lng' => $geo_data->lng,
                'address' => $geo_data->address,
            ));
        }

        if (is_page('sketch-map')) {
            $geo_data = SKET_db::get_all_geo_records();

            wp_enqueue_script('sket-google-maps-marker-cluster', plugin_dir_url(__FILE__) . 'js/markercluster.js', array('jquery', 'sket-google-maps-single-api'), '1.0.1', true);
            wp_enqueue_script('sket-google-maps-sketches', plugin_dir_url(__FILE__) . 'js/google-maps-sketches.js', array('sket-google-maps-marker-cluster', 'jquery'), $this->version, true);
            wp_localize_script('sket-google-maps-sketches', 'sketch_map_data', array(
                'latCentre' => get_option('sket_map_centre_lat'),
                'lngCentre' => get_option('sket_map_centre_lng'),
                'cluster_images_url' => plugin_dir_url(__FILE__) . 'images/m',
                'geo_data' => $geo_data,
                // Release 1.3 adding location map selection.
                'sket_map_ajax_nonce' => wp_create_nonce('sket_map_ajax_nonce'),
                'ajaxurl' => admin_url('admin-ajax.php')
            ));
        }
    }

    /**
     * add async defer parameters to sket-google-maps-single-api
     */
    function sket_add_google_maps_async_defer($tag) {

        ## 1: list of scripts to defer.
        $scripts = array('maps.googleapis.com');
        ## 2: list of scripts to async.

        foreach ($scripts as $defer_script) {
            if (strpos($tag, $defer_script)) {

                $newstr = '<script type="text/javascript" ' . ' async defer ' . substr($tag, 31);

                return $newstr;
            }
        }
        return $tag;
    }

    /**
     * add post type to archive post types
     * @param type $query
     * @return type
     */
    function include_posttype_on_category_archive(&$query) {

        if ($query->is_category) {
            $query->set('post_type', $this->listPosttypes);
        }
        remove_action('pre_get_posts', 'include_posttype_on_category_archive'); // run once!
    }

    /**
     * add posttypes to blogpost page.
     * @param type $query
     * @return type
     */
    function add_postypes_to_blogpost($query) {

        if (is_home() && $query->is_main_query()) {


            $query->set('post_type', $this->listPosttypes);
        }

        return $query;
    }

    /**
     * add posttypes to blogpost page.
     * @param type $query
     * @return type
     */
    function query_post_type($query) {

        if (is_category() || is_tag()) {
            $query->set('post_type', $this->listPosttypes);
        }
        return $query;
    }

    /*     * *******************************************************
     * Limit the number of tags displayed by Tag Cloud widget *
     * ******************************************************* */

    function show_tag_meta($args) {
        // Check if taxonomy option of the widget is set to tags
        if (isset($args['taxonomy']) && $args['taxonomy'] == 'post_tag') {

            $my_args = array(
                'smallest' => 12,
                'largest' => 12,
                'unit' => 'pt',
                'order' => 'ASC',);
            $args = wp_parse_args($args, $my_args);
        }
        return $args;
    }
    
    /**
     * Shortcode location-select contains a list of locations 
     * Used by map to display maps centered by location of Sketches
     */
    function sket_select_location($atts) {
        
        $args = array(
            'taxonomy' => 'location',
            'hierarchical' => '1',
            'depth' => '2',
            'class' =>  'sket-location-select',
            'echo' => '0'
        );
        $output = wp_dropdown_categories( $args );
        
        return $output;
    }
}
