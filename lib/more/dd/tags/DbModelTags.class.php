<?php

class DbModelTags extends DbModel {

  static function beforeCreateUpdate(array &$data) {
    if (empty($data['name']) and !empty($data['title']))
      $data['name'] = DdTags::title2name($data['title']);
    if (!empty($data['title']))
      $data['title'] = O::get('FormatText')->typo($data['title']);    
  }

}