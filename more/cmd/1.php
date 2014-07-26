<?php

Sflm::clearCache();
Sflm::setFrontendName('default');
Sflm::frontend('js')->addObject('Ngn.Grid');
Sflm::frontend('js')->store();