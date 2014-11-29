Вы находитесь в панели управления сайтом <b><?= SITE_TITLE ?></b>

<?= Config::getVarVar('adminExtras', 'homeHtml', true) ?>

<div id="table"></div>
<script>


  /*
  new Ngn.Dialog.RequestForm({
    title: 'Тип поля',
    url: '/god/ddField/shop?a=json_selectType',
    width: 610,
    height: 400
  });
  */

</script>

<hr/>

<div class="col">
  <? if (Misc::isGod()) { ?>
    <h3>Размер</h3>
    <?
    $siteFiles = Dir::getSize(WEBROOT_PATH);
    $sizeLogs = Dir::getSize(LOGS_PATH);
    $sizeUpload = Dir::getSize(UPLOAD_PATH);
    $siteMedia = Dir::getSize(WEBROOT_PATH.'/m');
    $siteData = Dir::getSize(DATA_PATH);
    $s2 = Db::getSize(db());
    ?>
    <ul>
      <li>
        <b>Файлы:</b> <?= File::format($siteFiles) ?><br>
        Из них логи: <?= File::format($sizeLogs) ?><br>
        Загруженные файлы: <?= File::format($sizeUpload) ?><br>
        Клиентская часть сайта: <?= File::format($siteMedia) ?><br>
        Файлы с данными: <?= File::format($siteData) ?><br>
      </li>
      <li><b>БД:</b> <?= File::format($s2) ?></li>
      <li><b>Вместе:</b> <?= File::format($siteFiles + $s2) ?></li>
    </ul>
    <?/*
    <h3>Тестирование</h3>
    <? if ($_SESSION['testing']) { ?>
      <a href="<?= Tt()->getPath(1).'/default/switchTestingMode/0' ?>">Выключить</a>
    <? }
    else { ?>
      <a href="<?= Tt()->getPath(1).'/default/switchTestingMode/1' ?>">Включить</a>
    <? } ?>
    */?>
  <? } ?>
</div>
