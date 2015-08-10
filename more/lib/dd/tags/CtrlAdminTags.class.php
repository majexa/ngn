<?php

class CtrlAdminTags extends CtrlAdmin {

  static $properties = [
    'title'  => 'Тэги',
    'onMenu' => false
  ];

  public $groupId;

  public $groupName;

  public $strName;

  /**
   * @var DdTagsTagsBase
   */
  private $tags;

  protected function getParamActionN() {
    if (!isset($this->req->params[2])) return;
    if (is_numeric($this->req->params[2])) {
      return 3;
    }
    else {
      return 4;
    }
  }

  protected function init() {
    if (isset($this->req->params[2])) {
      // Если указан 4-й параметр, значит меняем номер экшена
      $this->d['groupId'] = $this->groupId = $this->req->param(2);
      $this->tags = DdTags::getByGroupId($this->groupId);
      $this->groupName = $this->tags->group->name;
      $this->strName = $this->tags->group->strName;
      $this->d['field'] = O::get('DdFields', $this->strName, [
        'getDisallowed' => true,
        'getSystem'     => true
      ])->getField($this->groupName);
      $this->d['structure'] = O::get('DbItems', 'dd_structures')->getItemByField('name', $this->strName);
      $this->d['path'] = [
        [
          'title' => 'Тэги',
          'link'  => $this->tt->getPath(2)
        ],
        [
          'title' => $this->d['structure']['title'].': '.$this->d['field']['title'],
          'link'  => $this->tt->getPath(4)
        ],
      ];
    }
    //$this->addSubController(new SubCtrlTagsTree($this));
  }

  protected function setPathTplData() {
    if ($this->req->params[3]) {
      $this->d['path'] .= ' / <a href="'.$this->tt->getPath(4).'">'.$this->d['page']['title'].'</a>';
    }
  }

  function action_default() {
    $r = db()->query(<<<SQL
    SELECT
      tagGroups.*,
      dd_fields.title AS title,
      dd_structures.title AS strTitle
    FROM tagGroups
    LEFT JOIN dd_fields ON tagGroups.name=dd_fields.name
    LEFT JOIN dd_structures ON tagGroups.strName=dd_structures.name
SQL
    );
    $items = [];
    foreach ($r as $v) {
      $items[$v['strName']]['title'] = $v['strTitle'];
      $items[$v['strName']]['items'][$v['id']] = $v;
    }
    $this->d['items'] = $items;
    $this->setPageTitle('Редактирование тэгов');
  }

  protected function getGrid() {
    $group = DdTagsGroup::getById($this->groupId);
    return Items::grid([
      'head' => ['ID', 'Тэг', 'Кол-во записей'],
      'body' => array_map(function ($v) {
        return [
          'id'    => $v['id'],
          'tools' => [
            'delete' => 'Удалить',
            'edit'   => [
              'type'      => 'inlineTextEdit',
              'action'    => 'ajax_rename',
              'paramName' => 'title',
              'elN'       => 1
            ]
          ],
          'data'  => Arr::filterByKeys($v, ['id', 'title', 'cnt'])
        ];
      }, (new DdTagsTagsFlat($group))->getTags())
    ]);
  }

  function action_list() {
  }

  function action_json_getItems() {
    $this->json = $this->getGrid();
    $this->json['title'] = 'Тэги поля «'.$this->tags->group->title.'»';
  }

  function action_updateStr() {
    if (!$this->fieldName) throw new Exception('$this->fieldName not defined');
    DdTags::updateTagsStr($this->req->r['tagsStr'], $this->fieldName, $this->pageId);
    $this->redirect('referer');
  }

  function action_updateCounts() {
    //DdTags::rebuildCounts();
    DdTags::rebuildParents();
    $this->redirect();
  }

  function action_ajax_create() {
    $this->tags->create(['title' => $this->req->rq('title')]);
  }

  function action_import() {
    $this->d['tree'] = $this->tags->group->tree;
    $this->d['tpl'] = 'tags/import';
    $this->setPageTitle('Импортирование тэгов поля «'.$this->d['field']['title'].'»');
  }

  function action_makeImport() {
    if (!empty($this->req->r['deleteBeforeImport'])) $this->tags->deleteAll();
    if (strstr(get_class($this->tags), 'Flat')) $this->tags->setImportSeparator($this->req->r['sep'] == 'quote' ? "," : "\n");
    $this->tags->import($this->req->rq('text'));
    $this->redirect($this->tt->getPath(3).'/list');
  }

  function action_json_pageSearch() {
    $this->json['html'] = $this->tt->getTpl('common/searchResults', [
      'name'  => 'pageId',
      'items' => Pages::searchPage($this->req->rq('mask'))
    ]);
  }

  function action_ajax_reorder() {
    DbShift::items($this->req->rq('ids'), 'tags');
  }

  function action_ajax_rename() {
    $this->tags->update($this->req->rq('id'), ['title' => $this->req->rq('title')]);
  }

  function action_ajax_delete() {
    $this->tags->delete($this->req->rq('id'));
  }

  function action_ajax_deleteGroup() {
    DdTagsGroup::getById($this->req->rq('id'))->delete();
  }

  function action_json_create() {
    $id = $this->tags->create([
      'title'    => $this->req->rq('title'),
      'parentId' => $this->req->rq('parentId')
    ]);
    $this->json = $this->tags->getItem($id);
  }

}