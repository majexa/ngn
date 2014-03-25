<?php

$f = Sflm::flm('js', 'dummy');
prrc($f->version());
$f->addObject('Ngn.Btn');
prrc($f->version());
$f->getTags();