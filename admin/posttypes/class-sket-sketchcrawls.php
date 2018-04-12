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
class Sket_SketchCrawls {
    /* Declare fields and copnstants */

    const POST_TYPE = 'crawls';
    const POST_TYPE_NAME = 'Sketch Crawl';
    const SAVENONCE = 'sket_sketchCrawls_save_nonce';
    const SKET_CRAWL_METABOX_TITLE = 'Sketch Crawl Details';

    /* Sketch Crawl Post type Meta Fields */
    const SKET_CRAWL_ATTENDEES = 'sket-crawl-attendees';
    const SKET_CRAWL_DATE = 'sket-crawl-place';
    const SKET_CRAWL_CAFE = 'sket-crawl-cafe';

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
        $plural = self::POST_TYPE_NAME . 's';

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
            'taxonomies' => array('Category',),
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
     * use CMB2 to set up sketchcrawl metabox and fields.
     */
    function create_sketchcrawl_metabox() {


        $cmb = new_cmb2_box(array(
            'id' => 'sketch_crawl_metabox',
            'title' => __(self::SKET_CRAWL_METABOX_TITLE, 'cmb2'),
            'object_types' => array(self::POST_TYPE,), // Post type
            'context' => 'side',
            'priority' => 'high',
            'show_names' => true, // Show field names on the left
        ));

        $cmb->add_field(array(
            'name' => 'Date',
            'desc' => __('Select Sketch Crawl Meeting Date'),
            'id' => self::SKET_CRAWL_DATE,
            'type' => 'text_date',
            // 'timezone_meta_key' => 'wiki_test_timezone',
            'date_format' => 'd/m/Y',
        ));


        $cmb->add_field(array(
            'name' => 'Attendees',
            'desc' => 'Enter the number of people who joined the Sketch Crawl',
            'id' => self::SKET_CRAWL_ATTENDEES,
            'type' => 'text_small',
            'attributes' => array(
                'type' => 'number',
                'pattern' => '\d*',
                'required' => 'required'
            )
        ));

        $cmb->add_field(array(
            'name' => 'cafe',
            'desc' => 'Enter the name of the Cafe',
            'id' => self::SKET_CRAWL_CAFE,
            'type' => 'text',
        ));
    }

    /**
     *  remove editor and thumbnail from editor screen
     */
    function remove_post_edit_features() {
        remove_post_type_support(self::POST_TYPE, 'editor');
        remove_post_type_support(self::POST_TYPE, 'thumbnail');
    }
    
    /**
     *  remove yoast seo metabox.
     */

    function remove_yoast_seo_metabox() {
        if (!current_user_can('edit_others_posts'))
            remove_meta_box('wpseo_meta', 'post', 'normal');
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
        $params['post_type'] = array('post', self::POST_TYPE);
        return $params;
    }

}
