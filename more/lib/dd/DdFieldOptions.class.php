<?php

class DdFieldOptions {
  
  static function order($strName) {
    $options = ['' => '— '.Lang::get('nothingSelected').' —'];
    $o = new DdFields($strName, [
      'getSystem' => true,
      'getDisallowed' => true
    ]);
    $fields = $o->getFields();
    foreach ($fields as $v) {
      $options[$v['name']] = $v['title'];
      $options[$v['name'].' DESC'] = $v['title'].' ['.Lang::get('reverseOrder').']';
    }
    $options['rand()'] = 'Случайным образом';
    return $options;
  }
  
  static function date($strName) {
    return array_merge(
      ['' => '— '.Lang::get('nothingSelected').' —'],
      Arr::get(O::get('DdFields', $strName,
        [
          'getSystem' => true,
          'getDisallowed' => true
        ])->getDateFields(), 'title', 'name')
    );
  }
  
  static function fields($strName) {
    return array_merge(
      ['' => '— '.Lang::get('nothingSelected').' —'],
      Arr::get(O::get('DdFields', $strName,
        [
          'getSystem' => true,
          'getDisallowed' => true
        ])->getFields(), 'title', 'name')
    );
  }
  
}
