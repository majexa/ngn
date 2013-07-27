<?php

Sflm::clearCache();
NgnCache::clean();
UploadTemp::cleanup();
print '<div id="result">success</div>';