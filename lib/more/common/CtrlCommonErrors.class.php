<?php

class CtrlCommonErrors extends CtrlCommon {

  protected function errors() {
    return new Errors;
  }

  function action_default() {
    $items = [];
    foreach ($this->errors()->get() as $v) {
      $items[] = [
        'title'       => $v['name'].': '.strip_tags($v['body']),
        'pubDate'     => date('r', $v['time']),
        'description' => "{$v['body']}<hr><pre>{$v['trace']}</pre>",
        'guid'        => md5($v['time'].$v['trace']),
        'link'        => isset($v['url']) ? 'http://'.SITE_DOMAIN.$v['url'] : 'none',
      ];
    }
    $this->rss([
      'title' => SITE_TITLE.': ошибки',
    ], $items);
  }


}