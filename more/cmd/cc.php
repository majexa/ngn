<?php

require_once LIB_PATH.'/core/Memc.class.php';
require_once LIB_PATH.'/core/Mem.class.php';
require_once LIB_PATH.'/more/core/UrlCache.class.php';
require_once LIB_PATH.'/more/sflm/SflmBase.class.php';
require_once LIB_PATH.'/more/sflm/SflmJs.class.php';
require_once LIB_PATH.'/more/sflm/SflmCss.class.php';
require_once LIB_PATH.'/more/sflm/Sflm.class.php';
FileCache::clean();
Mem::clean();
UrlCache::clearCache();
Sflm::clearCache();
output("cleared");