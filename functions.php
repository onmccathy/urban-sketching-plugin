<?php



if (!function_exists('sket_get_day')) {

    function sket_get_day($day_of_week) {

        $day = '';
        switch ($day_of_week) {
            case 0:
                $day = 'Monday';
                break;
            case 1:
                $day = 'Tuesday';
                break;
            case 2:
                $day = 'Wednesday';
                break;
            case 3:
                $day = 'Thursday';
                break;
            case 4:
                $day = 'Friday';
                break;
            case 5:
                $day = 'Saturday';
                break;
            case 6:
                $day = 'Sunday';
                break;
        }

        return $day;
    }

}
if (!function_exists('om_get_post_type_template')) {

    /**
     * Get post type template
     * @param type $post_type
     * @param type $original_template
     * @return type
     */
    function sket_get_post_type_template($post_type, $original_template) {
        
        if (is_archive() && is_tax('location')) {
            if (file_exists(get_stylesheet_directory() . '/taxonomy-location.php')) {
                return get_stylesheet_directory() . '/taxonomy-location.php';
            } else {
                return plugin_dir_path(__FILE__) . 'public/templates/taxonomy-location.php';
            }
        }

        if (is_archive() || is_search()) {
            if (file_exists(get_stylesheet_directory() . '/archive-' . $post_type . '.php')) {
                return get_stylesheet_directory() . '/archive-' . $post_type . '.php';
            } else {
                /**
                 * The archive to be displayed can have multiple post of different post types
                 * Each post type may have different format because it will have different 
                 * so we will just call a common archive template that will loop through each post and 
                 * call the template at when we know which on to call.  
                 */
                return plugin_dir_path(__FILE__) . 'public/templates/sket-archive.php';
            }
        } else {
            if (is_single()) {
                if (file_exists(get_stylesheet_directory() . '/single-' . $post_type . '.php')) {
                    return get_stylesheet_directory() . '/single-' . $post_type . '.php';
                } else {
                    return plugin_dir_path(__FILE__) . 'public/templates/single-' . $post_type . '.php';
                }
            }
        }

        return $original_template;
    }

}

if (!function_exists('sket_get_post_type_archive_content_template')) {

    function sket_get_post_type_archive_content_template($post_ID) {
        
        $templateDir = 'public/template/template-parts/';

        $post_type = get_post_type($post_ID);
        if (file_exists(plugin_dir_path(__FILE__) . $templateDir . 'archive-content-' . $post_type . '.php')) {
            return plugin_dir_path(__FILE__) . $templateDir . 'archive-content-' . $post_type . '.php';
        } else {
            return plugin_dir_path(__FILE__) . $templateDir . 'archive-content.php';
        }
        return 'public/template/index.php';
    }

}

if (!function_exists('om_log')) {

    function om_log($log) {


        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }

}

if (!function_exists('dump_post_array')) {

    function dump_post_array($posts) {

        if (is_array($posts)) {
            forEach ($posts as $post) {
                om_log($post);
                $storedmeta = get_metadata('post', $post->ID);
                om_log($storedmeta);
                if (!empty($storedmeta['sket_StartDate'])) {

                    om_log($storedmeta['sket_StartDate'][0]);
                }
                if (!empty($storedmeta['sket_EndDate'])) {
                    om_log($storedmeta['sket_EndDate'][0]);
                }
            }
        } else {
            om_log($posts);
            $storedmeta = get_metadata('post', $posts->ID);
            if (!empty($storedmeta['sket_StartDate'])) {
                $date = new DateTime($storedmeta['sket_StartDate'][0]);
                om_log($date->format('Y-m-d H:i:s'));
            }
            if (!empty($storedmeta['sket_EndDate'])) {
                $date = new DateTime($storedmeta['sket_EndDate'][0]);
                om_log($date->format('Y-m-d H:i:s'));
            }
            om_log('now');
            $nowDate = new DateTime('now');
            $nowDate->setTime(00, 00, 00);
            $now = $nowDate->getTimestamp();
            om_log($now->format('Y-m-d H:i:s'));
            om_log($now->format($now->getTimestamp()));
        }
    }

}
    