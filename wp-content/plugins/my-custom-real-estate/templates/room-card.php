<?php
$img = $item['image'];
if (is_array($img) && isset($img['url'])) {
    $image = $img['url'];
} elseif (is_numeric($img)) {
    $image = wp_get_attachment_image_url($img, 'medium');
}
?>
<div class="card mb-3">
    <div class="row g-0">
        <div class="col-md-4">
            <img src="<?php echo esc_url($image); ?>" class="img-fluid rounded-start" alt="">
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <h5 class="card-title"><?php echo esc_html($item['building_title']); ?> - <?php _e('Приміщення', 'realestate'); ?></h5>
                <p class="card-text">
                    <?php if(!empty($item['area'])): ?>
                        <?php _e('Площа', 'realestate'); ?>: <?php echo esc_html($item['area']); ?><br>
                    <?php endif; ?>
                    <?php if(!empty($item['room_count'])): ?>
                        <?php _e('Кімнат', 'realestate'); ?>: <?php echo esc_html($item['room_count']); ?><br>
                    <?php endif; ?>
                    <?php if(!empty($item['balcony']) || !empty($item['wc'])): ?>
                        <?php _e('Балкон', 'realestate'); ?>: <?php echo esc_html($item['balcony']); ?> | 
                        <?php _e('Санвузол', 'realestate'); ?>: <?php echo esc_html($item['wc']); ?>
                    <?php endif; ?>
                </p>
                <a href="<?php echo esc_url($item['link']); ?>" class="btn btn-sm btn-outline-primary"><?php _e('Детальніше', 'realestate'); ?></a>
            </div>
        </div>
    </div>
</div>