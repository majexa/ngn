<?php

Sflm::clearCache();
FileCache::clean();
UploadTemp::cleanup();
print '<div id="result">success</div>';