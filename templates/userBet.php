<main>
  <?= $nav ?>
  <section class="rates container">
    <h2>Мои ставки</h2>


    <table class="rates__list">
      <?php foreach ($bet_lots as $item): ?>

        <?php
        $class = "";
        $class_row = "";
        $status_lot = "";

        if (strtotime($item['lot_date']) < time()) {
          if ($item['is_win']) {
            $status_lot = "Ставка выиграла";
            $class_row = "rates__item--win";
            $class = "timer--win";
          } else {
            $class_row = "rates__item--end";
            $status_lot = "Торги окончены";
            $class = "timer--end";
          }
        }
        ?>
        <tr class="rates__item <?= $class_row ?>">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?= $item['pic_lot'] ?>" width="54" height="40" alt="">
            </div>
            <h3 class="rates__title"><a href="lot.php?Id=<?= $item['LotId'] ?>">
                <?= htmlspecialchars($item['lot_name']) ?>
              </a></h3>
          </td>
          <td class="rates__category">
            <?= htmlspecialchars($item['category_name']) ?>
          </td>
          <td class="rates__timer">

            <div class="timer <?= $class ?>">
              <?php $date_end = ($status_lot==="") ?  get_dt_range($item['lot_date']): $status_lot?>
              <?php if($status_lot ===""):?>
              <div class=" <?php if ($date_end[0] < '24'): ?> timer--finishing <?php endif; ?>">
                <?= "$date_end[0]:$date_end[1]" ?>
              </div>
              <?php else:?>
              <?= htmlspecialchars($status_lot) ?>
              <?php endif;?>
            </div>
          </td>
          <td class="rates__price">
            <?= htmlspecialchars(format_price($item['Sum'])) ?>
          </td>
          <td class="rates__time">
            <?= formate_date($item['DateCreate']) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </section>
</main>