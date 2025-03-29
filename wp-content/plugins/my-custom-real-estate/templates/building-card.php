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
                <h5 class="card-title"><?php echo esc_html($item['title']); ?></h5>
                <p class="card-text"><?php echo esc_html($item['coords']); ?></p>
                <a href="<?php echo esc_url($item['link']); ?>" class="btn btn-sm btn-outline-primary"><?php _e('Детальніше', 'realestate'); ?></a>
            </div>
        </div>
    </div>
</div>