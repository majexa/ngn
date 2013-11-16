<?php

trait CtrlFormTabs {

  protected function processFormTabs(array $paths, $tpl = 'common/dialogFormTabs') {
    foreach ($paths as $uri) {
      $ctrl = (new RouterManager(['req' => new Req(['uri' => $uri])]))->router()->dispatch()->controller;
      $form = [
        'title' => $ctrl->json['title'],
        'html' => $ctrl->json['form'],
        'id' => Html::getParam($ctrl->json['form'], 'id')
      ];
      if ($ctrl->actionResult) $form['submitTitle'] = $ctrl->actionResult->options['submitTitle'];
      $d['forms'][] = $form;
    }
    $this->ajaxOutput = $this->path->getTpl('common/auth-ajax', $d);
  }

}