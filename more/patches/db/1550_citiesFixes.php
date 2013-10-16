<?php

$a = [
  1977 => 3289,
  2195 => 3267,
  3131 => 3303,
  2034 => 3332,
  1171 => 3262,
  1548 => 1548,
  2237 => 1105,
  2239 => 1084,
  2273 => 1025,
  3282 => 1628,
  2337 => 3259,
  3287 => 2562,
  2644 => 3338,
  3079 => 1150,
  3155 => 3304,
  3307 => 938
];
foreach ($a as $old => $new) {
  db()->query("UPDATE tagItems SET tagId=?d WHERE tagId=?d", $new, $old);
  db()->query("DELETE FROM tagCities WHERE id=?d", $old);
}
