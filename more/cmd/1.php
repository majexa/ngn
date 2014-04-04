<?php

$f = Sflm::frontend('js', 'dummy');
prrc($f->version());
$f->addObject('Ngn.Btn');
prrc($f->version());
$f->getTags();