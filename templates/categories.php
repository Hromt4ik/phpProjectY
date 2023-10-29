<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $item): ?>
            <li class="nav__item">
                <a href="all-lots.php?id=<?=$item['Id']?>">
                    <?= htmlspecialchars($item['NameCategory']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>