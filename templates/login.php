<main>
    <?=$nav?>
    <form class="form container <?php if ($errors):?>form--invalid<?php endif; ?>" action="sign-in.php" method="post"> <!-- form--invalid -->
        <h2>Вход</h2>
        <div class="form__item <?php if (isset($errors['email'])):?>form__item--invalid<?php endif; ?>"> <!-- form__item--invalid -->
            <label for="email">E-mail <sup>*</sup></label>
            <input value="<?=getPostVal('email'); ?>" id="email" type="text" name="email" placeholder="Введите e-mail">
            <span class="form__error"><?=$errors['email']?></span>
        </div>
        <div class="form__item <?php if (isset($errors['password'])):?>form__item--invalid<?php endif; ?>  form__item--last">
            <label for="password">Пароль <sup>*</sup></label>
            <input value="<?=getPostVal('password'); ?>" id="password" type="password" name="password" placeholder="Введите пароль">
            <span class="form__error"><?=$errors['password']?></span>
        </div>
        <button type="submit" class="button">Войти</button>
    </form>
</main>