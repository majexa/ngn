<?php

foreach ([
  'users_settings' => 'userSettings',
  'priv_msgs'      => 'privMsgs',
  'tags_groups'    => 'tagGroups',
  'tags_items'     => 'tagItems',
  'upload_temp'    => 'uploadTemp',
  'subs_list'      => 'subsList',
] as $old => $new) q("RENAME TABLE $old TO $new");

q("DROP TABLE dd_privileges");
q("DROP TABLE privs");
