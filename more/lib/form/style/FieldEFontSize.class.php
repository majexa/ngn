<?php

class FieldEFontSize extends FieldESelect {

  static $title;

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'options' => array_merge(['' => 'default'], Arr::toOptions([
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
        '1000px',
      ]))
    ]);
  }

}

FieldEFontSize::$title = Locale::get('fontSize');
