<?php

class DdFieldTypeDateSchedule extends DdFieldType {

  protected function _get() {
    return [
      'title'             => 'Расписание',
      'order'             => 90000,
      'virtual'           => true,
      'disableTypeChange' => true
    ];
  }

}