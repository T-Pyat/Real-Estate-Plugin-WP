<?php
get_header();
?>

<main class="container py-5">
    <h1 class="mb-4">Останні новини</h1>

    <?php if (have_posts()) : ?>
        <div class="row">
            <?php while (have_posts()) : the_post(); ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title h5"><?php the_title(); ?></h2>
                            <p class="card-text"><?php the_excerpt(); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php echo do_shortcode('[real_estate_filter]'); ?>
        </div>
    <?php else : ?>
        <p>Поки немає постів.</p>
    <?php endif; ?>
</main>
 
<?php
get_footer();
?>
 