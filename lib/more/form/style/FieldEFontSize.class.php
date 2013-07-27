<?php

class FieldEFontSize extends FieldESelect {

  static $title = 'Размер шрифт';

  protected function defineOptions() {
    $this->options['options'] = array_merge(['' => 'по умолчанию'], Arr::toOptions([
      '9px',
      '10px',
      '11px',
      '12px',
      '13px',
      '14px',
      '15px',
      '16px',
      '18px',
      '20px',
      '24px',
      '28px',
      '32px',
      '40px',
      '50px',
      '60px',
      '80px',
      '100px',
      '120px',
      '140px',
      '180px',
      '250px',
    ]));
  }

}