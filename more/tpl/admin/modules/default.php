Вы находитесь в панеле управления сайтом <b><?= SITE_TITLE ?></b>

<? /*<img src="http://majexa.ru/ngn-admin-ping/index.php?site=<?= SITE_DOMAIN ?>" width="1" height="1" />*/ ?>

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
  <? } else { ?>
    <a href="<?= Tt()->getPath(1).'/default/switchTestingMode/1' ?>">Включить</a>
  <? } ?>



  <?php /*
  <h3>Патчи базы данных</h3>
  <ul>
    <li><b>Номер последнего применённого БД-патча:</b><br /><?= O::get('DbPatcher')->getSiteLastPatchN() ?></li>
    <li><b>Номер последнего доступного БД-патча:</b><br /><?= O::get('DbPatcher')->getNgnLastPatchN() ?></li>
  </ul>
  <? if (O::get('DbPatcher')->getActualPatches()) { ?>
    <input type="button" value="Применить актуальные патчи" style="width:200px; height:30px;" 
      onclick="window.location='<?= $this->getPath(1).'/patcher/patch' ?>'" />
  <? } ?>
  
  <input type="button" value="Проверить наличие новой сборки" id="btnCheckNewBuild"
    style="width:300px;height:20px;margin-top:10px;" />
  */
  ?>

    <script type="" src="./i/js/ngn/Ngn.cp.Updater.js"></script>
    <script type="text/javascript">
      var btnCheckNewBuild = $('btnCheckNewBuild');
      if (btnCheckNewBuild) {
        btnCheckNewBuild.addEvent('click', function(e) {
          new Ngn.Updater('<?= $this->getPath(0) ?>', <?= BUILD ?>).check();
        });
      }
    </script>
  <? } ?>
</div>
<?php /*
<div class="col">
  <h2>Новости NGN</h2>
  <iframe src="http://<?= UPDATER_URL ?>/c/panel/ngnNews" style="width:100%; height:200px; border: 0px;"></iframe>
</div>
<div class="clear"><!-- --></div>
*/
?>