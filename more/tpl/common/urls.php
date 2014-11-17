<?php

$urls = explode(', ', $d['v']);
print '<div class="urls"><ul>';
foreach ($urls as $url) {
  print '<li><a href="http://'.Tpl::clearUrl($url).'" target="_blank">'.Tpl::clearUrl($url).'</a></li>';
}
print '</ul></div>';
