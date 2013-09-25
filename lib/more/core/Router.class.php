<?php

define('DISP_TYPE_TPL_BY_PATH', 1);
define('DISP_TYPE_DB_TREE_BY_PATH', 2);

abstract class Router {
use Options;

  /**
   * Необходимо ли подключение к БД
   *
   * @var bool
   */
  protected $isDb = true;

  /**
   * @var CtrlCommon
   */
  public $controller;

  /**
   * @var Req
   */
  public $req;

  function __construct(array $options) {
    $this->setOptions($options);
    $this->req = isset($this->options['req']) ? $this->options['req'] : O::get('Req');
    if (isset($this->options['isDb'])) $this->isDb = $this->options['isDb'];
    $this->init();
  }

  function getFrontend() {
    return false;
  }

  protected function init() {
    $this->headers();
    if (($frontend = $this->getFrontend())) Sflm::setFrontend($frontend);
    if ($this->isDb) {
      $this->session();
      $this->auth();
    }
  }

  function dispatch() {
    $this->controller = $this->getController();
    if (getConstant('IS_DEBUG') and $this->req['showCtrl']) die2('Controller: '.get_class($this->controller));
    if (!is_object($this->controller)) throw new Exception('Controller not initialized');
    // В этом месте, после диспатчинга контроллера, может произойти его подмена
    // т.е. контроллер $this->controller заменит себя другим контроллером или, другими словами, передаст управление
    if (get_class($this->controller) == 'Ctrl404') {
      $this->controller->dispatch();
    } else {
      try {
        $this->controller->dispatch();
      } catch (Exception $e) {
        $this->controller = (new Ctrl404($this, $e))->dispatch();
        if (get_class($e) != 'Error404') Err::log($e);
      }
    }
    return $this;
  }

  protected function headers() {
    if (!empty($this->options['disableHeaders'])) return;
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');
  }

  /**
   * Авторизация
   */
  protected function auth() {
    if (isset($this->req->g['logout'])) {
      Auth::logout();
      $url = 'http://'.SITE_DOMAIN.Tt()->getUrlDeletedParams($_SERVER['REQUEST_URI'], ['logout']);
      sendHeader();
      die("<a href='$url'>перейти</a>");
      (new Curl)->exists($url) ? redirect($url) : redirect('/');
    }
    elseif (isset($this->req->g['clear'])) {
      Auth::clear();
      //header('Location: '.Tt()->getUrlDeletedParams($_SERVER['REQUEST_URI'], array('clear')));
    }
    else {
      // Auth::$doNotSavePass = $_REQUEST
      Auth::setAuth();
    }
  }

  protected function session() {
    if (isset($_COOKIE['myComputer']) and $_COOKIE['myComputer'] != 2) Session::$expires = 0;
    if (isset($_REQUEST['sessionId'])) $_COOKIE[session_name()] = $_REQUEST['sessionId'];
    if (empty($this->options['disableHeaders']) and empty($this->options['disableSession'])) Session::init();
  }

  abstract function getController();

  protected function afterOutput() {
  }

  function getOutput() {
    if (!$this->controller) throw new Exception('Controller not defined');
    Err::noticeSwitch(false);
    $html = $this->controller->getOutput();
    $this->afterOutput();
    return $html;
  }

}