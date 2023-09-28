
        <nav class="nav">
            <ul class="nav__list container">
                <?php foreach ($categories as $item): ?>
                    <li class="nav__item">
                        <a href="pages/all-lots.html"><?= htmlspecialchars($item['NameCategory']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <form class="form form--add-lot container <?php if ($errors): ?>form--invalid<?php endif; ?> " enctype="multipart/form-data" action="add.php" method="post"> <!-- form--invalid -->
            <h2>Добавление лота</h2>
            <div class="form__container-two">
                <div class="form__item <?php if ($errors['lot-name']): ?> form__item--invalid <?php endif; ?>" > <!-- form__item--invalid -->
                    <label for="lot-name">Наименование <sup>*</sup></label>
                    <input id="lot-name" type="text" value="<?=getPostVal('lot-name'); ?>"  name="lot-name" placeholder="Введите наименование лота">
                    <span class="form__error"><?= $errors['lot-name'] ?></span>
                </div>
                <div class="form__item">
                    <label for="category">Категория <sup>*</sup></label>
                    <select id="category"  name="category">
                        <?php foreach ($categories as $item): ?>
                            <option><?= htmlspecialchars($item['NameCategory']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form__error">Выберите категорию</span>
                </div>
            </div>
            <div class="form__item <?php if ($errors['message']): ?> form__item--invalid <?php endif; ?> form__item--wide">
                <label for="message">Описание <sup>*</sup></label>
                <textarea id="message" name="message" placeholder="Напишите описание лота"><?=getPostVal('message'); ?></textarea>
                <span class="form__error"><?= $errors['message'] ?></span>
            </div>
            <div class="form__item form__item--file">
                <label>Изображение <sup>*</sup></label>
                <div class="form__input-file">
                    <input class="visually-hidden"  name="image" type="file" id="lot-img" value="">
                    <label for="lot-img">
                        Добавить
                    </label>
                </div>
            </div>
            <div class="form__container-three">
                <div class="form__item <?php if ($errors['lot-rate']): ?> form__item--invalid <?php endif; ?> form__item--small">
                    <label for="lot-rate">Начальная цена <sup>*</sup></label>
                    <input id="lot-rate" value="<?=getPostVal('lot-rate'); ?>" type="text" name="lot-rate" placeholder="0">
                    <span class="form__error"><?= $errors['lot-rate'] ?></span>
                </div>
                <div class="form__item <?php if ($errors['lot-step']): ?> form__item--invalid <?php endif; ?> form__item--small">
                    <label for="lot-step">Шаг ставки <sup>*</sup></label>
                    <input id="lot-step" value="<?=getPostVal('lot-step'); ?>" type="text" name="lot-step" placeholder="0">
                    <span class="form__error"><?= $errors['lot-step'] ?></span>
                </div>
                <div class="form__item <?php if ($errors['lot-date']): ?> form__item--invalid <?php endif; ?>">
                    <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
                    <input class="form__input-date" value="<?=getPostVal('lot-date'); ?>" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
                    <span class="form__error"><?= $errors['lot-date'] ?></span>
                </div>
            </div>
            <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
            <button type="submit"  class="button">Добавить лот</button>
        </form>