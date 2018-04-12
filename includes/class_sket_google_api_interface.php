<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Sket_Geo {

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

    public function get_address_for_latlng($lat, $lng, $testfunction = false) {
        
        
        $here = __FILE__ . ' ' . __LINE__ . ' ' . __FUNCTION__;
        $addr = new stdClass();
        
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&key=' . get_option('sket_google_api_key');
        $result = file_get_contents($url);
        $geoAddress = json_decode($result, true);

        $addr1 = $geoAddress['results'][0];
        $addr->formatted_address = $addr1['formatted_address'];
        $addr->place_id = $addr1['place_id'];
        $addr->address_components = $addr1['address_components'];
        
        $addr->street_number = Sket_Geo::get_addr_value($addr1['address_components'], 'street_number', 'long_name');
        $addr->route = Sket_Geo::get_addr_value($addr1['address_components'], 'route', 'long_name');
        $addr->sublocality_level_1 = Sket_Geo::get_addr_value($addr1['address_components'], 'sublocality_level_1', 'long_name');
        $addr->locality = Sket_Geo::get_addr_value($addr1['address_components'], 'locality', 'log_name');
        $addr->administrative_area_level_1 = Sket_Geo::get_addr_value($addr1['address_components'], 'administrative_area_level_1', 'long_name');
        $addr->country = Sket_Geo::get_addr_value($addr1['address_components'], 'country', 'long_name');
        $addr->post_code = Sket_Geo::get_addr_value($addr1['address_components'], 'postal_code', 'long_name');
        
        
        return $addr;
    }

    private function get_addr_value($address_components, $data_name, $longshort) {
        // find component by scanning component types array for $data_name
        // If not found return '';
        $value = '';
       
       
        foreach ($address_components as $comp) {
            $tempval = $comp['long_name'];
            foreach($comp['types'] as $type) {
                if (strcmp($data_name, $type) == 0) {
                    return $tempval;
                }
            }
        }
        return $value;
    }

}
