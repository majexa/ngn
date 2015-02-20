<?php

if (empty($d)) return;
print '<ul>';
foreach ($d as $v) {
  print //
    '<li'.($v['selected'] ? ' class="selected"' : ''). //
    (isset($v['data']) ? Html::dataParams($v['data']) : '').'>'. //
    (isset($v['link']) ? '<a href="'.$v['link'].'">' : ''). //
    '<span>'. //
    $v['title']. //
    '</span>'. //
    (isset($v['link']) ? '</a>' : ''). //
    (!empty($v['children']) ? $this->getTpl('common/ul', $v['children']) : ''). //
    '</li>';
}
print '</ul>';
