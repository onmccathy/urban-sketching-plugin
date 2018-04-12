<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://huttartsites.co.nz
 * @since      1.0.0
 *
 * @package    Sket_Sketch_Manager
 * @subpackage Sket_Sketch_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sket_Sketch_Manager
 * @subpackage Sket_Sketch_Manager/admin
 * @author     Owen McCarthy <onmccarthy@gmail.com>
 */
class Sket_Sketch_Manager_Admin {

    /**
     * Constants
     * CSS Version
     */
    const CSS_JS_VERSION = '1.0.0';

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

    /**
     * The options name to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	private
     * @var  	string 		$option_name 	Option name of this plugin
     */
    private $option_name = 'sket';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->load_dependencies();
    }

    /**
     * Load the required post-type dependecies.
     *
     * Include the following files:
     *
     * - class-sket-sketch-manager-artist. Defines the Artist Post type.
     * - class-sket-sketch-manager-venue. Defines the Venue Post type.
     * - class-sket-sketch-manager-sketch. Defines the Artist Post type.
     *
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The Sketch Custom post type
         */
        require_once plugin_dir_path(__FILE__) . 'posttypes/class-sket-sketch-manager-sketch.php';
        require_once plugin_dir_path(__FILE__) . 'posttypes/class-sket-sketch-db.php';
        require_once plugin_dir_path(__FILE__) . 'posttypes/class-sket-tests.php';
        
        require_once plugin_dir_path(__FILE__) . 'posttypes/class-sket-artist.php';
        /**
         * The Fancy Widget
         */
        require_once plugin_dir_path(__FILE__) . 'widgets/widget-sket-recent-sketches.php';
        
        /**
         * Sketch Crawl Post Type
         */
        require_once plugin_dir_path(__FILE__) . 'posttypes/class-sket-sketchcrawls.php';
    }
    
    

    /**
     * Register the stylesheets for the admin area.
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
        global $pagenow, $typenow, $post;
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/sket-sketch-manager-admin.css', array(), $this->version, 'all');

        if ($typenow == 'sketch') {
            // only load if we are editing an sketch or adding a new one
            if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {
                wp_enqueue_style('sket-jquery-ui', plugin_dir_url(__FILE__) . 'css/jquery-ui.css', array(), self::CSS_JS_VERSION);
                wp_enqueue_style('sket-date-time-css', plugin_dir_url(__FILE__) . 'css/flatpickr.min.css', array(), self::CSS_JS_VERSION);
                wp_enqueue_style('sket-place-address-css', plugin_dir_url(__FILE__) . 'css/sket_places_address.css', array(), self::CSS_JS_VERSION);
                wp_enqueue_script('sket-google-maps-single-api', 'http://maps.googleapis.com/maps/api/js?key=' .
                        get_option('sket_google_api_key') . '&libraries=places', array(), null, true);
                wp_enqueue_script('sket-date-picker', plugin_dir_url(__FILE__) . 'js/script-admin-sketch.js', array('jquery', 'sket-time-picker'), self::CSS_JS_VERSION, true);
                wp_enqueue_script('sket-time-picker', plugin_dir_url(__FILE__) . 'js/flatpickr.min.js', array('jquery'), self::CSS_JS_VERSION, true);
                wp_enqueue_script('sket-place-address', plugin_dir_url(__FILE__) . 'js/mapMarkerCreate.js', array('jquery'), self::CSS_JS_VERSION, true);

                $geo_data = SKET_Db::sket_get_geo_data_object($post->ID);
                
                

                wp_localize_script('sket-place-address', 'sketch_map_data', array(
                    'cluster_images_url' => plugin_dir_url(__FILE__) . 'images/m',
                    'security' => wp_create_nonce('sket_google_places_nonce'),
                    'post_id' => $post->ID,
                    'lat' => get_option('sket_map_centre_lat'),
                    'lng' => get_option('sket_map_centre_lng'),
                    'loclat' => $geo_data->lat,
                    'loclng' => $geo_data->lng,
                    'sketch_marker_icon' => plugin_dir_url(__FILE__) . 'icons/sketch.png',
                    
                    
                ));
            }
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

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
//		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sket-sketch-manager-admin.js', array( 'jquery' ), $this->version, false );
    }

    public function register_widgets() {

        register_widget('Sket_Recent_Sketches');
    }

    public function get_option_name() {

        return $this->option_name;
    }

    /**
     * Add an options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function add_options_page() {

        $this->plugin_screen_hook_suffix = add_options_page(
                __('Sketch Manager', TEXTDOMAIN), __('Sketch Manager', TEXTDOMAIN), 'manage_options', $this->plugin_name, array($this, 'display_options_page')
        );
    }

    /**
     * Add an options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function register_setting() {

        // Add a General section
        add_settings_section($this->option_name . '_general', __('General', TEXTDOMAIN), array($this, $this->option_name . '_general_cb'), $this->plugin_name);
        //               add_settings_section($this->option_name . '_sketch',__( 'Sketch', TEXTDOMAIN ),array( $this, $this->option_name . '_sketch_cb' ),$this->plugin_name);

        add_settings_field($this->option_name . '_google_api_key', __('Google_api_key', TEXTDOMAIN), array($this, $this->option_name . '_google_api_key_cb'), $this->plugin_name, $this->option_name . '_general', array('label_for' => $this->option_name . '_google_api_key'));
        add_settings_field($this->option_name . '_map_centre_lat', __('Map Centre Latitude', TEXTDOMAIN), array($this, $this->option_name . '_map_centre_lat_cb'), $this->plugin_name, $this->option_name . '_general', array('label_for' => $this->option_name . '_centre_lat'));
        add_settings_field($this->option_name . '_map_centre_lng', __('Map Centre Longitude', TEXTDOMAIN), array($this, $this->option_name . '_map_centre_lng_cb'), $this->plugin_name, $this->option_name . '_general', array('label_for' => $this->option_name . '_centre_lng'));

        register_setting($this->plugin_name, $this->option_name . '_google_api_key');
        register_setting($this->plugin_name, $this->option_name . '_map_centre_lat', array($this, $this->option_name . '_validate_latitude'));
        register_setting($this->plugin_name, $this->option_name . '_map_centre_lng', array($this, $this->option_name . '_validate_longitude'));
    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_options_page() {
        include_once 'partials/sket-sketch-manager-admin-display.php';
    }

    /**
     * Render the text for the general section
     *
     * @since  1.0.0
     */
    public function sket_general_cb() {
        echo '<p>' . __('Please change the settings accordingly.', TEXTDOMAIN) . '</p>';
    }

