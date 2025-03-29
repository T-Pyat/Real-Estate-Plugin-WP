<?php
/**
 * Real Estate Filter Functionality
 * 
 * Provides shortcode and AJAX functionality for filtering real estate objects
 */

if (!defined('ABSPATH')) exit;

/**
 * Register shortcode for real estate filter
 */
add_shortcode('real_estate_filter', 're_render_filter_shortcode');

/**
 * Render the real estate filter form
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML output of the filter form
 */
function re_render_filter_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 'default',
    ], $atts);

    $form_id = 'estate-filter-form-' . esc_attr($atts['id']);
    $results_id = 'estate-results-' . esc_attr($atts['id']);

    ob_start();
    include RE_PLUGIN_PATH . 'templates/filter-form.php';
    return ob_get_clean();
}

/**
 * Handle AJAX request for filtering estates
 */
add_action('wp_ajax_re_ajax_filter_estates', 're_ajax_filter_estates');
add_action('wp_ajax_nopriv_re_ajax_filter_estates', 're_ajax_filter_estates');

function re_ajax_filter_estates() {
    parse_str($_POST['filters'], $filters);
    $paged = isset($_POST['paged']) ? (int) $_POST['paged'] : 1;
    $posts_per_page = 5;

    $results = re_get_filtered_estates($filters);
    
    $total_results = count($results);
    $max_pages = ceil($total_results / $posts_per_page);
    $offset = ($paged - 1) * $posts_per_page;
    $paged_results = array_slice($results, $offset, $posts_per_page);
    
    re_display_filtered_results($paged_results, $paged, $max_pages);
    
    wp_die();
}

/**
 * Get filtered estates based on criteria
 * 
 * @param array $filters Array of filter criteria
 * @return array Filtered estates and rooms
 */
function re_get_filtered_estates($filters) {
    $args = [
        'post_type' => 'estate',
        'posts_per_page' => -1,
        'fields' => 'ids',
    ];

    if (!empty($filters['district'])) {
        $args['tax_query'][] = [
            'taxonomy' => 'district',
            'field' => 'slug',
            'terms' => $filters['district'],
        ];
    }

    $meta_query = [];
    foreach (['eco_rating', 'floors_count'] as $field) {
        if (!empty($filters[$field])) {
            $meta_query[] = [
                'key' => $field,
                'value' => $filters[$field],
                'compare' => '=',
            ];
        }
    }
    
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    $all_query = new WP_Query($args);
    $estate_ids = $all_query->posts;
    
    $results = [];
    $room_filters_present = !empty($filters['room_count']) || !empty($filters['balcony']) || !empty($filters['wc']);

    if (!empty($estate_ids)) {
        foreach ($estate_ids as $estate_id) {
            if (!$room_filters_present) {
                $results[] = re_get_building_data($estate_id);
            }
            
            $matched_rooms = re_get_matched_rooms($estate_id, $filters);
            
            if ($room_filters_present) {
                $results = array_merge($results, $matched_rooms);
            }
        }
    }

    return $results;
}

/**
 * Get building data for display
 * 
 * @param int $estate_id Post ID of the estate
 * @return array Building data
 */
function re_get_building_data($estate_id) {
    return [
        'type' => 'building',
        'id' => $estate_id,
        'title' => get_the_title($estate_id),
        'image' => get_field('estate_image', $estate_id),
        'coords' => get_field('estate_coords', $estate_id),
        'link' => get_permalink($estate_id)
    ];
}

/**
 * Get rooms that match the filter criteria
 * 
 * @param int $estate_id Post ID of the estate
 * @param array $filters Filter criteria
 * @return array Matched rooms
 */
function re_get_matched_rooms($estate_id, $filters) {
    $rooms = get_field('rooms', $estate_id);
    $matched_rooms = [];
    
    if (is_array($rooms) && !empty($rooms)) {
        foreach ($rooms as $index => $room) {
            if (re_room_matches_filters($room, $filters)) {
                $matched_rooms[] = [
                    'type' => 'room',
                    'building_id' => $estate_id,
                    'building_title' => get_the_title($estate_id),
                    'room_index' => $index,
                    'area' => $room['area'] ?? '',
                    'room_count' => $room['room_count'] ?? '',
                    'balcony' => $room['balcony'] ?? '',
                    'wc' => $room['wc'] ?? '',
                    'image' => $room['room_image'] ?? null,
                    'link' => get_permalink($estate_id) . '#room-' . $index
                ];
            }
        }
    }
    
    return $matched_rooms;
}

/**
 * Check if a room matches the filter criteria
 * 
 * @param array $room Room data
 * @param array $filters Filter criteria
 * @return bool Whether the room matches
 */
function re_room_matches_filters($room, $filters) {
    if (!empty($filters['room_count']) && $room['room_count'] != $filters['room_count']) {
        return false;
    }

    if (!empty($filters['balcony']) && $room['balcony'] != $filters['balcony']) {
        return false;
    }

    if (!empty($filters['wc']) && $room['wc'] != $filters['wc']) {
        return false;
    }

    return true;
}

/**
 * Display filtered results
 * 
 * @param array $results Filtered results
 * @param int $paged Current page
 * @param int $max_pages Maximum pages
 */
function re_display_filtered_results($results, $paged, $max_pages) {
    ob_start();

    if (!empty($results)) {
        foreach ($results as $item) {
            if ($item['type'] === 'building') {
                include RE_PLUGIN_PATH . 'templates/building-card.php';
            } else {
                include RE_PLUGIN_PATH . 'templates/room-card.php';
            }
        }
        
        if ($max_pages > 1) {
            include RE_PLUGIN_PATH . 'templates/pagination.php';
        }
    } else {
        echo '<p>' . __('Нічого не знайдено.', 'realestate') . '</p>';
    }

    echo ob_get_clean();
}