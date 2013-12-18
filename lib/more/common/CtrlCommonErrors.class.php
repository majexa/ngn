<?php

class CtrlCommonErrors extends CtrlCommon {

  protected function errors() {
    return new Errors;
  }

  function action_default() {
    $items = [];
    foreach ($this->errors()->get() as $v) {
      $v['body'] = preg_replace('/\s+/u', ' ', $v['body']);
      $items[] = [
        'title'       => Misc::cut($v['name'].': '.strip_tags($v['body']), 100),
        'time'        => $v['time'],
        'pubDate'     => date('r', $v['time']),
        'description' => (isset($v['exceptionClass']) ? "Class: {$v['exceptionClass']}-----\n" : '')."{$v['body']}<hr><pre>{$v['trace']}</pre>",
        'guid'        => md5($v['time'].$v['trace']),
        'link'        => isset($v['url']) ? 'http://'.SITE_DOMAIN.$v['url'] : 'none',
      ];
    }
    $items = Arr::sortByOrderKey($items, 'time', SORT_DESC);
    //foreach ($items as $n => &$v) $v['title'] = ($n + 1).') '.$v['title'];
    $this->rss([
      'title' => SITE_TITLE.': ошибки',
    ], $items);
  }


}