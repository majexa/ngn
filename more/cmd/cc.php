<?php

require_once CORE_PATH.'/lib/Memc.class.php';
require_once CORE_PATH.'/lib/Mem.class.php';
require_once MORE_PATH.'/lib/core/UrlCache.class.php';
require_once MORE_PATH.'/lib/sflm/SflmBase.class.php';
require_once MORE_PATH.'/lib/sflm/SflmJs.class.php';
require_once MORE_PATH.'/lib/sflm/SflmCss.class.php';
require_once MORE_PATH.'/lib/sflm/Sflm.class.php';
FileCache::clean();
Mem::clean();
UrlCache::clearCache();
Sflm::clearCache();
output("cleared");