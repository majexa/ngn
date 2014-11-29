<?php

class CtrlCommonErrors extends CtrlCommon {

  protected function errors() {
    return new Errors;
  }

  function action_default() {
    $this->hasOutput = false;
    print '<html><head><meta http-equiv="refresh" content="10"></head>';
    $rows = [];
    foreach ($this->errors()->get() as $v) {
      $rows[] = [
        $v['name'],
        date('d.m.Y H:i:s', $v['time']). //
        (isset($v['entryCmd']) ? '<br><b>cmd:</b> <code>'.$v['entryCmd'].'</code>' : ''). //
        "<br>{$v['body']}<pre>{$v['trace']}</pre>"
      ];
    }
    print '<body>';
    Tt()->tpl('common/table', $rows);
    print '</body></html>';
  }

  function action_rss() {
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
    $this->rss([
      'title' => SITE_TITLE.': ошибки',
    ], $items);
  }

}