<?php

/**
 * The artist custom post type functionality of the plugin.
 *
 * @link       http://huttartsites.co.nz
 * @since      1.0.0
 *
 * @package    Sket_Sketch_Manager
 * @subpackage Sket_Sketch_Manager/admin
 */
class Sket_Artist {

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
    /* Declare fields and copnstants */
    const PLUG_IN_NAME = "sketch";
    const VERSION = "1.0.1";
    const PREFIX = "sket_";
    const ARTIST_PT = "sket_artist";
    const SAVENONCE = "sket_save_nonce";
    const ADMIN_ARTIST_CSS = 'sket_admin_artist_css';

    /* change the version when updating css or js files to force cache refresh */
    const CSS_JS_VERSION = "0.0.1";

    private $sket_db;

    /**
     * Constructor. Hooks all interactions to the initialise the class.
     * @since 0.0.1
     * @access public
     */
    Public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // add_action('admin_enqueue_scripts',array($this, 'om_admin_enqueue_scripts') );
    }

//        /**
//         * Enqueue css and JavaScript:
//         * Once they are Queued we check that they are in fact on the queuea. 
//         * Shouldnt have to do this but just for debugging for now
//         */
//        function om_admin_enqueue_scripts() {
//            global $pagenow, $typenow;
//            
//            $plugin_url = plugins_url(self::PLUG_IN_NAME.'/');
// 
//            if ($typenow == self::ARTIST_PT) {
//                wp_enqueue_style(self::ADMIN_ARTIST_CSS,$plugin_url.'css/admin-artist.css',array(),self::CSS_JS_VERSION);
//            }
//          }
    /**
     * Create Artist post type
     */
    function create_artist_post_type() {

        $singular = 'Artist';
        $plural = 'Artist Posts';

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
            'menu_position' => '7',
            'menu_icon' => 'dashicons-businessman',
            'can_export' => true,
            'delete_with_user' => false,
            'hierarchical' => true,
            'has_archive' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'rewrite' => array(
                'slug' => 'artist',
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
        register_post_type(self::ARTIST_PT, $args);
    }

    /**
     * add artists colunm to media list library listing
     * @param array $cols
     * @return string
     */
    function add_artist_column($cols) {
        $cols['sket_artist_col'] = "Artist";
        return $cols;
    }

    /**
     * Display artists name on media listing
     * @param type $column_name
     * @param type $id
     */
    function artist_value($column_name, $id) {

        if ('sket_artist_col' == $column_name) {
            $posttext = '';
            //gets the artist post id from the media attachement
            $artistID = get_post_meta($id, '_sket_artist');
            if ($artistID) {

                // get Artist post
                $artistPost = get_post($artistID[0]);
                if (!$artistPost) {
                    $posttext = 'Artist does not exist';
                } else {

                    $posttext = $artistPost->post_title;
                }
            } else {
                $posttext = 'Artist not assigned';
            }
            print_r($posttext);
        }
    }

    /**
     * Make artist colunm sortable
     * @param array $cols
     * @return string
     */
    function artist_column_sortable($cols) {
        $cols["sket_artist_col"] = "name";

        return $cols;
    }

    /**
     * Display list of links to sketch posts where artist is cited.
     *  
     * @global type $post
     * @param type $atts
     * @param type $content
     * @param type $name
     * @return string
     */
    function display_sketch_posts($atts, $content, $name) {

        global $post;

        $atts = shortcode_atts(array(
            'title' => 'Sketch Posts',
            'sketch-post-count' => 20,
            'pagination' => 'false'
                ), $atts);

        $paged = get_query_var('paged') ? get_query_var('paged') : 1;

        $arel = $this->sket_db->sket_get_artist_sketch_relationships($post->ID);

        $html = '<div class="image-list-title"><h3>';
        $html .= 'Sketch Posts' . '</h3></div>';

        if (!empty($arel)) {

            $sketch_array = array();

            foreach ($arel as $rel) {
                $sketch_array[] = $rel->sketch_id;
            }
            $args = array(
                'post_type' => 'sketch',
                'post__in' => $sketch_array,
                'post_status' => 'publish',
                'no_found_rows' => $atts['pagination'],
                'posts_per_page' => $atts['sketch-post-count'],
                'paged' => $paged
            );
            $sketch_posts = new WP_Query($args);
            $html .= '<div class="sket-sketch-posts">';
            if ($sketch_posts->have_posts()) {
                while ($sketch_posts->have_posts()) {
                    $sketch_posts->the_post();
                    $html .= '<p><a class="sket-sketch-post-entry" href="';
                    $html .= esc_html(get_the_permalink());
                    $html .= '">';
                    $html .= esc_html(get_the_title()) . '</a></p>';
                }
            }
            $html .= '</div>';
            wp_reset_postdata();

            // video add && is_page() but do we need to linmit it

            if ($sketch_posts->max_num_pages > 1) {

                $html .= '<nav class="sket-prev-next-posts">';
                $html .= '<div class="sket-nav-previous">';
                $html .= get_next_posts_link(__('<span class="sket-meta-nav">&larr;</span> Previous'), $sketch_posts->max_num_pages);
                $html .= '</div>';
                $html .= '<div class="sket-next-posts-link">';
                $html .= get_previous_posts_link(__('<span class="sket-meta-nav">&rarr;</span> Previous'));
                ;
                $html .= '</div>';
                $html .= '</nav>';
            }
        } else {
            $html .= '<div class="sket-message">The artist is not cited as the artist in any sketch posts.';
        }
        return $html;
    }

    /**
     * Display a list of artist sketches and creates html to display
     * sketches in a Colorbox.
     * The colorbox is triggered by image_group class tag.
     * TODO check what the rel parameter does.
     * @see js/colorbox-init.js
     * @param type $atts
     * @param type $content
     */
    function display_artist_sketches($atts, $content = null) {

        global $post;

        // Get the Artists images
        // they must be attached to the artist
        // See media library
        //
           
           $html = '<div class="image-list-title"><h3>';
        $html .= 'Sketches' . '</h3></div>';

        if (!empty(get_post_meta($post->ID, 'artists_works'))) {
            $media = get_post_meta($post->ID, 'artists_works')[0];

            $html .= '<div class="colorbox-images" >';
            for ($i = 0; $i < count($media); $i++) {
                //
                // The media may have been deleted and a reference to it is still in the array
                // So check that the media post exists before building html.
                //
                    if (get_post($media[$i])) {
                    $image = get_post($media[$i]);
                    $html .= '<p><a class="image_group" href="';
                    $html .= esc_html(wp_get_attachment_url($image->ID));
                    $html .= '">';
                    $html .= esc_html($image->post_title);
                    $html .= '</a></p>';
                }
            }
            $html .= '</div>';

            return($html);
        } else {
            $html .= '<div class="image-list-title">The Artist has no registered sketches</div>';
        }
        return $html;
    }

}
