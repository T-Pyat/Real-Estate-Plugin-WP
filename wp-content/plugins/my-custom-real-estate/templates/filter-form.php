<div id="estate-filter" class="container my-4">
    <?php if ($atts['id'] !== 'widget') : ?>
        <h3><?php esc_html_e('Фільтр обʼєктів нерухомості', 'realestate'); ?></h3>
    <?php endif; ?>
    <form id="<?php echo $form_id; ?>" class="estate-filter-form" data-results="<?php echo $results_id; ?>">
        <div class="row">
            <div class="col-md-3 mb-3">
                <label><?php _e('Район', 'realestate'); ?></label>
                <select name="district" class="form-select">
                    <option value=""><?php _e('Усі', 'realestate'); ?></option>
                    <?php
                    $districts = get_terms(['taxonomy' => 'district', 'hide_empty' => false]);
                    foreach ($districts as $d) {
                        printf('<option value="%s">%s</option>', esc_attr($d->slug), esc_html($d->name));
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label><?php _e('Кількість поверхів', 'realestate'); ?></label>
                <select name="floors_count" class="form-select">
                    <option value=""><?php _e('Усі', 'realestate'); ?></option>
                    <?php foreach (range(1, 20) as $i) echo "<option value='$i'>$i</option>"; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label><?php _e('Екологічність', 'realestate'); ?></label>
                <select name="eco_rating" class="form-select">
                    <option value=""><?php _e('Усі', 'realestate'); ?></option>
                    <?php foreach (range(1, 5) as $i) echo "<option value='$i'>$i</option>"; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label><?php _e('Кіл. кімнат', 'realestate'); ?></label>
                <select name="room_count" class="form-select">
                    <option value=""><?php _e('Усі', 'realestate'); ?></option>
                    <?php foreach (range(1, 10) as $i) echo "<option value='$i'>$i</option>"; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label><?php _e('Балкон', 'realestate'); ?></label>
                <select name="balcony" class="form-select">
                    <option value=""><?php _e('Усі', 'realestate'); ?></option>
                    <option value="так"><?php _e('Так', 'realestate'); ?></option>
                    <option value="ні"><?php _e('Ні', 'realestate'); ?></option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label><?php _e('Санвузол', 'realestate'); ?></label>
                <select name="wc" class="form-select">
                    <option value=""><?php _e('Усі', 'realestate'); ?></option>
                    <option value="так"><?php _e('Так', 'realestate'); ?></option>
                    <option value="ні"><?php _e('Ні', 'realestate'); ?></option>
                </select>
            </div>
            <div class="col-md-3 mb-3 d-grid">
                <button type="submit" class="btn btn-primary"><?php _e('Пошук', 'realestate'); ?></button>
            </div>
        </div> 
    </form>
    <div id="<?php echo $results_id; ?>" class="mt-4 estate-results"></div>
</div>