<?php
/**
 * Real Estate Filter Widget
 * 
 * Provides a widget for filtering real estate properties
 * 
 * @package MyCustomRealEstate
 */

if (!defined('ABSPATH')) exit;

class Real_Estate_Filter_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'real_estate_filter_widget',
            __('Фільтр нерухомості', 'realestate'),
            ['description' => __('Фільтр обʼєктів нерухомості по всіх полях.', 'realestate')]
        );
    }

    /**
     * Front-end display of widget
     * 
     * @param array $args     Widget arguments
     * @param array $instance Saved values from database
     * @return void
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : __('Фільтр нерухомості', 'realestate');

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        echo do_shortcode('[real_estate_filter id="widget"]');
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     * 
     * @param array $instance Previously saved values from database
     * @return void
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Фільтр нерухомості', 'realestate');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Заголовок:', 'realestate'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     * 
     * @param array $new_instance Values just sent to be saved
     * @param array $old_instance Previously saved values from database
     * @return array Updated safe values to be saved
     */
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

function register_real_estate_filter_widget() {
    register_widget('Real_Estate_Filter_Widget');
}
add_action('widgets_init', 'register_real_estate_filter_widget');