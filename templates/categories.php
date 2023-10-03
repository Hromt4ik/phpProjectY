<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $item): ?>
            <li class="nav__item">
                <a href="pages/all-lots.html">
                    <?= htmlspecialchars($item['NameCategory']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>