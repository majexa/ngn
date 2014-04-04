<?php

Lang::load('admin');

class DdFieldOptions {
  
  static function order($strName) {
    $options = ['' => '— '.LANG_NOTHING_SELECTED.' —'];
    $o = new DdFields($strName, [
      'getSystem' => true,
      'getDisallowed' => true
    ]);
    $fields = $o->getFields();
    foreach ($fields as $v) {
      $options[$v['name']] = $v['title'];
      $options[$v['name'].' DESC'] = $v['title'].' ['.LANG_REVERSE_ORDER.']';
    }
    $options['rand()'] = 'Случайным образом';
    return $options;
  }
  
  static function date($strName) {
    return array_merge(
      ['' => '— '.LANG_NOTHING_SELECTED.' —'],
      Arr::get(O::get('DdFields', $strName,
        [
          'getSystem' => true,
          'getDisallowed' => true
        ])->getDateFields(), 'title', 'name')
    );
  }
  
  static function fields($strName) {
    return array_merge(
      ['' => '— '.LANG_NOTHING_SELECTED.' —'],
      Arr::get(O::get('DdFields', $strName,
        [
          'getSystem' => true,
          'getDisallowed' => true
        ])->getFields(), 'title', 'name')
    );
  }
  
}
