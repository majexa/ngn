<?php

Sflm::clearCache();
FileCache::c()->clean();
UploadTemp::cleanup();
print '<div id="result">success</div>';