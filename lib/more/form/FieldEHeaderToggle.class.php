<?php

class FieldEHeaderToggle extends FieldEHeaderAbstract {

  function _html() {
    if (!empty($this->options['thelp'])) {
      if (preg_match('/(.*)\[(.*)\](.*)/', $this->options['help'], $m)) {
        $this->options['help'] = $m[1].$m[3];
        $text = $this->options['help'];
        $btnValue = $m[2];
      } else {
        $text = '';
        $btnValue = $this->options['help'];
      }
      return '<div class="toggleHelp dgray">'.
        $text.'<a href="#" class="toggleBtn pseudoLink" data-name="'.$this->options['name'].'" />'.$btnValue.'</a></div>'.
        '<h3>'.$this->options['title'].'</h3>';

    } else {
      return '<h3>'.$this->options['title'].
        '&nbsp;<input type="button" class="toggleBtn" data-name="'.$this->options['name'].
        '" value="&nbsp;&nbsp;↓&nbsp;&nbsp;" /></h3>';
    }
  }
  
}
