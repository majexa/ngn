<?php

/**
 * Data Manager Field Action
 *
 * look in DataManagerAbstract class: '$this->getDmfa'
 */
abstract class Dmfa {
  
  /**
   * @var DataManagerAbstract
   */
  protected $dm;
  
  function __construct(DataManagerAbstract $dm) {
    $this->dm = $dm;
  }
  
  /**
   * В чем разница между?
   * form2sourceFormat и beforeCreateUpdate
   */

  // function post2formFormat($v) { return $v }
  // function form2sourceFormat($v) { return $v }
  // function source2formFormat($v) { return $v }
  // function elBeforeCreateUpdate(FieldEAbstract $el) {}
  // function elAfterCreateUpdate(FieldEAbstract $el) {}
  // function elAfterUpdate(FieldEAbstract $el) {}
  // function elBeforeDelete(FieldEAbstract $el) {}

  static $processedNames = [];

}