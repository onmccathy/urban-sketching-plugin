<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Sket_Sketch_Tests {
    /* Declare fields and copnstants */

    const POST_TYPE = 'sketch';
    const POST_TYPE_NAME = 'Sketch';
    const SAVENONCE = 'sket_sketch_save_nonce';
    const METABOX_TITLE = 'Sketch Details';

    /* Sketch Post type Meta Fields */
    const ARTIST = "sket_artist";
    const DATE_SKETCHED = "sket_date_sketched";
    const SKET_ARTIST_OPTION = 'sket-artist-option';

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
     * The options name to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	private
     * @var  	string 		$option_name 	Option name of this plugin
     */
    public $number_of_tests = 0;
    public $passed_tests = 0;
    public $failed_tests = 0;

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

    public function sket_do_tests() {
       
        $testsAlreadyRunTransient = 'sket-tests_already_run';
        
        if (false === ( get_transient($testsAlreadyRunTransient))) {
            
               // only run tests about every 5 minutes

            om_log(__CLASS__ . 'about to start sketch tests');
            //          $this->test_geo_postmeta_update();
            //          $this->test_google_geocoder();
            $this->test_transient_post_data1();
            $this->test_transient_post_data2();
          
            om_log('.....................Test Results...................');
            om_log('Number of Tests Executed: ' . $this->number_of_tests);
            om_log('                Success : ' . $this->passed_tests);
            om_log('                Failed  : ' . $this->failed_tests);
            
            set_transient($testsAlreadyRunTransient, 'Tests have run', 5 * MINUTE_IN_SECONDS);
        }
    }

    private function test_geo_postmeta_update() {
        $this->number_of_tests = $this->number_of_tests + 1;
        $post_ID = 501;
        $post_type = 'sketch';
        $post_data_name = 'address';
        $value = '11 Viscount Grove,Loweer Hutt, NZ, 5010';

        Sket_Sketch_Manager_Sketch::sket_update_post_meta($post_ID, $post_type, $post_data_name, $value);

        $address = get_post_meta($post_ID, 'sket_sketch_address');

        if (strcmp(trim($address[0]), trim($value)) != 0) {
            $this->failed_tests = $this->failed_tests + 1;
            om_log(__FILE__ . ' ' . __LINE__ . ' ' . __FUNCTION__ . ' Test failed ');
            om_log($address[0]);
            om_log($value);
        } else {
            $this->passed_tests = $this->passed_tests + 1;
            om_log(__FILE__ . ' ' . __LINE__ . ' ' . __FUNCTION__ . ' Test Succecced ');
            // cleanup 
            //delete post post meta just updated
            delete_post_meta($post_ID, 'sket_sketch_address', $value);
        }
    }

    private function test_google_geocoder() {
        $this->number_of_tests = $this->number_of_tests + 1;
        $address = Sket_Geo::get_address_for_latlng(-41.2495804, 174.9057770, false);

        if (strcmp($address->post_code, '5010') != 0) {
            $this->failed_tests = $this->failed_tests + 1;
            om_log(__FILE__ . ' ' . __LINE__ . ' ' . __FUNCTION__ . ' Test failed ');
            om_log($address->post_code);
        } else {
            $this->passed_tests = $this->passed_tests + 1;
        }
    }

    private function test_transient_post_data1() {

//        $this->number_of_tests = $this->number_of_tests + 1;
//        // prepare 
//        // create post
//        $post_id = $this->sket_test_create_post();
//        if ($post_id < 0) {
//            $this->failed_tests = $this->failed_tests + 1;
//            om_log('test post was not created');
//            om_log(__FILE__ . ' ' . __LINE__ . ' ' . __FUNCTION__ . ' Test failed ');
//        } else {
//            set_transient('sket_test' . $post_id, '1234');
//            // set transient
//            $transient = Sket_Sketch_Manager_Sketch::get_post_transient_or_meta($post_id, 'sket_test');
//
//            if (strcmp($transient, '1234') != 0) {
//                $this->failed_tests = $this->failed_tests + 1;
//                om_log(__FILE__ . ' ' . __LINE__ . ' ' . __FUNCTION__ . ' Test failed ');
//            } else {
//                $this->passed_tests = $this->passed_tests + 1;
//            }
//        }
    }

    private function test_transient_post_data2() {

//        $this->number_of_tests = $this->number_of_tests + 1;
//        // prepare 
//        $meta_key = 'sket_test2';
//        // 
//        // create post
//        $post_id = $this->sket_test_create_post();
//        if ($post_id < 0) {
//            $this->failed_tests = $this->failed_tests + 1;
//            om_log('test post was not created');
//            om_log(__FILE__ . ' ' . __LINE__ . ' ' . __FUNCTION__ . ' Test failed ');
//        } else {
//            update_post_meta($post_ID, $meta_key, 'Test2');
//            $transient = Sket_Sketch_Manager_Sketch::get_post_transient_or_meta($post_id, $meta_key);
//
//            if (strcmp($transient, 'Test2') != 0) {
//                $this->failed_tests = $this->failed_tests + 1;
//                om_log(__FILE__ . ' ' . __LINE__ . ' ' . __FUNCTION__ . ' Test failed ');
//                om_log('expected: ' . 'Test2' . 'actual: ' . $transient);
//            } else {
//                $this->passed_tests = $this->passed_tests + 1;
//            }
//        }
//
//        // cleanup
//        wp_delete_post($post_id);
//        delete_post_meta($post_id, $meta_key);
//        // Delete post
    }

    /**
     * A function used to programmatically create a post in WordPress. The slug, author ID, and title
     * are defined within the context of the function.
     *
     * @returns -1 if the post was never created, -2 if a post with the same title exists, or the ID
     *          of the post if successful.
     */
    function sket_test_create_post() {

        // Initialize the page ID to -1. This indicates no action has been taken.
        $post_id = -1;

        // Setup the author, slug, and title for the post
        $author_id = 1;
        $slug = 'test-post';
        $title = 'test post';

        // If the page doesn't already exist, then create it
        if (null == get_page_by_title($title)) {

            // Set the post ID so that we know the post was created successfully
            $post_id = wp_insert_post(
                    array(
                        'comment_status' => 'closed',
                        'ping_status' => 'closed',
                        'post_author' => $author_id,
                        'post_name' => $slug,
                        'post_title' => $title,
                        'post_status' => 'publish',
                        'post_type' => 'post'
                    )
            );

            // Otherwise, we'll stop
        } else {

            // Arbitrarily use -2 to indicate that the page with the title already exists
            $post_id = -2;
        } // end if

        return $post_id;
    }
}
