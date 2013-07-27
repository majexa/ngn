<?php

class FormAll {
  
  function __construct() {
    foreach (ClassCore::getDescendants('FieldEAbstract') as $v) {
      $fields[] = [
        'title' => $v['name'],
        'html' => '<u>'.$v['name'].'</u>',
        'name' => $v['name'],
        'type' => $v['name'],
        'required' => true,
        'options' => [
          'asd', 'rereg', '342g 3g3 2'
        ]
      ];
    }
    $o = new Form(new Fields($fields));
    print $o->html();
  }
  
}
