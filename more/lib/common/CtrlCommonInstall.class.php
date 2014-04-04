<?php

class CtrlCommonInstall extends CtrlCommon {
	
	public $paramActionN = 2;
	
	protected function init() {
	  die2(222222);
		if (defined('DB_INSTALLED')) throw new Exception('Database already installed');
	}
	
  function setDefaultTpl() {
    $this->d['mainTpl'] = '_installer/main';
  }

  function action_default() {
    $this->d['tpl'] = '_installer/database';
  	$this->d['host'] = 'localhost';
	  $oDBI = new DbInstaller();
    $oDBI->rootHost = DB_HOST;
    $oDBI->rootUser = DB_USER;
    $oDBI->rootPass = DB_PASS;
    $this->d['installed'] = $oDBI->installed(DB_NAME);
    if ($this->d['installed']) {
    	$this->d['user'] = DB_USER;
    	$this->d['pass'] = DB_PASS;
    	$this->d['name'] = DB_NAME;
    }
  }
  
  function action_json_installDb() {
    $oDBI = new DbInstaller();
    if (!$this->req->r['host']) {
    	$this->json['error'] = 'Хост не введён';
    	return;
    }
    if (!$this->req->r['user']) {
      $this->json['error'] = 'Пользователь не введён';
      return;
    }
    if (!$this->req->r['pass']) {
      $this->json['error'] = 'Пароль не введён';
      return;
    }
    if (!$this->req->r['name']) {
      $this->json['error'] = 'Имя базы данных не введено';
      return;
    }
    $oDBI->rootHost = $this->req->r['host'];
    $oDBI->rootUser = $this->req->r['user'];
    $oDBI->rootPass = $this->req->r['pass'];
    $oDBI->addSqlFile(NGN_PATH.'/lib/installer/sql/common2.sql');
    if (!$oDBI->import($this->req->r['name'])) {
    	$this->json['error'] = $oDBI->error;
    	return;
    }
    SiteConfig::replaceConstant('database', 'DB_INSTALLED', true);
    SiteConfig::updateConstants('database', [
      'DB_NAME' => $this->req->r['name'],
      'DB_USER' => $this->req->r['user'],
      'DB_PASS' => $this->req->r['pass'],
      'DB_HOST' => $this->req->r['host']
    ]);
    $this->json['success'] = true;
  }
  
  function action_asd() {
    die2(222223333344444);
  }

  	
}