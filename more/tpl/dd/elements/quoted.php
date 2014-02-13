<?php

/* @var $ddo Ddo */
$ddo = $d['o'];
$fields = array_values($ddo->fields);
for ($n = 0; $n < count($fields); $n++) {
  $f =& $fields[$n];
  print $ddo->el($d[$f['name']], $f['name'], $d['id']);
}