//        
//                      
//        /**
//	 * Render the text for the general section
//	 *
//	 * @since  1.0.0
//	 */
//	public function sket_sketch_cb() {
//		echo '<p>' . __( 'Please change the settings accordingly.', TEXTDOMAIN ) . '</p>';
//	}
//        
    /**
     * Render the map centre lat input for this plugin
     *
     */
    public function sket_map_centre_lat_cb() {
        $map_centre_lat = get_option($this->option_name . '_map_centre_lat');
        echo '<input type="text" name="' . $this->option_name . '_map_centre_lat' . '" id="' . $this->option_name . 'map_centre_lat' . '" value="' . $map_centre_lat . '"> ' . __(' Set map centre latitude', TEXTDOMAIN);
    }

    /**
     * Render the map centre lng input for this plugin
     *
     */
    public function sket_map_centre_lng_cb() {
        $map_centre_lng = get_option($this->option_name . '_map_centre_lng');
        echo '<input type="text" name="' . $this->option_name . '_map_centre_lng' . '" id="' . $this->option_name . 'map_centre_lng' . '" value="' . $map_centre_lng . '"> ' . __(' Set map centre longitude', TEXTDOMAIN);
    }

    /**
     * Render the google api key input for this plugin
     *
     */
    public function sket_google_api_key_cb() {
        $google_api_key = get_option($this->option_name . '_google_api_key');
        echo '<input type="text" name="' . $this->option_name . '_google_api_key' . '" id="' . $this->option_name . 'google_api_key' . '" value="' . $google_api_key . '"> ' . __(' Set google api key', TEXTDOMAIN);
    }

    /**
     * validate longitude
     */
    function sket_validate_longitude($lng) {
        if (null == $lng) {
            return $lng;
        }
        $lngprev = get_option($this->option_name . '_map_centre_lng');
        $val = $this->sket_sanitize_float($lng);
        if ($val) {
            $floatval = floatval($val);
            if ($floatval < -180 || $floatval > 180) {
                add_settings_error($this->option_name . '_map_centre_lng', 'sket_lng_range_fail', __('Longitude must be >= -90 or <=90 , please try again.', 'sketch'), 'error');
                return $lngprev;
            }
        } else {
            add_settings_error($this->option_name . '_map_centre_lng', 'sket_lng_numeric_fail', __('Longitude must be numeric , please try again.', 'sketch'), 'error');
            return $lngprev;
        }

        return $val;
    }

    /**
     * validate latitude
     */
    function sket_validate_latitude($lat) {
        if (null == $lat) {
            return $lat;
        }
        $latprev = get_option($this->option_name . '_map_centre_lng');
        $val = $this->sket_sanitize_float($lat);
        if ($val) {
            $floatval = floatval($val);
            if ($floatval < -90 || $floatval > 90) {
                add_settings_error($this->option_name . '_map_centre_lat', 'sket_lat_range_fail', __('Latitude must be >= -90 or <=90 , please try again.', 'sketch'), 'error');
                return $latprev;
            }
        } else {
            add_settings_error($this->option_name . '_map_centre_lat', 'sket_lat_numeric_fail', __('Latitude must be numeric , please try again.', 'sketch'), 'error');
            return $latprev;
        }
        return $val;
    }

    /**
     * Sanitize float field
     *
     */
    function sket_sanitize_float($value) {

        $var = sanitize_text_field($value);
        if (is_numeric($var)) {
            return $var;
        }
        return false;
    }

    function sandbox_theme_validate_input_examples($input) {

        // Create our array for storing the validated options
        $output = array();

        // Loop through each of the incoming options
        foreach ($input as $key => $value) {

            // Check to see if the current option has a value. If so, process it.
            if (isset($input[$key])) {

                // Strip all HTML and PHP tags and properly handle quoted strings
                $output[$key] = strip_tags(stripslashes($input[$key]));
            } // end if
        } // end foreach
        // Return the array processing any additional functions filtered by this action
        return apply_filters('sandbox_theme_validate_input_examples', $output, $input);
    }

}
