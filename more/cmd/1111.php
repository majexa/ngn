<?php

Sflm::setFrontendName('asd');
//Sflm::frontend('js', 'asd')->addClass('Ngn.Dialog.Auth');

$c = file_get_contents(Sflm::frontend('js', 'asd')->classes->getAbsPath('Ngn.Dialog.Auth'));
//print $c;
die2(SflmJsClasses::parseValidPreloadClasses($c));
//Sflm::frontend('js', 'asd')->store('sss');
