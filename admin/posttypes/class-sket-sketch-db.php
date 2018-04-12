<?php

if (!defined('WPINC')) {
    exit;
}

class SKET_db {

    const SKET_DB_VERSION_OPTION_KEY = 'sket_db_version';
    const SKET_RELATIONSHIP_TABLE_NAME = 'sket_artist_sketch_relationship';
    const SKET_GEO_RELATIONSHIP_TABLE_NAME = 'sket_geo_relationship';
    const SKET_GEO_TABLE_NAME = 'sket_geo';
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

    private static $instance;

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
     * Constructor. Hooks all interactions to the initialise the class.
     * @since 0.0.1
     * @access public
     */
    public function __construct($plugin_name, $version) {
        
        
        $this->plugin_name = $plugin_name;
        $this->version = 0.3;
        SKET_db::registerTables();
        SKET_db::sket_db_upgradecheck();
        // clean up on plugin uninstall
        register_uninstall_hook(__FILE__, 'sket_uninstall_db');

    }
    
    function registerTables() {
        global $wpdb;
        $wpdb->sket_geo = "{$wpdb->prefix}sket_geo";
        $wpdb->sket_artist_sketch_relationship = "{$wpdb->prefix}sket_artist_sketch_relationship";
    }
    
    function sket_db_upgradecheck() {
        
         //Database version - this may need upgrading.
        $installed_version = get_option(self::SKET_DB_VERSION_OPTION_KEY);

        if (!$installed_version) {
            //No installed version - we'll assume its just been freshly installed
            SKET_db::sket_create_geo_tables();

            add_option(self::SKET_DB_VERSION_OPTION_KEY, $this->version);
        } elseif ($installed_version != $this->version) {
            /*
             * If this is an old version, perform some updates.
             */
            //Installed version is before 0.2 - upgrade to 0.2
            if (version_compare('0.3', $installed_version)) {
                //Code to upgrade to version 0.3
                SKET_db::sket_create_geo_tables();
            }

            //Database is now up to date: update installed version to latest version
            update_option(self::SKET_DB_VERSION_OPTION_KEY, $this->version);
        }
    }
    
