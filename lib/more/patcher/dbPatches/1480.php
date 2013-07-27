<?php

foreach (db()->selectCol('SELECT id FROM users WHERE actCode=""') as $id) {
  print_r("process $id");
  // db()->query('UPDATE users SET actCode=? WHERE id=?d', Misc::randString(), $id);
}