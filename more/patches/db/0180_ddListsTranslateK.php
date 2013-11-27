<?php

foreach (q('SELECT k, fieldId FROM dd_lists') as $v) {
  db()->query('UPDATE dd_lists SET k=? WHERE fieldId=?d AND k=?', Misc::transit($v['k'], true),
    $v['fieldId'], $v['k']);
}

$r = q('
SELECT
  dd_fields.name AS fieldName,
  dd_fields.strName
FROM dd_lists
LEFT JOIN dd_fields ON dd_fields.id=dd_lists.fieldId
GROUP BY fieldId
');

foreach ($r as $v) {
  foreach (q("SELECT {$v['fieldName']}, id FROM dd_i_{$v['strName']}") as $v2) {
    $v2[$v['fieldName']] = Misc::transit($v2[$v['fieldName']], true);
    db()->query("UPDATE dd_i_{$v['strName']} SET {$v['fieldName']}=? WHERE id=?d",
      $v2[$v['fieldName']], $v2['id']);
  }
}

print 'complete.';