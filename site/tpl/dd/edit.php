<?= Slice::html(
  'beforeForm_'.$d['action'].'_'.$d['page']['id'],
  'Блок над формой добавления записи раздела «'.$d['page']['title'].'»'
) ?>

<? $this->tpl('dd/form', $d) ?>