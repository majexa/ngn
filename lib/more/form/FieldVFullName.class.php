<?php

class FieldVFullName extends FieldVAbstract {

  function error($v) {
    return preg_match('/\S+ \S+ \S+/', $v) ? false : 'Введите правильные фамилию, имя и отчество';
  }

}
