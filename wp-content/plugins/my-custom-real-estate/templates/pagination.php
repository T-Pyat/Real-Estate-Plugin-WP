<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $max_pages; $i++): ?>
            <li class="page-item<?php echo ($i == $paged) ? ' active' : ''; ?>">
                <a href="#" class="page-link estate-page" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>