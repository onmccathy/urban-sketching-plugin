<?php
/* * *****************************************************************************
 * Sketch metabox fields.
 * 
 * This file contains the markup for displaying the sketch metabox markup.
 * 
 * included form metabox update.
 * 
 * Parameters.
 * 
 * $post                - this current post
 * 
 * Constants:
 * 
 * PARENT_POST_TYPE     - The venue / gallery where the sketch will be held.
 * START_DATE           - The Date the sketch starts.
 * END_DATE             - The date the sketch closes.
 *                      
 * 
 * ***************************************************************************** */

$stored_metadata = get_metadata('post', $post->ID);
$current_user = wp_get_current_user();

// get a table of artists post types to be used in the selection drop down
// we are getting all here prehaps we need to cater for a large numner

$args = array(
    'post_type' => 'sket_artist',
    'post_status' => 'publish',
    'number' => '-1',
    'order' => 'ASC',
    'orderby' => 'title',
);
$results = new WP_Query($args);

// Get the currrent relationships for display
$relationships = SKET_Db::sket_get_sketch_artist_relationships($post->ID);
$geo_data = SKET_Db::sket_get_geo_data_object($post->ID);
?>
<div id="sket_post" value="<?php $post->ID ?>">
    <div class="sket-meta-row">

        <div class="sket-meta-label">
            <label for="sket_date_sketched" class="sket-row-title">Date Sketched</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" class="sket-date sket-datepicker" name="sket_date_sketched" id="sket_date_sketched"
                   value="<?php
                   if (!empty($stored_metadata['sket_date_sketched'])) {
                       $str = esc_attr($stored_metadata['sket_date_sketched'][0]);
                       printf($str);
                   } else {
                       $str = esc_attr(date('d/m/Y'));
                       printf($str);
                   }
                   ?>"</input>
        </div>
    </div>
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket_artist" class="sket-row-title">Artist</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" class="sket-artist-class" name="sket_artist" id="sket_artist"
                   value="<?php
                   if (!empty($stored_metadata['sket_artist'])) {
                       $str = esc_attr($stored_metadata['sket_artist'][0]);
                       printf($str);
                   } else {
                       $str = esc_attr($current_user->user_firstname . ' ' . $current_user->user_lastname);
                       printf($str);
                   }
                   ?>"</input>
        </div>
    </div>
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-artist-select[]" class="sket-row-title">Select Additional Artists</label>
        </div>
        <div class="sket-meta-field">
            <div id="sket-artist-selections" name="sket-artist-selections">
                <?php
                if ($results->have_posts()) {
                    while ($results->have_posts()) {
                        $results->the_post();
                        ?><input type=checkbox id="sket-artist-option" name="sket-artist-option[]" value="<?php esc_html(the_ID()) ?>"
                        <?php
                        $html = sket_is_selected($results->post->ID, $relationships);
                        printf($html);
                        ?>><label for="sket-artist-option"   class="sket-artist-selection-name"><?php esc_html(the_title()); ?></label><br><?php
                           }
                           wp_reset_postdata();
                       } else {
                           ?>
                    <p><?php _e('There are no artists.'); ?></p>
                <?php }; ?>
            </div>
        </div>
    </div>
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <div class="sket-meta-label">
                <label for="sket-address" class="sket-row-title">Address</label>
            </div>
        </div>
        <div class="sket-meta-field">
            <input type="text" readonly="readonly" size=75 id="sket-address-text" class="sket-place-text" name="sket-address"
                   value="<?php printf($geo_data->address)?>"</input>
        </div>
    </div>

    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-lat" class="sket-row-title">Latitude</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" readonly="readonly" id="sket-lat-text" class="sket-lat-text" name="sket-lat" 
                   value="<?php printf($geo_data->lat)?>"</input>
        </div>
    </div> 
    
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-lng" class="sket-row-title">Longitude</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" readonly="readonly" id="sket-lng-text" class="sket-lng-text" name="sket-lng" 
                   value="<?php printf($geo_data->lng)?>"</input>
        </div>
    </div> 
    
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-street-number" class="sket-row-title">Street Number</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" readonly="readonly"  id="sket-street-number-text" class="sket-lng-text" name="sket-street-number" 
                   value="<?php printf($geo_data->street_number)?>"</input>
        </div>
    </div>
    
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-route" class="sket-row-title">Street</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" readonly="readonly" id="sket-route-text" class="sket-lng-text" name="sket-route" 
                   value="<?php printf($geo_data->route)?>"</input>
        </div>
    </div>
    
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-postal-code" class="sket-row-title">Postal Code</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" readonly="readonly" id="sket-postal-code-text" class="sket-lng-text" name="sket-postal-code" 
                   value="<?php printf($geo_data->postal_code)?>"</input>
        </div>
    </div>
    
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-locality" class="sket-row-title">Locality</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" readonly="readonly" id="sket-locality-text" class="sket-lng-text" name="sket-locality" 
                   value="<?php printf($geo_data->locality)?>"</input>
        </div>
    </div>
    
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-sublocality" class="sket-row-title">Sub Locality</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" readonly="readonly" id="sket-sublocality-text" class="sket-lng-text" name="sket-sublocality" 
                   value="<?php printf($geo_data->sub_locality)?>"</input>
        </div>
    </div>
    
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-country" class="sket-row-title">Country</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" readonly="readonly" id="sket-country-text" class="sket-lng-text" name="sket-country" 
                   value="<?php printf($geo_data->country)?>"</input>
        </div>
    </div>
    
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-place-id" class="sket-row-title">Place ID</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" size="30" readonly="readonly" id="sket-place-id-text" class="sket-lng-text" name="sket-place-id" 
                   value="<?php printf($geo_data->place_id);?>"</input>
        </div>
    </div>
    
    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <label for="sket-place" class="sket-row-title">Place</label>
        </div>
        <div class="sket-meta-field">
            <input type="text" size="75" id="sket-place-text" class="sket-lng-text" name="sket-place" 
                   value="<?php printf($geo_data->place);?>"</input>
        </div>
    </div>

    <div class="sket-meta-row">
        <div class="sket-meta-label">
            <div class="sket-meta-label sket-place">
                <label for="sket-location" class="sket-row-title">Sketch Location Search</label>
            </div>
        </div>

    </div>    
    <div class="sket-meta-full-row">
        <div class="sket-meta-map">
            <div class=sket-map id="map" style="clear:both;  height:300px;"></div> 
            <div class=sket-infowindow-content id="infowindow-content">
                <img src="" width="16" height="16" id="place-icon">
                <span id="place-name"  class="title"></span><br>
                <span id="place-address"></span>

            </div>
        </div>
    </div>
</div>
</div>

<?php

function sket_is_selected($artist_List_Id, $relationships) {

    $selected = '';
    foreach ($relationships as $rel) {

        if ($rel->artist_id == $artist_List_Id) {

            $selected = 'checked="checked"';
        }
    }
    return $selected;
}

/*  end of Sketch metabox fields */
/*  do not remove this line - php needs a line or two after opening <?php */