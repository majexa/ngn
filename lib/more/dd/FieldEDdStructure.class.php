<?php

class FieldEDdStructure extends FieldESelect {

  protected function init() {
    $this->options['options'] = ['' => '—'];
    $items = new DdStructureItems;
    if (!empty($this->options['allowedType'])) {
      $allowedType = (array)$this->options['allowedType'];
      $items->addF('type', $allowedType);
    }
    foreach ($items->getItems() as $v) {
      $this->options['options'][$v['name']] = $v['title'].' ('.$v['name'].')';
    }
    parent::init();
  }

  function _js() {
    return "
$('{$this->form->id()}').getElements('.type_ddStructure select').each(function(eSelect){
  eSelect.setStyles({
    'float': 'left',
    'margin-right': '5px'
  });
  var eCont = Elements.from('<div><a href=\"#\" class=\"iconBtn ddStructure tooltip\" title=\"Редактировать структуру\"><i></i></a><div class=\"clear\"><!-- --></div></div>')[0].inject(eSelect, 'after');
  var eEditStrBtn = eCont.getElement('.iconBtn');
  eEditStrBtn.addEvent('click', function(e){
    var strName = eSelect.get('value');
    if (strName) window.open(Ngn.getPath(1) + '/ddField/' + strName, '_blank');
    else alert('Структура не задана');
    return false;
  });
});
";
  }

}