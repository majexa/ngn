Вы находитесь в панеле управления сайтом <b><?= SITE_TITLE ?></b>

<?= Config::getVarVar('adminExtras', 'homeHtml', true) ?>

<hr/>

<div class="col">
  <? if (Misc::isGod()) { ?>
    <h3>Размер</h3>
    <?
    $s1 = Dir::getSize(WEBROOT_PATH);
    $s11 = Dir::getSize(LOGS_PATH);
    $s12 = Dir::getSize(UPLOAD_PATH);
    $s122 = Dir::getSize(WEBROOT_PATH.'/m');
    $s13 = Dir::getSize(DATA_PATH);
    $s2 = Db::getSize(db());
    ?>
    <ul>
      <li>
        <b>Файлы:</b> <?= File::format($s1) ?><br>
        Из них логи: <?= File::format($s11) ?><br>
        Динамическая статика: <?= File::format($s12) ?><br>
        Статическая статика: <?= File::format($s122) ?><br>
        Файлы с данными: <?= File::format($s13) ?><br>
      </li>
      <li><b>БД:</b> <?= File::format($s2) ?></li>
      <li><b>Вместе:</b> <?= File::format($s1 + $s2) ?></li>
    </ul>
    <h3>Тестирование</h3>
    <? if ($_SESSION['testing']) { ?>
      <a href="<?= Tt()->getPath(1).'/default/switchTestingMode/0' ?>">Выключить</a>
    <? }
    else { ?>
      <a href="<?= Tt()->getPath(1).'/default/switchTestingMode/1' ?>">Включить</a>
    <? } ?>
  <? } ?>
</div>
?>