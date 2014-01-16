<? $this->tpl('admin/modules/ddField/header', $d) ?>

<style>
.t_header {
background: #ebf4f4;
}
</style>

<?

$d['items'] = array_filter($d['items'], function($v) { return $v['editable']; });
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
        if ($v['tagGroup'] and $v['tagGroup']['allowEdit']/* and !$d['filterableStr']*/) $r['tools']['tags'] = 'Редактировать теги';
      }
    }
    return $r;
  }, $d['items'])
];

if (($paths = Hook::paths('dd/fieldsGrid'))) include $paths[0];

?>

<div id="table"></div>

<script>

new Ngn.Grid({
  isSorting: true,
  toolActions: {
    tags: function(row) {
      Ngn.DdTags.dialog(row);
    }
  },
  toolLinks: {
    edit: function(row) {
      return Ngn.getPath(3)+'?a=edit&id='+row.id;
    },
    delete: function(row) {
      return Ngn.getPath(3)+'?a=delete&id='+row.id;
    },
    tags: function(row) {
      return '#';
    }
  },
  data: <?= Arr::jsObj($d['grid']) ?>
});
</script>

<? /*if ($d['items']) { ?>
<table cellpadding="0" cellspacing="0" id="itemsTable">
  <thead>
  <tr>
    <th>&nbsp;</th>
    <th>Название</th>
    <th>Имя</th>
    <th>Тип</th>
    <th>&nbsp;</th>
    <th>Описание</th>
  </tr>
  </thead>
  <tbody>
    <? foreach ($d['items'] as $k => $v) { ?>
  <tr class="t_<?= $v['type'] ?><?= $v['defaultDisallow'] ? ' disallow' : '' ?>" id="<?= 'item_'.$v['id'] ?>">
    <td class="tools loader">
      <div class="dragBox"></div>
      <? if ($v['editable']) { ?>
      <a class="iconBtn delete" title="<?= LANG_DELETE ?>"
         href="<?= $this->getPath() ?>?a=delete&id=<?= $v['id'] ?>"
        ><i></i></a>
      <a class="iconBtn edit" title="<?= LANG_EDIT ?>"
         href="<?= $this->getPath() ?>?a=edit&id=<?= $v['id'] ?>"><i></i></a>
      <?
    }
    else {
      ?>
      &nbsp;
      <? } ?>
      <? if ($v['isTagType']) { ?>
      <a class="iconBtn tags" title="Редактировать теги"
         href="<?= $this->getPath(1) ?>/tags/<?= $v['tagsGroupId'] ?>/list"><i></i></a>
      <? } ?>
      <div class="clear"><!-- --></div>
    </td>
    <td><?= $v['title'] ?><?= $v['required'] ? '<span style="color:#FF0000">*</span>' : '' ?></td>
    <td><i><?= $v['name'] ?></i></td>
    <td><i><img src="<?= DdFieldCore::getIconPath($v['type']) ?>" title="<?= $v['type'] ?>"></i></td>
    <td>
      <small>
        <?= $v['notList'] ? '<nobr>{не выводится}</nobr>' : '' ?>
        <?= $v['system'] ? '<nobr>{системное}</nobr>' : '' ?>
        <?= $v['defaultDisallow'] ? '<nobr>{не доступно}</nobr>' : '' ?>
        <?= $v['editable'] ? '' : '<nobr>{не редактируется}</nobr>' ?>
        &nbsp;</small>
    </td>
    <td>
      <small><?= $v["descr"] ?>&nbsp;</small>
    </td>
  </tr>
    <? } ?>
  </tbody>
</table>
<?
}
else {
  ?>
<p><?= LANG_NO_FIELDS ?>. <a href="<?= $this->getPath() ?>?a=new"><?= LANG_CREATE ?>?</a></p>
<? }*/ ?>