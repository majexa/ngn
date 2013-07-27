<?php

class CtrlAdminUsers extends CtrlAdmin {

  static $properties = [
    'title'  => 'Пользователи',
    'onMenu' => true
  ];

  function action_json_new() {
    $this->json['title'] = 'Создание пользователя';
    return $this->jsonFormActionUpdate(new UsersRegFormAdmin);
  }

  function action_json_edit() {
    $this->json['title'] = 'Редактирование пользователя';
    return $this->jsonFormActionUpdate(new UsersEditFormAdmin($this->req->rq('id')));
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
    $this->setPageTitle('Общий список');
  }

  function action_json_getItems() {
    $this->json = $this->getGrid();
  }

  function getGrid() {
    $r = DbModelCore::pagination(400, 'users');
    if (Config::getVarVar('userReg', 'extraData')) {
      $exItems = (new DdItems('users'))->getItems();
      $exFields = (new DdFields('users'))->fields;
      $exFieldsFilter = Config::getVar('users.exFieldsFilter');
      if (!empty($exFieldsFilter)) $exFields = Arr::filterByKeys($exFields, $exFieldsFilter);
    } else {
      $exItems = [];
      $exFields = [];
    }
    $head = ['id'];
    $config = Config::getVar('userReg');
    if ($config['loginEnable']) $head[] = UserRegCore::getLoginTitle();
    if ($config['emailEnable']) $head[] = 'E-mail';
    if ($config['phoneEnable']) $head[] = 'Телефон';
    return [
      'head' => Arr::append($head, Arr::get($exFields, 'title')),
      'body' => array_map(function($item) use ($exItems, $exFields, $config) {
        $exItem = isset($exItems[$item['id']]) ? Arr::filterByKeys($exItems[$item['id']], array_keys($exFields)) : [];
        foreach ($exFields as $f) {
          if (isset($exItem[$f['name']]) and FieldCore::hasAncestor($f['type'], 'select')) {
            if (is_array($exItem[$f['name']])) {
              $exItem[$f['name']] = implode(', ', $exItem[$f['name']]);
            } else {
              $exItem[$f['name']] = '';
            }
          }
        }
        $data = [$item['id']];
        if ($config['loginEnable']) $data[] = $item['login'];
        if ($config['emailEnable']) $data[] = $item['email'];
        if ($config['phoneEnable']) $data[] = $item['phone'];
        return [
          'id'        => $item['id'],
          'active'    => $item['active'],
          'tools'     => [
            'delete' => 'Удалить',
            'active' => [
              'type' => 'switcher',
              'on'   => $item['active']
            ],
            'edit'   => 'Редактировать'
          ],
          'data'      => Arr::append($data, array_values(Arr::sortByArray($exItem, array_keys($exFields))))
        ];
      }, $r['items'])
    ];
  }

  function action_search() {
    $this->d['items'] = db()->select("
      SELECT id, login, active, email FROM users 
      WHERE login LIKE ? OR email LIKE ? LIMIT 10", $this->req->r['searchLogin'].'%', $this->req->r['searchLogin'].'%');
    $this->d['searchLogin'] = htmlentities($this->req->r['searchLogin'], ENT_QUOTES, CHARSET);
    $this->setPageTitle('Результаты поиска по фрагменту «'.$this->d['searchLogin'].'»');
  }

}