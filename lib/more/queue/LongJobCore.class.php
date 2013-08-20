<?php

class LongJobCore {

  static function create($id, $object) {
    (new ProjectQueue)->add([
      'class' => 'object',
      'object' => $object,
      'method' => 'cycle',
      'jobId' => 'lj'.$id
    ]);

  }

  static function get($id) {

    return new LongJobState($id);
  }

}