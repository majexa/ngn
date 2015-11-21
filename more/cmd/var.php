<?php

print json_encode(Config::getVar(R::get('options')['varName'], true));