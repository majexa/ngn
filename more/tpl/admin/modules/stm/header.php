<?

$links[] = [
  'title' => 'Темы',
  'class' => 'list',
  'link' => $this->getPath(2),
];
$links[] = [
  'title' => 'Изменить тему',
  'class' => 'edit dialogForm',
  'link' => $this->getPath(2).'/json_changeTheme',
];
$links[] = [
  'title' => 'Создать тему',
  'class' => 'add dialogForm',
  'link' => $this->getPath(2).'/json_themeNewStep1',
];
if ($d['action'] == 'editTheme') {
  $links[] = [
    'title' => 'Предпросмотр темы',
    'class' => 'preview',
    'target' => '_blank',
    'link' => $this->getPath(0).'/?theme[location]='.$d['params'][3].'&theme[design]='.$d['params'][5].'&theme[n]='.$d['params'][6],
  ];
}
$links[] = [
  'separator' => true
];
$links[] = [
  'title' => 'Список меню',
  'class' => 'list',
  'link' => $this->getPath(2).'/menuList',
];
$links[] = [
  'title' => 'Создать меню',
  'class' => 'add',
  'link' => $this->getPath(2).'/menuNewStep1',
];

$this->tpl('admin/common/module-header', ['links' => $links]);
