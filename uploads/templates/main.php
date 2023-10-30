<main class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и
            горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <?php foreach ($categories as $item): ?>
                <li class="promo__item promo__item--<?= ($item['SymbolCode']) ?>">
                    <a class="promo__link" href="all-lots.php?id=<?= $item['Id'] ?>">
                        <?= htmlspecialchars($item['NameCategory']) ?>
                    </a>
                </li>
            <?php endforeach; ?>

        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
            <?php foreach ($lots as $item): ?>
                <li class="lots__item lot">

                    <div class="lot__image">
                        <img src="<?= htmlspecialchars($item['Image']) ?>" width="350" height="260" alt="">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category">
                            <?= htmlspecialchars($item['NameCategory']) ?>
                        </span>
                        <h3 class="lot__title"><a class="text-link" href="lot.php?Id=<?= $item['Id'] ?>">
                                <?= htmlspecialchars($item['NameLot']) ?>
                            </a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span class="lot__cost">
                                    <?= format_price($item['StartPrise']) ?>
                                </span>
                            </div>
                            <?php $time_end = get_dt_range($item['DateEnd']) ?>
                            <div class="lot__timer <?php if ($time_end[0] < "24"): ?>timer--finishing<?php endif ?> timer ">
                                <?= "$time_end[0]:$time_end[1]" ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>