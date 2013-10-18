<?php

if (defined('SB_PATH')) {
  $tables = [
    'comments',
    'comments_active',
    'comments_counts',
    'notify_subscribe_items',
    'notify_subscribe_pages',
    'notify_subscribe_types',
    'pageBlocks',
    'pages',
    'slices',
    'storeCart',
    'users_pages',
    'privs',
    'grabber_channels',
    'grabber_keys',
    'rss_subscribes',
    'rating_dd_voted_ips',
    'rating_dd_voted_users',
    'sound_play_time_log',
    'subsList',
    'subs_emails',
    'subs_returns',
    'subs_subscribers',
    'subs_subscribes',
    'subs_users',
    'level_items',
    'level_users',
    'menu',
    'tasks_items',
    'tasks_types'
  ];
  foreach ($tables as $table) q("DROP TABLE IF EXISTS $table");
}


