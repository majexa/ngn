<?php

class DmfaNumberRange extends Dmfa {

  function form2sourceFormat($v, $k, &$data) {
    unset($data[$k]);
    $data[$k.'From'] = isset($v['from']) ? $v['from'] : 0;
    $data[$k.'To'] = isset($v['to']) ? $v['to'] : 0;
  }

}