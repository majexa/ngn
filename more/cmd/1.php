<?php

Sflm::clearCache();
(new RouterManager)->router()->dispatch();
