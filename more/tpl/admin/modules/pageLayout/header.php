<?php

$links = [
  [
    'title' => 'Формат страницы <b>по умолчанию</b>',
    'link' => $this->getPath(2),
    'class' => 'layout'
  ]
];

if (!empty($d['layoutPages'])) {
  foreach ($d['layoutPages'] as $v) {
    $links[] = [
      'title' => 'Формат страницы <b>'.$v['title'].'</b>',
      'link' => $this->getPath(2).'/'.$v['id'],
      'class' => 'layout'
    ];
  }
}

$this->tpl('admin/common/module-header', [
  'links' => $links
]);
