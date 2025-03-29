<?php get_header(); ?>

<main class="container py-5">
    <h1 class="mb-4"><?php the_title(); ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?php
            $image = get_field('estate_image');
            if ($image) echo '<img src="' . esc_url($image['url']) . '" class="img-fluid mb-3">';
            ?>

            <ul class="list-group">
                <li class="list-group-item"><strong>Назва:</strong> <?php the_field('estate_name'); ?></li>
                <li class="list-group-item"><strong>Координати:</strong> <?php the_field('estate_coords'); ?></li>
                <li class="list-group-item"><strong>Тип:</strong> <?php the_field('building_type'); ?></li>
                <li class="list-group-item"><strong>Поверхів:</strong> <?php the_field('floors_count'); ?></li>
                <li class="list-group-item"><strong>Екологічність:</strong> <?php the_field('eco_rating'); ?>/5</li>
            </ul>
        </div>

        <div class="col-md-6">
            <h4>Приміщення:</h4>
            <?php if (have_rows('rooms')) : ?>
                <?php while (have_rows('rooms')) : the_row(); ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p><strong>Площа:</strong> <?php the_sub_field('area'); ?></p>
                            <p><strong>Кімнат:</strong> <?php the_sub_field('room_count'); ?></p>
                            <p><strong>Балкон:</strong> <?php the_sub_field('balcony'); ?> | <strong>Санвузол:</strong> <?php the_sub_field('wc'); ?></p>
                            <?php
                            $img = get_sub_field('room_image');
                            if ($img) echo '<img src="' . esc_url($img['url']) . '" class="img-fluid">';
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>Немає приміщень.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
