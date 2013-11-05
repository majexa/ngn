<? if ($d['page']['controller'] == 'item') { ?>
  <p class="info">Сохранено успешно.</p>
  <ul>
    <li><a href="<?= $this->getPath() ?>">« Вернуться</a></li>
  </ul>
<? } elseif ($d['page']['controller'] == 'profile') { ?>
  <p>Изменение информации вашего профиля прошло успешно.</p>
  <p>Вы можете сделать следующее</p>
  <ul>
    <li><a href="<?= $this->getPath() ?>?a=edit">Продолжить редактирование</a></li>
    <li><a href="<?= $this->getControllerPath('userData').'/'.Auth::get('id').'/'.$d['page']['strName'] ?>">Посмотреть на него</a></li>
  </ul>
<? } else { ?>
  <? if ($_GET['completeAction'] == 'deleteFile') { ?>
    <p>Файл записи <b><?= $d['item']['title'] ?></b> был успешно удален
  <? } else { ?>
    <p><?= ucfirst($d['page']['settings']['itemTitle']) ?> <b><?= $d['item']['title'] ?></b>
    <?
    
    if ($_GET['completeAction'] == 'edit')
      print NgnMorph::gender($d['page']['settings']['itemTitle'], [
        'изменён', 'изменена', 'изменено'
      ]);
    elseif ($_GET['completeAction'] == 'new')
      print NgnMorph::gender($d['page']['settings']['itemTitle'], [
        'создан', 'создана', 'создано'
      ]);
    elseif ($_GET['completeAction'] == 'publish')
      print NgnMorph::gender($d['page']['settings']['itemTitle'], [
        'опубликован', 'опубликована', 'опубликовано'
      ]);
    elseif ($_GET['completeAction'] == 'delete')
      print NgnMorph::gender($d['page']['settings']['itemTitle'], [
        'удалён', 'удалена', 'удалено'
      ]);
    ?> 
      успешно.</p>
    <? if ($d['settings']['premoder'] and $this->getControllerPath('userData', true)) { ?>
      <p>Она появится на сайте после проверки модератором.</p>
      <? if ($d['moders']) { ?>
        <p class="dgray">Модераторы раздела:
          <?= $this->enumDddd($d['moders'], '<a href="`.$this->getUserPath($id).`">$login</a>') ?></p>
      <? } ?>
    <? } ?>
  <? } ?>
  <ul>
    <li><a href="<?= $this->getPath() ?>">« Вернуться</a></li>
    <? if ($_REQUEST['completeAction'] != 'delete') {
    $t = NgnMorph::cast($d['page']['settings']['itemTitle'], ['ЕД', 'ВН']);
    ?>
      <li><a href="<?= $this->getPath(1).'/'.$d['item']['id'] ?>">Посмотреть <?= $t ?></a></li>
      <li><a href="<?= $this->getPath(1).'/'.$d['item']['id'].'?a=edit' ?>">Редактировать <?= $t ?></a></li>
    <? } ?>
  </ul>
  <? if ($d['settings']['premoder'] and $_REQUEST['completeAction'] == 'new') { ?>
  <div class="info">
    <p>Этот раздел является премодерируемым.
    Это значит, что ваша запись будет добавлена на сайт после проверки.</p>
  </div>
  <? } ?>
<? } ?>