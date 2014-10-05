<?php

$c = file(NGN_PATH.'/more/config/constants/lang-admin-en.php');
$s = "<?php\n\nreturn [\n";
for ($i=0; $i<count($c); $i++) {
  $l = trim($c[$i]);
  if (!$l) continue;
  if (substr($l, 0, 2) == '/') $title = $l;
  else {
    if (preg_match("/define\\('(.*)', '(.*)'\\);/", $l, $m)) {
      //print_r($m);
      $m[1] = str_replace('LANG_', '', $m[1]);
      $m[1] = strtolower($m[1]);
      $m[1] = Misc::camelCase($m[1], '_');
      $s .= "  '{$m[1]}' => '$m[2]'\n";
    }
  }
}
$s .= "];\n";
file_put_contents(NGN_PATH.'/more/config/vars/lang/ru2.php', $s);