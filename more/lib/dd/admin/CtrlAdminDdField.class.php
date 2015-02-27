<?php

class CtrlAdminDdField extends CtrlAdmin {

  static $properties = [
    'title'  => 'Поля',
    'descr'  => 'Поля структур',
    'onMenu' => false
  ];

  protected $prepareMainFormTpl = true;

  /**
   * Информация о текущей структуре
   *
   * @var array
   */
  public $strData;

  /**
   * Имя структуры
   *
   * @var string
   */
  public $strName;

  /**
   * @var DdFieldsManager
   */
  protected $im;

  protected function init() {
    $items = new DdStructureItems;
    if (!$this->strData = $items->getItemByField('name', $this->req->param(2))) {
      throw new Exception('Структура не определена');
    }
    $this->d['strData'] = $this->strData;
    $this->d['filterableStr'] = DbModelCore::get('dd_structures', $this->strData['name'], 'filterStrName');
    $this->strName = $this->strData['name'];
    $this->im = O::di('DdFieldsManager', $this->strName);
  }

  function action_default() {
    $this->im->items->cond->addF('strName', $this->strName);
    $this->d['items'] = $this->im->items->getItems();
    $this->setPageTitle('Структуа «'.$this->d['strData']['title'].'»');
  }

  function action_new() {
    if ($this->im->requestCreate()) $this->redirect($this->tt->getPath(3));
    $this->d['form'] = $this->im->form->html();
    $this->setPageTitle('Создание поля структуры «'.$this->d['strData']['title'].'»');
  }

  function action_edit() {
    $fieldData = $this->im->items->getItem($this->req->rq('id'));
    if ($this->im->requestUpdate($this->req->rq('id'))) {
      $this->redirect($this->tt->getPath(3));
    }
    $this->d['form'] = $this->im->form->html();
    $this->setPageTitle('Редактирование поля «'.$fieldData['title'].'» структуры «'.$this->d['strData']['title'].'»');
  }

  function action_delete() {
    $this->im->delete($this->req->rq('id'));
    $this->redirect();
  }

  function action_ajax_delete() {
    $this->im->delete($this->req->rq('id'));
  }

  function action_ajax_reorder() {
    DbShift::items($this->req->rq('ids'), 'dd_fields');
  }

  function action_import() {
    $this->d['tpl'] = 'ddField/import';
    $this->setPageTitle('Импорт полей');
  }

  function action_ajax_importPreview() {
    if (!$this->req->r['text']) return;
    if (!$fields = $this->text2fields($this->req->r['text'])) return;
    $oF = new Form(new Fields($fields));
    print $oF->html();
  }

  function action_ajax_importMake() {
    $this->text2fields($this->req->rq('text'), false);
    print "Перейдите к просмотру... раздела с этой формой";
  }

  function text2fields($text, $onlyGet = true) {
    $tree = O::get('common/Text2Tree')->getTree($text);
    /* @var $oF DdFields */
    $oF = O::get('DdFields', $this->strName);
    $n = 0;
    foreach ($tree as $v) {
      $n++;
      if (preg_match('/(.+)(?:\[(.+)\])/i', $v['title'], $m)) {
        $title = mb_strtolower(trim($m[1]), CHARSET);
        $type = mb_strtolower(trim($m[2]), CHARSET);
        $type = $oF->types[$type] ? $type : 'radio';
      }
      else {
        $title = trim($v['title']);
        $type = 'radio';
      }
      if ($onlyGet) {
        $fields[] = [
          'name'    => 'f'.$n,
          'title'   => $title,
          'type'    => $type,
          'options' => isset($v['children']) ? Arr::get($v['children'], 'title') : []
        ];
      }
      else {
        $oF->create([
          'name'       => 'f'.$n,
          'oid'        => $n * 10,
          'title'      => $title,
          'type'       => $type,
          'valuesList' => implode("\n", isset($v['children']) ? Arr::get($v['children'], 'title') : [])
        ]);
      }
    }
    if ($onlyGet) return $fields;
  }

  function action_deleteAll() {
    $this->im->deleteFields();
    $this->redirect();
  }

  function action_json_selectType() {
    return $this->jsonFormAction(new FormDdFieldType);
  }

}
