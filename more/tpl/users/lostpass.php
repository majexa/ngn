<? if ($d['params'][2] == 'complete') { ?>
  <p>Пароль был отправлен на Ваш ящик</p>
<? } elseif ($d['params'][2] == 'failed') { ?>
  <p>Ошибка отправки</p>
<? } else { ?>
  <div class="apeform"><?= $d['form'] ?></div>
<? } ?>