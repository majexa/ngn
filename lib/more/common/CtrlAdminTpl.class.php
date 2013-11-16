<?php

class CtrlAdminTpl extends CtrlAdmin {

  function action_default() {
    $this->d['items'] = [];
    foreach (Tpl::getList(WEBROOT_PATH.'/site/tpl') as $file) {
      $this->d['items'][] = [
        'link'  => '/'.Tt()->getPath(2).'/'.$file,
        'title' => $file
      ];
    }
    $this->d['tpl'] = 'common/menu';
  }

  function action_edit() {
    $this->d['form'] = (new Form([
      [
        'title'   => '',
        'name'    => 'html',
        'type'    => 'wisiwigSimple',
        'default' => htmlspecialchars(Tt()->getTpl($this->req->param(2)))
      ]
    ]))->html();
    $this->d['tpl'] = 'common/form';
  }

}