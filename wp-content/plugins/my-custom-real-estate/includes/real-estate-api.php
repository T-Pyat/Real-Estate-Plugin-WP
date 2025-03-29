<?php
/**
 * Real Estate API Functions
 * 
 * Provides REST API endpoints for estate objects manipulation
 * 
 * @package MyCustomRealEstate
 */

if (!defined('ABSPATH')) exit;

add_action('rest_api_init', function () {
    register_rest_route('real-estate/v1', '/objects', [
        'methods'  => 'GET',
        'callback' => 're_api_get_estates',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('real-estate/v1', '/objects', [
        'methods'  => 'POST',
        'callback' => 're_api_create_estate',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('real-estate/v1', '/objects/(?P<id>\\d+)', [
        'methods'  => 'PUT',
        'callback' => 're_api_update_estate',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('real-estate/v1', '/objects/(?P<id>\\d+)', [
        'methods'  => 'DELETE',
        'callback' => 're_api_delete_estate',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('real-estate/v1', '/import-xml', [
        'methods'  => 'POST',
        'callback' => 're_api_import_xml',
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Downloads an image from URL and attaches it to a post
 * 
 * @param string $image_url URL of the image to download
 * @param int $post_id Post ID to attach the image to
 * @return int|null Attachment ID if successful, null on failure
 */
function re_sideload_image_from_url($image_url, $post_id) {
    if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
        error_log("[Image] Invalid or empty URL: $image_url");
        return null;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    error_log("[Image] Trying to download image: $image_url");

    $tmp = download_url($image_url);
    if (is_wp_error($tmp)) {
        error_log("[Image] download_url failed: " . $tmp->get_error_message());
        return null;
    }

    $file_array = [
        'name'     => basename($image_url),
        'tmp_name' => $tmp
    ];

    error_log("[Image] File array ready: " . print_r($file_array, true));

    add_filter('upload_mimes', function ($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['png'] = 'image/png';
        $mimes['jpg'] = 'image/jpeg';
        $mimes['jpeg'] = 'image/jpeg';
        return $mimes;
    });

    $media_id = media_handle_sideload($file_array, $post_id);

    if (is_wp_error($media_id)) {
        error_log("[Image] media_handle_sideload failed: " . $media_id->get_error_message());
        return null;
    } else {
        error_log("[Image] Image successfully uploaded. Attachment ID: $media_id");
    }

    return $media_id;
}

/**
 * GET endpoint handler for retrieving estate objects
 * 
 * @param WP_REST_Request $request REST API request object
 * @return WP_REST_Response Response with estate objects
 */
function re_api_get_estates($request) {
    $args = [
        'post_type' => 'estate',
        'posts_per_page' => -1,
        'meta_query' => [],
    ];

    if ($district = $request->get_param('district')) {
        $args['tax_query'] = [[
            'taxonomy' => 'district',
            'field' => 'slug',
            'terms' => $district,
        ]];
    }

    if ($eco_rating = $request->get_param('eco_rating')) {
        $args['meta_query'][] = [
            'key' => 'eco_rating',
            'value' => $eco_rating,
            'compare' => '=',
        ];
    }

    if ($floors = $request->get_param('floors_count')) {
        $args['meta_query'][] = [
            'key' => 'floors_count',
            'value' => $floors,
            'compare' => '=',
        ];
    }

    $query = new WP_Query($args);
    $data = [];

    foreach ($query->posts as $post) {
        $rooms_raw = get_field('rooms', $post->ID) ?: [];
        $rooms = [];
        foreach ($rooms_raw as $room) {
            $rooms[] = [
                'area' => $room['area'] ?? '',
                'room_count' => $room['room_count'] ?? '',
                'balcony' => $room['balcony'] ?? '',
                'wc' => $room['wc'] ?? '',
                'room_image' => $room['room_image']['url'] ?? ''
            ];
        }

        $data[] = [
            'id' => $post->ID,
            'title' => get_the_title($post),
            'estate_name' => get_field('estate_name', $post->ID),
            'coords' => get_field('estate_coords', $post->ID),
            'eco_rating' => get_field('eco_rating', $post->ID),
            'floors_count' => get_field('floors_count', $post->ID),
            'building_type' => get_field('building_type', $post->ID),
            'estate_image' => get_field('estate_image', $post->ID)['url'] ?? null,
            'district' => wp_get_post_terms($post->ID, 'district', ['fields' => 'names']),
            'rooms' => $rooms
        ];
    }

    return rest_ensure_response($data);
}

/**
 * POST endpoint handler for creating a new estate object
 * 
 * @param WP_REST_Request $request REST API request object
 * @return WP_REST_Response|WP_Error Response with created estate ID or error
 */
function re_api_create_estate($request) {
    $params = $request->get_json_params();

    if (empty($params['title'])) {
        return new WP_Error('missing_title', 'Поле "title" є обовʼязковим', ['status' => 400]);
    }

    $post_id = wp_insert_post([
        'post_type' => 'estate',
        'post_status' => 'publish',
        'post_title' => sanitize_text_field($params['title']),
    ]);

    if (is_wp_error($post_id)) {
        return new WP_Error('insert_failed', 'Не вдалося створити обʼєкт', ['status' => 500]);
    }

    $district_name = sanitize_text_field($params['district'] ?? '');
    if ($district_name) {
        wp_set_object_terms($post_id, [$district_name], 'district', false);
    }

    update_field('estate_name', sanitize_text_field($params['estate_name'] ?? ''), $post_id);
    update_field('estate_coords', sanitize_text_field($params['coords'] ?? ''), $post_id);
    update_field('eco_rating', (int) ($params['eco_rating'] ?? ''), $post_id);
    update_field('floors_count', (int) ($params['floors_count'] ?? ''), $post_id);
    update_field('building_type', sanitize_text_field($params['building_type'] ?? ''), $post_id);

    if (!empty($params['estate_image']) && filter_var($params['estate_image'], FILTER_VALIDATE_URL)) {
        $image_id = re_sideload_image_from_url($params['estate_image'], $post_id);
        if ($image_id) {
            update_field('estate_image', $image_id, $post_id);
        }
    }

    if (!empty($params['rooms']) && is_array($params['rooms'])) {
        $rooms = [];
        foreach ($params['rooms'] as $room) {
            $room_image_id = null;
            if (!empty($room['room_image']) && filter_var($room['room_image'], FILTER_VALIDATE_URL)) {
                $room_image_id = re_sideload_image_from_url($room['room_image'], $post_id);
            }

            $rooms[] = [
                'area' => sanitize_text_field($room['area'] ?? ''),
                'room_count' => sanitize_text_field($room['room_count'] ?? ''),
                'balcony' => sanitize_text_field($room['balcony'] ?? ''),
                'wc' => sanitize_text_field($room['wc'] ?? ''),
                'room_image' => $room_image_id,
            ];
        }
        update_field('rooms', $rooms, $post_id);
    }

    return rest_ensure_response(['success' => true, 'id' => $post_id]);
}

/**
 * PUT endpoint handler for updating an existing estate object
 * 
 * @param WP_REST_Request $request REST API request object
 * @return WP_REST_Response|WP_Error Response with updated estate ID or error
 */
function re_api_update_estate($request) {
    $id = $request['id'];
    $params = $request->get_json_params();

    $post = get_post($id);
    if (!$post || $post->post_type !== 'estate') return new WP_Error('not_found', 'Обʼєкт не знайдено', ['status' => 404]);

    wp_update_post([
        'ID' => $id,
        'post_title' => sanitize_text_field($params['title'] ?? $post->post_title),
    ]);

    $district_name = sanitize_text_field($params['district'] ?? '');
    if ($district_name) {
        wp_set_object_terms($id, [$district_name], 'district', false);
    }

    update_field('estate_name', sanitize_text_field($params['estate_name'] ?? ''), $id);
    update_field('estate_coords', sanitize_text_field($params['coords'] ?? ''), $id);
    update_field('eco_rating', (int) ($params['eco_rating'] ?? ''), $id);
    update_field('floors_count', (int) ($params['floors_count'] ?? ''), $id);
    update_field('building_type', sanitize_text_field($params['building_type'] ?? ''), $id);

    if (!empty($params['estate_image']) && filter_var($params['estate_image'], FILTER_VALIDATE_URL)) {
        $image_id = re_sideload_image_from_url($params['estate_image'], $id);
        if ($image_id) {
            update_field('estate_image', $image_id, $id);
        }
    }

    if (!empty($params['rooms']) && is_array($params['rooms'])) {
        $rooms = [];
        foreach ($params['rooms'] as $room) {
            $room_image_id = null;
            if (!empty($room['room_image']) && filter_var($room['room_image'], FILTER_VALIDATE_URL)) {
                $room_image_id = re_sideload_image_from_url($room['room_image'], $id);
            }

            $rooms[] = [
                'area' => sanitize_text_field($room['area'] ?? ''),
                'room_count' => sanitize_text_field($room['room_count'] ?? ''),
                'balcony' => sanitize_text_field($room['balcony'] ?? ''),
                'wc' => sanitize_text_field($room['wc'] ?? ''),
                'room_image' => $room_image_id,
            ];
        }
        update_field('rooms', $rooms, $id);
    }

    return rest_ensure_response(['success' => true, 'updated_id' => $id]);
}

/**
 * DELETE endpoint handler for removing an estate object
 * 
 * @param WP_REST_Request $request REST API request object
 * @return WP_REST_Response|WP_Error Response with deleted estate ID or error
 */
function re_api_delete_estate($request) {
    $id = $request['id'];
    $post = get_post($id);
    if (!$post || $post->post_type !== 'estate') return new WP_Error('not_found', 'Обʼєкт не знайдено', ['status' => 404]);

    wp_delete_post($id, true);
    return rest_ensure_response(['success' => true, 'deleted_id' => $id]);
}

/**
 * POST endpoint handler for importing estates from XML
 * 
 * @param WP_REST_Request $request REST API request object
 * @return WP_REST_Response|WP_Error Response with number of imported estates or error
 */
function re_api_import_xml($request) {
    $params = $request->get_json_params();
    $url = $params['url'] ?? '';

    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        return new WP_Error('invalid_url', 'Недійсний або відсутній URL XML', ['status' => 400]);
    }

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        error_log('XML Fetch Error: ' . $response->get_error_message());
        return new WP_Error('fetch_failed', 'Не вдалося отримати XML', ['status' => 500]);
    }

    $body = wp_remote_retrieve_body($response);
    if (empty($body)) {
        error_log('XML Parse Error: Empty body');
        return new WP_Error('empty_body', 'Порожній вміст XML', ['status' => 500]);
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($body);

    if ($xml === false) {
        foreach (libxml_get_errors() as $error) {
            error_log('XML Error: ' . $error->message);
        }
        return new WP_Error('invalid_xml', 'Невалідний XML формат', ['status' => 500]);
    }

    $imported = 0;
    foreach ($xml->estate as $item) {
        $title = (string) ($item->title ?? '');
        if (empty($title)) continue;

        $post_id = wp_insert_post([
            'post_type' => 'estate',
            'post_status' => 'publish',
            'post_title' => sanitize_text_field($title),
        ]);

        if (is_wp_error($post_id)) {
            error_log('Insert failed: ' . $post_id->get_error_message());
            continue;
        }

        update_field('estate_name', sanitize_text_field($item->name ?? ''), $post_id);
        update_field('estate_coords', sanitize_text_field($item->coords ?? ''), $post_id);
        update_field('eco_rating', (int) ($item->eco_rating ?? 0), $post_id);
        update_field('floors_count', (int) ($item->floors_count ?? 0), $post_id);
        update_field('building_type', sanitize_text_field($item->building_type ?? ''), $post_id);

        if (!empty($item->district)) {
            wp_set_object_terms($post_id, [(string) $item->district], 'district', false);
        }

        if (!empty($item->estate_image)) {
            $img_url = (string) $item->estate_image;
            $img_id = re_sideload_image_from_url($img_url, $post_id);
            if ($img_id) update_field('estate_image', $img_id, $post_id);
        }

        $rooms = [];
        if (!empty($item->rooms->room)) {
            foreach ($item->rooms->room as $room) {
                $room_img_id = null;
                if (!empty($room->room_image)) {
                    $room_img_id = re_sideload_image_from_url((string) $room->room_image, $post_id);
                }

                $rooms[] = [
                    'area' => (string) ($room->area ?? ''),
                    'room_count' => (string) ($room->room_count ?? ''),
                    'balcony' => (string) ($room->balcony ?? ''),
                    'wc' => (string) ($room->wc ?? ''),
                    'room_image' => $room_img_id,
                ];
            }
        }

        if (!empty($rooms)) {
            update_field('rooms', $rooms, $post_id);
        }

        $imported++;
    }

    return rest_ensure_response(['success' => true, 'imported' => $imported]);
}