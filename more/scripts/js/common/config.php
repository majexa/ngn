<?= 'Ngn.config.'.str_replace('/', '.', $_REQUEST['name']).' = '.json_encode(Config::getVar($_REQUEST['name'], true) ?: []);
