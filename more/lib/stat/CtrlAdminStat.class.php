<?php

class CtrlAdminStat extends CtrlAdmin {

  static $properties = [
    'title' => 'Статистика',
    'order' => 310
  ];

}

CtrlAdminStat::$properties['onMenu'] = Config::getVarVar('stat', 'enable');
