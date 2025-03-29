<?php
/**
 * Estate Query Modifier Class
 * 
 * Modifies default WP queries for estate post type
 * 
 * @package MyCustomRealEstate
 */

if (!defined('ABSPATH')) exit;

class Estate_Query_Modifier {
    public function __construct() {
        add_action('pre_get_posts', [$this, 'sort_by_eco_rating']);
    }

    /**
     * Sort estates by ecological rating
     * 
     * @param WP_Query $query The WordPress query object
     * @return void
     */
    public function sort_by_eco_rating($query) {
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('estate')) {
            $query->set('meta_key', 'eco_rating');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
        }
    }
}