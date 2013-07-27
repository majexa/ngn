<? // @title Данные пользователя ?>
<div id="tabs_" class="contentBody">

<?php
$p = $d['profile'];
$pd = $p['data'];
$profilesPage = DbModelCore::get('pages', $d['page']['settings']['profilesPageId']);
$oDdo = new DdoPageSite($p['page'], 'profile')->setItem($p['data'])->setPagePath($profilesPage['path']);
?>

<div id="bmp_<?= $p['page']['name'] ?>" class="profilePage">
  <div class="profileInfo">
  <? if ($p['page']) { ?>
    <? if (!$pd) { ?>
      <p>Информация не заполнена.</p>
    <? } else { ?>
      <?= $oDdo->els() ?>
    <? } ?>
  <? } ?>
  </div>
  
  <?php /*
  <? if ($d['settings']['showRegDate']) { ?>
    <p><b class="title">Зарегистрирован: </b> <?= date('d.m.Y H:i', $d['user']['dateCreate_tStamp']) ?></p>
  <? } ?>
  <div id="msgsCount">
    <small class="gray">Комментариев:</small>
    <div>
      <?= $msgsCount ?>
    </div>
  </div>
  */?>

  <? if (!empty($d['level'])) { ?>
  <div id="level">
    <small class="gray">Уровень:</small>
    <div><img src="./i/img/portal/level/<?= $d['level'] ?>.png" /></div>
  </div>
  <? } ?>
  
</div>

<? if (isset($d['userItems'])) foreach ($d['userItems'] as $p) {
  if (!$p['items']) continue;
  $title = empty($p['page']['settings']['myProfileTitle']) ? $p['page']['title'].' автора' : $p['page']['settings']['myProfileTitle'];
  ?>

  <h2 id="bmt_<?= $p['page']['name'] ?>" class="tab"><?= $title ?> (<?= count($p['items']) ?>)</h2>
  <div id="bmi_<?= $p['page']['name'] ?>" class="pageName_<?= $p['page']['name'] ?> module_<?= $p['page']['module'] ?> ddil_<?= $p['page']['settings']['ddItemsLayout'] ?>">
    <?php
    $oDdo = new DdoPageSite($p['page'], 'profile')->setItems($p['items']);
    $oDdo->ddddByName['title'] = '`<h3><a href="'.$p['page']['path'].'/`.$id.`">`.$v.`</a></h3>`';
    print $oDdo->els().
      '<div class="clear"><!-- --></div>';
    /*
    print '<div class="items str_'.$p['page']['strName'].'">';
    foreach ($p['items'] as $itemId => $item) {
      print '<div class="item">';
      foreach ($fields as $f) {
        print '<div class="element n_'.$f['name'].' t_'.$f['type'].'">'.
          $oDdo->el($item[$f['name']], $f['name'], $itemId).'</div>';
      }
      print '<div class="clear"><!-- --></div>'.
            '</div>';            
    }
    print '</div>'.
          '<div class="clear"><!-- --></div>';
    */
    
    ?>
    <a href="<?= $p['page']['path'].'/u.'.$d['user']['id'] ?>" class="btn btn2">
      <span><?= Misc::plural($title) ? 'Все' : 'Вся' ?> <?= mb_strtolower($title, CHARSET) ?> автора...</span></a>
    <a href="<?= $p['page']['path'] ?>?a=new" class="btn btn2"><span>Добавить запись</span></a>
    <div class="clear"><!-- --></div>
    <br />
  </div>
<? } ?>

<? if ($d['page']['settings']['wallEnable']) { ?>
  <h2 id="bmt_comments" class="tab"><?= !empty($d['settings']['wallTitle']) ? $d['settings']['wallTitle'] : 'Комментарии' ?> <?= cnt($d['_sub']['items']) ?></h2>
  <div id="bmi_comments">
    <?= Slice::html('commentsUser', 'Блок над комментариями пользователя') ?>
    <?
    $this->tpl(
      $d['oController']->subControllers['comments']->d['tpl'],
      $d['oController']->subControllers['comments']->d
    );
    ?>
  </div>
<? } ?>

<? if ($d['answers']) { ?>
  <h2 id="bmt_answers" class="tab">Ответы <?= cnt($d['answers']) ?></h2>
  <div id="bmi_answers">
    <? $this->tpl('common/msgs', [
      'priv' => ['sub_view' => true],
      'items' => $d['answers']
    ]); ?>
  </div>
<? } ?>

</div>
