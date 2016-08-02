<?php

class CtrlAdminUsers extends CtrlAdmin {

  static $properties = [
    'onMenu' => true
  ];

  function action_json_new() {
    return $this->jsonFormActionUpdate(new UsersRegFormAdmin);
  }

  function action_json_edit() {
    return new UsersEditFormAdmin($this->req->rq('id'));
  }

  function action_ajax_delete() {
    DbModelCore::delete('users', $this->req->r['id']);
  }

  function action_ajax_activate() {
    DbModelCore::update('users', $this->req->r['id'], ['active' => 1]);
    Ngn::fireEvent('users.activation', $this->req->r['id']);
  }

  function action_ajax_deactivate() {
    DbModelCore::update('users', $this->req->r['id'], ['active' => 0]);
  }

  function action_default() {
    $this->d['grid'] = $this->getGrid();
  }

  function action_json_getItems() {
    $this->json = $this->getGrid();
    $this->json['pagination'] = $this->items()->getPagination();
  }

  function action_json_search() {
    $keyword = '%'.$this->req->rq('word').'%';
    $this->items()->cond->addLikeFilter([
      'login',
      'email',
      'name',
      'role'
    ], $keyword);
    $this->json = $this->getGrid();
    $this->json['pagination'] = $this->items()->getPagination();
  }

  protected $roles;

  protected $config;

  protected function init() {
    $this->config = Config::getVar('userReg');
    if ($this->config['roleEnable']) {
      $this->roles = Config::getVar('userRoles');
    }
    else {
      $this->roles = [];
    }
  }

  protected $items;

  protected function items() {
    if (isset($this->items)) return $this->items;
    $this->items = new DbItems('users', [
      'paginationOptions' => [
        'n' => 20
      ]
    ]);
    $this->items->hasPagination = true;
    return $this->items;
  }

  protected function getHead() {
    $head = ['id'];
    $config = Config::getVar('userReg');
    if ($config['loginEnable']) $head[] = UserRegCore::getLoginTitle();
    if ($config['emailEnable']) $head[] = 'E-mail';
    if ($config['phoneEnable']) $head[] = Locale::get('phone', 'users');
    if ($config['nameEnable']) $head[] = Locale::get('name', 'users');
    if ($config['roleEnable']) {
      $head[] = Locale::get('profileType', 'users');
    }
    Arr::append($head, Arr::get($this->getExFields(), 'title'));
    return $head;
  }

  protected $exFields;

  protected function getExFields() {
    if (isset($this->exFields)) return $this->exFields;
    if (!Config::getVarVar('userReg', 'extraData')) {
      return $this->exFields = [];
    }
    $exFields = (new DdFields('users'))->getFieldsF();
    $exFieldsFilter = Config::getVar('users.exFieldsFilter');
    if (!empty($exFieldsFilter)) $exFields = Arr::filterByKeys($exFields, $exFieldsFilter);
    return $this->exFields = $exFields;
  }

  protected function getData($item) {
    $data = [$item['id']];
    if ($this->config['loginEnable']) $data[] = $item['login'];
    if ($this->config['emailEnable']) $data[] = $item['email'];
    if ($this->config['phoneEnable']) $data[] = $item['phone'];
    if ($this->config['nameEnable']) $data[] = $item['name'];
    if ($this->config['roleEnable']) $data[] = $this->roles[$item['role']];
    return $data;
  }

  protected function getItems() {
    return $this->items()->getItems();
  }

  function getGrid() {
    $items = $this->getItems();
    if (Config::getVarVar('userReg', 'extraData')) {
      $exItems = (new DdItems('users'))->getItems();
    }
    else {
      $exItems = [];
    }
    $exFields = $this->getExFields();
    return [
      'pagination' => $this->items()->getPagination(),
      'head'       => $this->getHead(),
      'body'       => array_map(function ($item) use ($exItems, $exFields) {
        // @todo эту вещь нужно реализовать через DDO
        $exItem = isset($exItems[$item['id']]) ? Arr::filterByKeys($exItems[$item['id']], array_keys($exFields)) : [];
        foreach ($exFields as $f) {
          if (isset($exItem[$f['name']]) and FieldCore::hasAncestor($f['type'], 'select')) {
            if (is_array($exItem[$f['name']])) {
              $exItem[$f['name']] = implode(', ', $exItem[$f['name']]);
            }
            else {
              $exItem[$f['name']] = '';
            }
          }
        }
        return [
          'id'     => $item['id'],
          'active' => $item['active'],
          'tools'  => [
            'delete' => Locale::get('delete'),
            'active' => [
              'type' => 'switcher',
              'on'   => $item['active']
            ],
            'edit'   => Locale::get('edit')
          ],
          'data'   => Arr::append($this->getData($item), array_values(Arr::sortAssoc($exItem, array_keys($exFields))))
        ];
      }, $items)
    ];
  }

}

CtrlAdminUsers::$properties['title'] = Locale::get('users', 'admin');
