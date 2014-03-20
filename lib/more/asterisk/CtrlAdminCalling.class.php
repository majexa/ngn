<?php

class CtrlAdminCalling extends CtrlAdmin {

  static $properties = [
    'title' => 'Обзвон',
    'order' => 310,
  ];

  function action_default() {
    $this->d['table'] = [
      [
        'Телефон',
        'Время последнего звонка',
        'Количество совершенных попыток дозвона',
        'Время следующего звонка',
      ]
    ];
    foreach (glob('/usr/share/asterisk/agi-bin/startCalling/'.PROJECT_KEY.'/*') as $file) {
      $r = require $file;
      $r['phone'] = Misc::parsePhone($r['phone']);
      $r['retryTime'] = datetimeStr($r['startCallingTime'] + $r['retryTime']);
      $r['startCallingTime'] = datetimeStr($r['startCallingTime']);
      $this->d['table'][] = $r;
    }
    $this->d['tpl'] = 'common/tableTable';
  }

}