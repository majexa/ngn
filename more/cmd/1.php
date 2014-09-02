<?php

Sflm::setFrontendName();
$ut = new UploadTemp([
  'formId' => 'f383694e9b6abed286cf7ef587aecee3a',
  'tempId' => 'uvisbb2ibgjhu86oj9bu2r6ig6'
]);
$ut->uploadFile(TestRunnerNgn::tempImageFixture(), 'sample');
die2($ut->getFiles());
//UploadTemp::extendFormOptions($form);