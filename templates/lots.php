<main>
    <?= $nav ?>
    <div class="container">
        <?php if ($lots): ?>
            <section class="lots">
                <h2>Все лоты в категории <span>«
                        <?= htmlspecialchars($lots[0]["cat_name"]) ?>»
                    </span></h2>

                <ul class="lots__list">

                    <?php foreach ($lots as $item): ?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src="<?= htmlspecialchars($item['Image']) ?>" width="350" height="260" alt="">
                            </div>
                            <div class="lot__info">
                                <span class="lot__category">
                                    <?= htmlspecialchars($item['cat_name']) ?>
                                </span>
                                <h3 class="lot__title"><a class="text-link" href="lot.php?Id=<?= $item['Id'] ?>">
                                        <?= htmlspecialchars($item['name_lot']) ?>
                                    </a></h3>
                                <div class="lot__state">

                                    <div class="lot__rate">
                                        <span class="lot__amount">Стартовая цена</span>
                                        <span class="lot__cost">
                                            <?= htmlspecialchars(format_price($item['StartPrise'])) ?>
                                        </span>
                                    </div>

                                    <?php $date_end = get_dt_range($item['DateEnd']) ?>

                                    <div
                                        class="lot__timer timer <?php if ((int) $date_end[0] <= 24): ?> timer--finishing <?php endif; ?>">
                                        <?= "$date_end[0]:$date_end[1]" ?>
                                    </div>

                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <ul class="pagination-list">
                    <li class="pagination-item pagination-item-prev"><a
                            href="all-lots.php?id=<?= $lots[0]["CategoryId"] ?>&page=<?= $curr_page <= 1 ? 1 : $curr_page - 1 ?>">Назад</a>
                    </li>
                    <?php for ($i = 1; $i <= $count_page; $i++): ?>
                        <li class="pagination-item <?= intval($curr_page) === $i ? "pagination-item-active" : "" ?>"><a
                                href="all-lots.php?id=<?= $lots[0]["CategoryId"] ?>&page=<?= $i ?>">
                                <?= $i ?>
                            </a></li>
                    <?php endfor; ?>
                    <li class="pagination-item pagination-item-next"><a
                            href="all-lots.php?id=<?= $lots[0]["CategoryId"] ?>&page=<?= $curr_page >= $count_page ? $curr_page : $curr_page + 1 ?>">Вперед</a>
                    </li>

                </ul>
        </div>
    <?php else: ?>
        <h2>По вашему запросу ничего не найдено</h2>
    <?php endif; ?>
</main>