<?php

/**
 * The sketch custom post type functionality of the plugin.
 *
 * @link       http://huttartsites.co.nz
 * @since      1.0.0
 *
 * @package    Sket_Sketch_Manager
 * @subpackage Sket_Sketch_Manager/admin
 */
class Sket_Sketch_Manager_Sketch {
    /* Declare fields and copnstants */

    const POST_TYPE = 'sketch';
    const POST_TYPE_NAME = 'Sketch';
    const SAVENONCE = 'sket_sketch_save_nonce';
    const METABOX_TITLE = 'Sketch Details';

    /* Sketch Post type Meta Fields */
    const ARTIST = "sket_artist";
    const DATE_SKETCHED = "sket_date_sketched";
    const SKET_ARTIST_OPTION = 'sket-artist-option';
    const SKET_PLACE = 'sket-place';
    const SKET_LAT = 'sket-lat';
    const SKET_LNG = 'sket-lng';
    const SKET_ADDRESS = 'sket-address';
    const SKET_STREET_NUMBER = 'sket-street-number';
    const SKET_ROUTE = 'sket-route';
    const SKET_POSTAL_CODE = 'sket-postal-code';
    const SKET_LOCALITY = 'sket-locality';
    const SKET_COUNTRY = 'sket-country';
    const SKET_SUBLOCALITY = 'sket-sublocality';
    const SKET_PLACE_ID = 'sket-place-id';

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
     * @param      string    $version           The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function create_post_type() {

        $singular = self::POST_TYPE_NAME;
        $plural = self::POST_TYPE_NAME . 'es';

        $labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'add_name' => 'Add New',
            'add_new_item' => 'Add New ' . $singular,
            'edit' => 'Edit',
            'edit_item' => 'Edit ' . $singular,
            'new_item' => 'New ' . $singular,
            'view' => 'View ' . $singular,
            'view_item' => 'View ' . $singular,
            'search_term' => 'Search ' . $plural,
            'parent' => 'Parent ' . $singular,
            'not_found' => 'No ' . $plural . ' found',
            'not_found_in_trash' => 'No ' . $plural . ' in Trash'
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_position' => '6',
            'menu_icon' => 'dashicons-businessman',
            'can_export' => true,
            'delete_with_user' => false,
            'hierarchical' => false,
            'has_archive' => true,
            'query_var' => true,
            'taxonomies' => array('Category', 'post_tag'),
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'rewrite' => array(
                'slug' => substr(self::POST_TYPE, 5),
                'with_front' => true,
                'pages' => true,
                'feeds' => false
            ),
            'supports' => array(
                'title',
                'editor',
                'thumbnail'
            )
        );
        register_post_type(self::POST_TYPE, $args);
    }
    
    /**
         * Create Taxomony
         * 
         */
        public function create_location_taxonomy() {
            
            $plural = 'Locations';
            $singular = 'Location';
            
            $labels = array(
                'name'                      => $plural,
                'singular_name'             => $singular,
                'search_items'              => 'Search ' . $plural,
                'popular_items'             => 'Popular ' . $plural,
                'all_items'                 => 'All '. $plural,
                'edit_item'                 => 'Edit ' . $singular,
                'update_item'               => 'Update ' . $singular,
                'add_new_item'              => 'Add New ' . $singular,
                'new_item_name'             => 'New ' . $singular . ' Name',
                'separate_items_with_commas'=> 'Separate ' . $plural . ' with commas',
                'add_or_remove_items'       => 'Add or remove '.$plural,
                'Choose_from_most_used'     => 'Choose from most used ' . $singular,
                'not_found'                 => 'No ' . $singular . ' found',
                'menu_name'                 => $plural
            );
            
            $args = array(
                'hierarchical'              => true,
                'labels'                    => $labels,
                'show_ui'                   => true,
                'show_admin_column'         => true,
                'update_count_callback'     => '_update_post_term_count',
                'query_var'                 => true,
                'rewrite'                   => array( 'slug' => 'location')
            );
            
            register_taxonomy('location', self::POST_TYPE, $args);
        }
        

    /**
     * Gets the single for this post type
     * @global type $post
     * @param type $template
     * @return type
     */
    public function get_template($template) {

        global $post;
        if (!isset($post)) {
            return $template;
        }
        
        if ($post->post_type !== self::POST_TYPE) {
            return $template;
        }
        return sket_get_post_type_template($post->post_type, $template);
    }

    /**
     * create release meta box 
     */
    function create_metabox() {

        add_meta_box(
                self::POST_TYPE . '_metabox', __(self::METABOX_TITLE), array($this, 'meta_callback'), self::POST_TYPE, 'side', 'high'
        );
    }

    /**
     * Displays Sketch Metabox.
     * @param type $post
     */
    function meta_callback($post) {

        // get post metadata
        wp_nonce_field(basename(__FILE__), self::SAVENONCE);

        require_once plugin_dir_path(__FILE__) . 'metaboxes/sket-sketch-metabox.php';

        wp_reset_postdata();
    }

    /*
     * Update post parent product
     */

    function save_meta_data($post_id) {

        
        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);
        $is_valid_nonce = false;
        if (isset($_POST[self::SAVENONCE])) {
            if (wp_verify_nonce($_POST[self::SAVENONCE], basename(__FILE__))) {
                $is_valid_nonce = true;
            }
        }

        if (!$is_valid_nonce || $is_autosave || $is_revision) {
            return;
        }
        if (isset($_POST[self::ARTIST])) {
            update_post_meta($post_id, self::ARTIST, sanitize_text_field($_POST[self::ARTIST]));
        }

        if (isset($_POST[self::DATE_SKETCHED])) {
            update_post_meta($post_id, self::DATE_SKETCHED, sanitize_text_field($_POST[self::DATE_SKETCHED]));
        }

        if (isset($_POST[self::SKET_ARTIST_OPTION])) {

            foreach ($_POST[self::SKET_ARTIST_OPTION] as $artist_id) {
                sanitize_text_field($artist_id);
            }

            $selected_artists_string = implode(',', $_POST[self::SKET_ARTIST_OPTION]);
            $selected_artists = explode(',', $selected_artists_string);
            SKET_db::sket_update_artist_sketch_relationships($post_id, $selected_artists);
        }
        $update_geo = false;
        $geodata = SKET_db::sket_initialise_geodata();
        
        if (isset($_POST[self::SKET_ADDRESS])) {
            
            $geodata->sket_address = sanitize_text_field($_POST[self::SKET_ADDRESS]);
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_LAT])) {
            
            $geodata->sket_lat = floatval(sanitize_text_field($_POST[self::SKET_LAT]));
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_LNG])) {
            
            $geodata->sket_lng = floatval(sanitize_text_field($_POST[self::SKET_LNG]));
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_STREET_NUMBER])) {
            
            $geodata->sket_street_number = sanitize_text_field($_POST[self::SKET_STREET_NUMBER]);
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_ROUTE])) {
            
            $geodata->sket_route = sanitize_text_field($_POST[self::SKET_ROUTE]);
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_POSTAL_CODE])) {
            
            $geodata->sket_postal_code = sanitize_text_field($_POST[self::SKET_POSTAL_CODE]);
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_LOCALITY])) {
            
            $geodata->sket_locality = sanitize_text_field($_POST[self::SKET_LOCALITY]);
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_SUBLOCALITY])) {
            
            $geodata->sket_sublocality = sanitize_text_field($_POST[self::SKET_SUBLOCALITY]);
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_COUNTRY])) {
            
            $geodata->sket_country = sanitize_text_field($_POST[self::SKET_COUNTRY]);
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_PLACE_ID])) {
            
            $geodata->sket_place_id = sanitize_text_field($_POST[self::SKET_PLACE_ID]);
            $update_geo = true;
        }
        if (isset($_POST[self::SKET_PLACE])) {
            
            $geodata->sket_place = sanitize_text_field($_POST[self::SKET_PLACE]);
            $update_geo = true;
        }
        if ($update_geo) {
            SKET_db::sket_update_geodata($post_id, $geodata);
            
            // update location category taxonomy.
            // check country.
            $country = term_exists($geodata->sket_country, 'location');
            if ($country !== 0 && $country !== null) {
                $country_id = $country;
                if (is_array($country)) {
                    $country_id = $country['term_id'];
                }
            } else { // we have one so get $country_ID 
                $result = wp_insert_term($geodata->sket_country,'location');
                delete_option('location_children');
                wp_cache_flush();
                $country_id = $result['term_id'];
            }
            // Update post with country
            $post_result = wp_set_post_terms($post_id,array($country_id),'location',true);

            // check locality.
            $locality_exists = true;
            $locality = term_exists($geodata->sket_locality, 'location', $country_id);
            if ($locality !== 0 && $locality !== null) {
                $locality_id = $locality;
                if (is_array($locality)) {
                    $locality_id = $locality['term_id'];
                }
            } else {
                $result = wp_insert_term($geodata->sket_locality,'location',array('parent'=>$country_id));
                delete_option('location_children');
                wp_cache_flush();
                $locality_id = $result['term_id'];
                $locality_exists = false;
                
             }
             // Update post with locaity
            $post_result = wp_set_post_terms($post_id,array($locality_id),'location',true);
            // check sub_locality.
            $sub_locality_exists = true;
            $sub_locality = term_exists($geodata->sket_sublocality, 'location', $locality_id);
            if ($sub_locality !== 0 && $sub_locality !== null) {
                $sub_locality_id = $sub_locality;
                if (is_array($sub_locality)) {
                    $sub_locality_id = $sub_locality['term_id'];
                }
            } else {
                $result = wp_insert_term($geodata->sket_sublocality,'location',array('parent'=>$locality_id));
                delete_option('location_children');
                wp_cache_flush();
                $sub_locality_id = $result['term_id'];
                $sub_locality_exists = false;
  
            }
            
            // Update post with sub Locality
            $post_sub_result = wp_set_post_terms($post_id,array($sub_locality_id),'location',true);
            
        }
    }

    function add_columns($cols) {
        //        $cols[self::START_DATE] = "Opening";
        //        $cols[self::END_DATE] = "Closes";
        //        $cols[self::PARENT_POST_TYPE] = "Venue";
        return $cols;
    }

    /**
     * Sortable cols
     * @param array $cols
     * @return columns
     */
    function sortable_columns($cols) {

        //     $cols[self::START_DATE] = self::START_DATE;
        //     $cols[self::END_DATE] = self::END_DATE;
        //     $cols[self::PARENT_POST_TYPE] = self::PARENT_POST_TYPE;
        return $cols;
    }

    function display_columns($column_name, $post_id) {
        global $post;

//            $sm = get_metadata('post',$post->ID);
//            $startDate = ! empty( $sm[self::START_DATE] ) ? new DateTime($sm[self::START_DATE][0]) : '';
//            $endDate = ! empty( $sm[self::END_DATE] ) ? new DateTime($sm[self::END_DATE][0]) : '';
//            switch ($column_name) {
//                case self::START_DATE: {
//                    echo $startDate->format('Y-m-d');
//                    break;
//                }
//                case self::END_DATE: {
//                    echo $endDate->format('Y-m-d');
//                    break;
//                }
//                //Venue: Venue ID is the parent of an sketch
//                case self::PARENT_POST_TYPE: {
//                    $args = array(
//                        'p'                 => $post->post_parent,
//                        'post_type'         => self::PARENT_POST_TYPE,
//                        'post_status'       => 'publish',
//                        'number'            => '1',
//                    );
//                    $venues = new WP_Query($args);
// 
//                    if ( $venues->have_posts()) {
//                        while ($venues->have_posts()) {
//                            $venues->the_post();
//                            print_r (esc_html(get_the_title()));
//                        }
//                    }
//                    
//                    break;
//                }
//            }
    }

    /**
     * add post type to archive post types
     * @param type $query
     * @return type
     */
    function include_posttype_on_category_archive(&$query) {

        if ($query->is_author) {
            $query->set('post_type', array('post', self::POST_TYPE));
        }
        remove_action('pre_get_posts', 'include_posttype_on_category_archive'); // run once!
    }

    /**
     * alters the recent post loop to add sketch posts
     * @param array $params
     * @return string
     */
    function sket_widget_posts_args_add_custom_type($params) {
        //TODO This adds a new post_type array to to WP_Query
        // Before adding array check that there is not already
        // an array present add to end if there is.
        $params['post_type'] = array('post', self::SKETCH_PT);
        return $params;
    }

    /**
     * gets data for post to be used to display sketcher post data in the blog
     * this fuction can only called when in a loop
     * @param type $post
     * @return \stdClass
     */
    public function sket_get_sketcher_post_details($post_ID) {

        $details = new stdClass();
        $date_time = '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a><time class="updated" datetime="%5$s">%6$s</time>';
        $date_time = sprintf($date_time, esc_url(get_permalink()), esc_attr(get_the_time()), esc_attr(get_the_date('c')), esc_html(get_the_date()), esc_attr(get_the_modified_date('c')), esc_html(get_the_modified_date())
        );

        $author = sprintf('<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(sprintf('View all posts by %s', get_the_author())), get_the_author()
        );


        //	$comments_link = '<span class="comments-link"><a href="' . get_comments_link() . '">' . get_comments_number_text( 'Leave a comment' ) . '</a></span>';

        $posted_on_parts = array(
            'on' => sprintf('Posted on %s', $date_time),
            'by' => sprintf('<span class="byline"> by %s</span>', $author)
                //		'with' => ' | ' . $comments_link
        );
        $details->posted_on = implode(' ', $posted_on_parts);


        $arel = SKET_db::sket_get_sketch_artist_relationships($post_ID);
        $artists_array = array();

        foreach ($arel as $rel) {
            $artists_array[] = get_post($rel->artist_id);
        }
        //
        $details->additional_artists = $artists_array;
        $stored_meta = get_metadata('post', $post_ID);

        $details->date_sketched = 'No date sketched was recorded';
        $details->artist = 'No artist was recorded';
        //
        if (!empty($stored_meta)) {

            if (isset($stored_meta['sket_date_sketched'])) {

                $details->date_sketched = $stored_meta['sket_date_sketched'][0];
            }
            if (isset($stored_meta['sket_artist'])) {
                $details->artist = $stored_meta['sket_artist'][0];
            }
        }
        //
        return $details;
    }
    
    /**
     * Handles ajax Post to get map longitude and latitude for location
     */
    
    function sket_map_select_handler() {
        
        om_log('sket_map_select_handler');
        // check nonce 'sket_map_ajax_nonce'
        if (! isset($_POST['sket_map_ajax_nonce'])){
            wp_send_json_error('Security Error - nonce is not set');
        }
        if (!wp_verify_nonce($_POST['sket_map_ajax_nonce'],'sket_map_ajax_nonce')) {
            wp_send_json_error('Security Error - Invalid nonce');
        }
        // check selected_location is set and and integer
        if (!isset($_POST['sket_map_location'])) {
            wp_send_json_error('The Post does not contain sket_map_location');
        }
        $location_id = intval($_POST['sket_map_location']);
        if ( $$location_id == 0 ) {
            wp_send_json_error('There was something wrong with the selected location...' + $location_id);
        }
        // get term metadata for selected location
        
        $latitude = get_term_meta($location_id,'sket_lat',true);
        $longitude = get_term_meta($location_id,'sket_lng',true);
        
        $response = array(
            'latitude' => $latitude,
            'longitude' => $longitude,
            'zoomLevel' => '11'
        );
        wp_send_json_success($response);
        
    }

    function sket_update_post_meta($post_ID, $post_type, $post_data_name, $value) {

        
        return null;
    }

    

    /*
     * gets the most current value from transient or from post metadata
     */

    function get_post_transient_or_meta($post_id, $meta_key) {
        
        return null;
    }
    

}
