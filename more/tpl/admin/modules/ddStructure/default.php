<? $this->tpl('admin/modules/ddStructure/header') ?>

<?

$d['items'] = array_values($d['items']);
$d['grid'] = [
  'head' => ['Название', 'Имя', 'Тип', '', 'Описание'],
  'body' => array_map(function($v) use ($d) {
    $r = array_merge(Arr::filterByKeys($v, ['id', 'tagGroup']), [
      'rowClass' => 't_'.$v['type'].($v['defaultDisallow'] ? ' disallow' : ''),
      'data'  => [
        $v['title'].($v['required'] ? '<span style="color:#FF0000">*</span>' : ''),
        $v['name'],
        '<img src="'.DdFieldCore::getIconPath($v['type']).'" title="'.$v['type'].'">',
        '<small>'.
        ($v['notList'] ? '<nobr>{не выводится}</nobr>' : '').
        ($v['system'] ? '<nobr>{системное}</nobr>' : '').
        ($v['defaultDisallow'] ? '<nobr>{не доступно}</nobr>' : '').
        (!$v['editable'] ? '<nobr>{не редактируется}</nobr>' : '').
        ($v['filterable'] ? '<nobr>{фильтруемое}</nobr>' : '').
        '&nbsp;</small>',
        $v['descr']
      ]
    ]);
    if ($v['editable']) {
      $r['tools'] = [
        'delete' => 'Удалить'
      ];
      if (!$v['system'] or Misc::isGod()) {
        $r['tools']['edit'] = 'Редактировать';
        if ($v['tagGroup'] and $v['tagGroup']['allowEdit'] and !$d['filterableStr']) $r['tools']['tags'] = 'Редактировать теги';
      }
    }
    return $r;
  }, $d['items'])
];

if (($paths = Hook::paths('dd/fieldsGrid'))) include $paths[0];

?>

<? if ($d['items']) { ?>
<table cellpadding="0" cellspacing="0" id="itemsTable" class="itemsTable valign structures">
<tr>
  <th>&nbsp;</th>
  <th>Название</th>
  <th>Имя</th>
  <th>&nbsp;</th>
  <th>Описание</th>
</tr>
<? foreach ($d['items'] as $k => $v) { ?>
  <tr>
    <td class="tools">
      <a class="iconBtn delete confirm" title="Удалить структуру"
        href="<?= $this->getPath() ?>?a=delete&id=<?= $v['id'] ?>"><i></i></a>
      <a class="iconBtn edit" title="Редактировать структуру"
        href="<?= $this->getPath() ?>?a=edit&id=<?= $v['id'] ?>"><i></i></a>
      <a class="iconBtn fields" title="Редактировать поля структуры"
         href="<?= $this->getPath(1) ?>/ddField/<?= $v['name'] ?>"><i></i></a>
      <a class="iconBtn list" title="Управление выводом"
         href="<?= $this->getPath(1) ?>/ddo/<?= $v['name'] ?>"><i></i></a>
      <? if ($d['params'][0] == 'god') { ?>
        <a class="iconBtn list" title="Редактировать записи"
           href="<?= $this->getPath(1) ?>/ddItems/<?= $v['name'] ?>"><i></i></a>
      <? } ?>
    </td>
    <td><?= $v['title'] ?></td>
    <td><i><?= $v['name'] ?></i></td>
    <td>
      <? if ($v['type'] == 'variant') { ?>
        <span class="smIcons variant tooltip" title="Любая структура"><i></i></span>
      <? } elseif ($v['type'] == 'static') { ?>
        <span class="smIcons static tooltip" title="Статическая структура"><i></i></span>
      <? } else { ?>
        <span class="smIcons dynamic tooltip" title="Динамическая структура"><i></i></span>
      <? } ?>
      <? if ($v['locked']) { ?>
        <span class="smIcons lock tooltip" title="Структура с ограниченным доступом"><i></i></span>
      <? } ?>
      <? if ($v['indx']) { ?>
        <span class="smIcons index tooltip" title="Структура разрешена для индексации"><i></i></span>
      <? } ?>
      &nbsp;
    </td>
    <td>
      <div class="descr">
        <? if ($v['pages']) { ?>
          <? foreach ($v['pages'] as $p) { ?>
            <div>
              <a href="<?= $this->getPath(1).'/pages/'.$p['pageId'].'/editContent' ?>" class="smIcons bordered edit" title="<?= LANG_EDIT ?>"><i></i></a>
              <a href="<?= $this->getPath(1).'/pages/'.$p['pageId'].'/editPage' ?>" class="smIcons bordered settings" title="<?= LANG_SETTING ?>"><i></i></a>
              <a href="/<?= $p['path'] ?>" target="_blank" class="smIcons bordered link" title="<?= LANG_SHOW ?>"><i></i></a>
              <?php /* Сделать тут выпадающую подсказку на которой был бы написан путь */ ?>
              <small class="tooltip" title="<?= $this->enumDddd($p['pathData'], '$title', ' / ') ?>"><?= $p['title'] ?></small>
              <div class="clear"><!-- --></div>
            </div>
          <? } ?>
        <? } ?>
        <? if ($v['descr']) {  ?><small><?= $v['descr'] ?>&nbsp;</small><? } ?>
      </div>
    </td>
  </tr>
<? } ?>
</table>
<? } else { ?>
<p>Пока что не создано ниодной структуры</p>
<? } ?>