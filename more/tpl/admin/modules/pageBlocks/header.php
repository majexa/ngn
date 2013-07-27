<?

if ($d['page']) {
  /*
  $links[] = array(
    'title' => 'Блоки',
    'class' => 'list',
    'link' => $this->getPath(3),
  );
  */
  if ($d['god']) {
    if ($d['globalBlocksAdded']) {
      $links[] = [
        'title' => 'Убрать дубликаты глобальных блоков',
        'class' => 'delete',
        'link' => $this->getPath(3).'/deleteGlobalBlocksDuplicates',
      ];
    } else {
      $links[] = [
        'title' => 'Добавить дубликаты глобальных блоков',
        'class' => 'add',
        'link' => $this->getPath(3).'/createGlobalBlocksDuplicates',
      ];
    }
  }

  $this->tpl('admin/common/module-header', ['links' => $links]);
}
