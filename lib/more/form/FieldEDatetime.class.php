<?php

class FieldEDatetime extends FieldEDate {

  function _html() {
    $html = parent::_html();
    $v = $this->options['value'] ?: [1, 1, $this->getLastYear(), '10', '00'];
    $html .= '<input type="text" name="'.$this->options['name'].'[]" value="'.$v[3].'" maxlength="2" /> : ';
    $html .= '<input type="text" name="'.$this->options['name'].'[]" value="'.$v[4].'" maxlength="2" />';
    return $html;
  }

}


/*
class FieldEDatetime extends FieldEDate {

  protected function defineOptions() {
    $this->options['maxlength'] = 19;
    $this->options['help'] = 'Формат даты: ДД.ММ.ГГГГ ЧЧ:ММ';
    //$this->options['cssClass'] = "validate-date dateFormat:'%d.%m.%Y %H:%M'";
  }

  protected function init() {
    parent::init();
    if (preg_match('/(\d+)\D+(\d+)\D+(\d+)\D+(\d+)\D+(\d+)/', $this->options['value'], $this->m)) {
      $this->options['value'] = sprintf("%02s.%02s.%04s %02s:%02s",
        $this->m[1], $this->m[2], $this->m[3], $this->m[4], $this->m[5]);
    }
  }
  
  static function form2sourceFormat($v) {
    return $v ? date_reformat($v, 'Y-m-d') : '0000-00-00';
  }
  
  static function source2formFormat($v) {
    if (!$v) return '';
    preg_match('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/', $v, $m);
    return $m[3].'.'.$m[2].'.'.$m[1].' '.$m[4].':'.$m[5];
  }
  
  function _js() {
    return "
    $('{$this->oForm->id}').getElements('.type_{$this->type}').each(function(el) {
      new Ngn.DatePicker(el.getElement('input'), {
        pickerClass: 'datepicker_cp',
        timePicker: true
      });
    });
    ";
  }
  
}
*/