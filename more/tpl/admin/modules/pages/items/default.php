<?

$this->tpl('dd/css', $d);
$ddItemsLayout = isset($d['page']['settings']['ddItemsLayout']) ?
  $d['page']['settings']['ddItemsLayout'] : 'details';
$this->tpl('admin/modules/pages/items/'.$ddItemsLayout, $d);