    function sket_create_geo_tables() {
        
        
        // Sets some constants and loads the dbDelta function.
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        global $wpdb;
        global $charset_collate;
        // Call this manually as we may have missed the init hook
        SKET_db::sket_register_geo();
        $wpdb->query("DROP TABLE IF EXISTS $wpdb->sket_geo");
        $sql_create_geo_table = "CREATE TABLE {$wpdb->sket_geo} (
            geo_id bigint(20) unsigned NOT NULL auto_increment,
            post_id bigint(20) unsigned NOT NULL default '0',
            type varchar(10) NOT NULL,
            lat FLOAT(11,7) NOT NULL,
            lng FLOAT(11,7) NOT NULL,
            street_number varchar(10) NULL,
            route varchar(50) NULL,
            sub_locality varchar(50) NULL,
            locality varchar(50) NULL,
            postal_code varchar(10) NULL,
            country varchar(30) NULL,
            address TINYTEXT NULL,
            place varchar(30) NULL,
            place_id TINYTEXT NULL,
            PRIMARY KEY  (geo_id),
            KEY lat (lat),
            KEY lng (lng),
            KEY country (country)

        ) $charset_collate; ";
        dbDelta($sql_create_geo_table);
    }


    function sket_do_tests($version) {
        
        SKET_db::get_address_for_latlng_test();
    }

    function sket_register_artist_sketch_relationship() {

        global $wpdb;
        $wpdb->sket_artist_sketch_relationship = "{$wpdb->prefix}sket_artist_sketch_relationship";
    }

    
    function sket_register_geo() {

        global $wpdb;
        $wpdb->sket_geo = "{$wpdb->prefix}sket_geo";
    }

    

    function sket_create_artist_sketch_relationship_tables() {

        // Sets some constants and loads the dbDelta function.
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        global $wpdb;
        global $charset_collate;
        // Call this manually as we may have missed the init hook
        SKET_db::sket_register_artist_sketch_relationship();
        // Set up the create table sql
        $sql_create_table = "CREATE TABLE {$wpdb->sket_artist_sketch_relationship} (
            rel_id bigint(20) unsigned NOT NULL auto_increment,
            artist_id bigint(20) unsigned NOT NULL default '0',
            sketch_id bigint(20) unsigned NOT NULL default '0',
            PRIMARY KEY  (rel_id) 
        ) $charset_collate; ";
        dbDelta($sql_create_table);
    }

    
    

    function sket_uninstall_db() {
        global $wpdb;
        //Remove our table (if it exists)
//        $wpdb->query("DROP TABLE IF EXISTS $wpdb->sket_artist_sketch_relationship");
//        $wpdb->query("DROP TABLE IF EXISTS $wpdb->sket_geo");
//        $wpdb->query("DROP TABLE IF EXISTS $wpdb->sket_geo_relationship");
//
//        //Remove the database version
//        delete_option(self::SKET_DB_VERSION_OPTION_KEY);

        /* Remove any other options your plug-in installed and clear any plug-in cron jobs */
    }

    /*
     * Adds sketch to artist post releationsips.
     * if the artists array is empty then we just delete existing relationships  
     * @parm $post_id - The post Id.
     * @parm $artists - an array of artist post ids.
     */

    public function sket_update_artist_sketch_relationships($post_id, $artists) {

        global $wpdb;

        // first check if there are any artist relationships with this  sketch post_id
        $relationships = SKET_db::sket_get_sketch_artist_relationships($post_id);

        if (!empty($relationships)) {
            // we have relationships so delete them

            SKET_db::sket_delete_sketch_artist_relationship($post_id);
        }
        // if the artists array is empty justreturn
        if (empty($artists)) {
            return;
        }
        // add new relationships for this sketch post

        $post_id_num = (int) $post_id;
        //       sket_artist_sketch_relationship

        $items = count($artists);
        for ($i = 0; $i < $items; $i++) {
            $artist_id_num = (int) $artists[$i];

            $inserted = $wpdb->insert(
                    $wpdb->sket_artist_sketch_relationship, array(
                'artist_id' => $artist_id_num,
                'sketch_id' => $post_id_num
                    ), array(
                '%d',
                '%d'
                    )
            );
            if ($inserted) {
                
            } else {
                new WP_Error('sket_sketch_artist_insert', __('Sketch / Artist relationship was not inserted'));
            }
        }
    }

    public function sket_delete_sketch_artist_relationship($sketch_post_type) {
       
        global $wpdb;
        $deleted = $wpdb->delete(
                $wpdb->sket_artist_sketch_relationship, array(
            'sketch_id' => $sketch_post_type,
                ), array(
            '%d',
                )
        );
        if ($deleted) {
            return true;
        } else {
            new WP_Error('sket_sketch_artist_insert', __('Could not delete Sketch / Artist relationship was not inserted'));
            return false;
        }
    }

    public function sket_get_sketch_artist_relationships($post_id) {

        global $wpdb;

        $sql = $wpdb->prepare("SELECT* FROM {$wpdb->sket_artist_sketch_relationship} 
                WHERE sketch_id = %d", $post_id);
        $relationships = $wpdb->get_results($sql);
        return $relationships;
    }

    

    public function insert_post_geodata($post_id, $geodata, $type) {
        
        global $wpdb;
        $wpdb->show_errors = TRUE;
        $wpdb->suppress_errors = FALSE;
        $inserted = $wpdb->insert(
                $wpdb->sket_geo, array(
            'post_id' => $post_id,
            'type' => $type,
            'lat' => $geodata->sket_lat,
            'lng' => $geodata->sket_lng,
            'address' => $geodata->sket_address,
            'place_id' => $geodata->sket_place_id,
            'country' => $geodata->sket_country,
            'locality' => $geodata->sket_locality,
            'sub_locality' => $geodata->sket_sublocality,
            'postal_code' => $geodata->sket_postal_code,
            'route' => $geodata->sket_route,
            'street_number' => $geodata->sket_street_number,
            'place' => $geodata->sket_place
                ), array(
            '%d',
            '%s',
            '%f',
            '%f',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
                )
        );
        if ($inserted) {

            return $wpdb->insert_id;
        } else {
            new WP_Error('create_sket_geo_record', __('Could not insert sket_geo record / Geo record was not inserted'));
            return false;
        }
    }

    public function update_post_geodata($post_id, $geo_key, $geodata, $type) {
        

        global $wpdb;
        $wpdb->show_errors = TRUE;
        $wpdb->suppress_errors = FALSE;
        $updated = $wpdb->update(
                $wpdb->sket_geo, array(
            'post_id' => $post_id,
            'type' => $type,
            'lat' => $geodata->sket_lat,
            'lng' => $geodata->sket_lng,
            'address' => $geodata->sket_address,
            'place_id' => $geodata->sket_place_id,
            'country' => $geodata->sket_country,
            'locality' => $geodata->sket_locality,
            'sub_locality' => $geodata->sket_sublocality,
            'postal_code' => $geodata->sket_postal_code,
            'route' => $geodata->sket_route,
            'street_number' => $geodata->sket_street_number,
            'place' => $geodata->sket_place
                ), array('geo_id' => $geo_key), array(
            '%d',
            '%s',
            '%f',
            '%f',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
                )
        );
        if ($updated) {

            return $wpdb->insert_id;
        } else {
            new WP_Error('create_sket_geo_record', __('Could not insert sket_geo record / Geo record was not inserted'));
            return false;
        }
    }

    /**
     * gets lat lng for post
     * returns array containing the two lat lng variables of type float.
     * if not found lat lng will be set to zero.
     * @param type $post_id
     */
    public function get_lat_lng($post_id) {
        // get post sket geo relationship
        // if not found return array as latlng
        // get sket-geo record
        // return sket geo lat lng

        global $wpdb;

        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->sket_geo_relationship} 
                WHERE post_image_id = %d", $post_id);
        $relationships = $wpdb->get_results($sql);

        foreach ($relationships as $rel) {
            // get sket_geo
            $sql2 = $wpdb->prepare("SELECT * FROM {$wpdb->sket_geo} 
                WHERE geo_id = %d", $rel->geo_id);
            $geo_records = $wpdb->get_results($sql2);
            
            foreach ($geo_records as $geo) {
                $latlng = array(
                    floatval($geo->lat),
                    floatval($geo->lng),
                );

                return $latlng;
            }
        }
        return false;
    }

    /**
     * gets all geo records
     * returns array containing the two lat lng variables of type float.
     * if not found lat lng will be set to zero.
     * @param type $post_id
     */
    public function get_all_geo_records() {

        global $wpdb;
        // get sket_geo
        $sql2 = "SELECT DISTINCT p.post_title, geo.post_id, geo.lat, geo.lng, geo.address FROM {$wpdb->sket_geo} geo"
        . " INNER JOIN {$wpdb->posts} p ON geo.post_id = p.ID";
        
        $geo_records = $wpdb->get_results($sql2);
       
        $data = array();
        foreach ($geo_records as $geo) {
            $geo_data = new stdClass();
            $geo_data->post_id = $geo->post_id;
            $geo_data->permalink = get_permalink($geo->post_id);
            $geo_data->thumbnail_url = get_the_post_thumbnail_url($geo->post_id,'thumbnail');
            $geo_data->post_title = esc_attr($geo->post_title);
            $geo_data->address = esc_attr($geo->address);
            $geo_data->lat = $geo->lat;
            $geo_data->lng = $geo->lng;
            $data[] = $geo_data;
        }

        return $data;
    }

    public function sket_initialise_geodata() {
        $geodata = new stdClass();

        $geodata->sket_address = '';
        $geodata->sket_place_id = '';
        $geodata->sket_lat = get_option('sket_map_centre_lat');
        $geodata->sket_lng = get_option('sket_map_centre_lng');
        $geodata->sket_country = '';
        $geodata->sket_sublocality = '';
        $geodata->sket_locality = '';
        $geodata->sket_postal_code = '';
        $geodata->sket_route = '';
        $geodata->sket_street_number = '';
        $geodata->sket_place = '';

        return $geodata;
    }

    /**
     * updates geo table with new geodata
     * if geodata record does not exist for post code then 
     * create it otherwise update it.
     * 1. get georecord id for post_id
     * 2. if it doen't exist then instert new record and add new gorecord key to metadata
     * 3. if it exists then get georecord and update it.
     * @param type $post_id
     * @param type $geodata
     */
    public function sket_update_geodata($post_id, $geodata, $type = 'post') {
        // get post metadata geo_key
        if (get_post_meta($post_id, 'sket-geo-key')) {
            $geo_key = get_post_meta($post_id, 'sket-geo-key')[0];
            SKET_db::update_post_geodata($post_id, $geo_key, $geodata, $type);
        } else {
            $geo_key = SKET_db::insert_post_geodata($post_id, $geodata, $type);
            update_post_meta($post_id, 'sket-geo-key', $geo_key);
        }
    }

    /**sket_get_geo_data_object()
     * get_geo_data_object returns an object with all fields from sket-geo with all fields
     * for a post with ID equal to $post-id.
     * if none are found it will return an object with all fields set to null.
     * @param type $post_id
     */
    Public function sket_get_geo_data_object($post_id) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT* FROM {$wpdb->sket_geo} 
                WHERE post_id = %d", $post_id);
        $geo_data = $wpdb->get_row($sql);
        
        return $geo_data;
    }

}
