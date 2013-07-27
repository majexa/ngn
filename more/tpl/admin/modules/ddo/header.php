<?php

$links[] = [
  'title' => 'Редактирование полей',
  'class' => 'list',
  'link' => $this->getPath(1).'/ddField/'.$d['page']['strName'],
];

$this->tpl('admin/common/module-header', ['links' => $links]);
