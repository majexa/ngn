<?php

class FieldEDdFieldType extends FieldESelect {

  protected function init() {
    $this->options['options'] = Arr::get(DdFieldCore::getTypes(), 'title', 'KEY');
    foreach (array_keys($this->options['options']) as $type) {
      if (!empty(DdFieldCore::getType($type)['disableTypeChange'])) unset($this->options['options'][$type]);
    }
    parent::init();
  }

  function _html() {
    $s = '<table cellpadding="0" cellspacing="0" class="itemsTable">';
    $checked = empty($this->options['value']) ? Arr::firstKey($this->options['options']) : $this->options['value'];
    foreach ($this->options['options'] as $k => $v) {
      $type = DdFieldCore::getType($k)->r;
      $type['disable'] = [];
      if (!empty($type['virtual'])) $type['disable'][] = 'required';
      if (!empty($type['system'])) {
        $type['disable'][] = 'defaultDisallow';
        $type['disable'][] = 'system';
      }
      if (empty($type['tags']) and !DdFieldCore::isBoolType($k)) $type['disable'][] = 'filterable';
      if (!empty($type['notList'])) $type['disable'][] = 'notList';
      $s .= '<tr>'.'<td><input type="radio" name="type" value="'.$k.'"'.($checked == $k ? ' checked' : '').' />'."\n<script type=\"text/javascript\">Ngn.cp.ddFieldType.types.$k = ".Arr::jsObj($type)."</script>\n".'</td>'.'<td><img src="'.DdFieldCore::getIconPath($k).'" title="'.$k.'" /></td>'.'<td>'.$v.'</td>'.'</tr>';
    }
    $s .= '</table>';
    return $s;
  }

  function _js() {
    return "
new Ngn.cp.ddFieldType.Properties($('{$this->form->id()}'), '{$this->options['name']}');
";
  }

}