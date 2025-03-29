<?php
/**
 * Estate Post Type Class
 *
 * Registers the custom post type "estate" and taxonomy "district"
 * 
 * @package MyCustomRealEstate
 */

if (!defined('ABSPATH')) exit;

class Estate_Post_Type {
    public static function register() {
        register_post_type('estate', [
            'labels' => [
                'name' => __('Обʼєкти нерухомості', 'realestate'),
                'singular_name' => __('Обʼєкт', 'realestate'),
                'add_new' => __('Додати новий', 'realestate'),
                'add_new_item' => __('Додати новий обʼєкт', 'realestate'),
                'edit_item' => __('Редагувати обʼєкт', 'realestate'),
                'new_item' => __('Новий обʼєкт', 'realestate'),
                'view_item' => __('Переглянути обʼєкт', 'realestate'),
                'search_items' => __('Пошук обʼєктів', 'realestate'),
                'not_found' => __('Йой, обʼєктів не знайдено', 'realestate'),
                'not_found_in_trash' => __('В кошику ніц не знайдено', 'realestate'),
                'menu_name' => __('Нерухомість', 'realestate'),
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'estates'],
            'supports' => ['title', 'editor', 'thumbnail'],
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-admin-home',
        ]);

        register_taxonomy('district', 'estate', [
            'labels' => [
                'name' => __('Райони', 'realestate'),
                'singular_name' => __('Район', 'realestate'),
                'search_items' => __('Шукати район', 'realestate'),
                'all_items' => __('Всі райони', 'realestate'),
                'edit_item' => __('Редагувати район', 'realestate'),
                'update_item' => __('Оновити район', 'realestate'),
                'add_new_item' => __('Додати новий район', 'realestate'),
                'new_item_name' => __('Назва нового району', 'realestate'),
                'menu_name' => __('Райони', 'realestate'),
            ],
            'hierarchical' => true,
            'rewrite' => ['slug' => 'district'],
            'show_in_rest' => true,
        ]);
    }
}
