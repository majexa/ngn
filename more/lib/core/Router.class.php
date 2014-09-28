<?php

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

  protected function getFrontendName() {
    return false;
  }

  protected function init() {
    $this->headers();
    if ($this->isDb) {
      $this->session();
      $this->auth();
    }
    if (($frontend = $this->getFrontendName())) Sflm::setFrontendName($frontend, true);
  }

  final function dispatch() {
    $this->controller = $this->getController();
    if (getConstant('IS_DEBUG') and $this->req['showCtrl']) die2('Controller: '.get_class($this->controller));
    if (!is_object($this->controller)) throw new Exception('Controller not initialized');
    // В этом месте, после диспатчинга контроллера, может произойти его подмена,
    // т.е. контроллер $this->controller заменит себя другим контроллером или, другими словами, передаст управление

    $this->controller->dispatch();
    return $this;

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
      jsRedirect($url);
    }
    elseif (isset($this->req->g['clear'])) {
      Auth::clear();
      //header('Location: '.Tt()->getUrlDeletedParams($_SERVER['REQUEST_URI'], array('clear')));
    }
    else {
      Auth::setAuth();
    }
  }

  protected function session() {
    if (isset($_COOKIE['myComputer']) and $_COOKIE['myComputer'] != 2) Session::$expires = 0;
    if (isset($_REQUEST['sessionId'])) $_COOKIE[session_name()] = $_REQUEST['sessionId'];
    if (empty($this->options['disableHeaders']) and empty($this->options['disableSession'])) Session::init();
  }

  abstract function _getController();

  final function getController() {
    $controller = $this->_getController();
    if ($this->req['showController']) die2("Current controller is: <u>".get_class($controller)."</u>");//"\nPath: ".Lib::getPath($class));
    return $controller;
  }

  protected function afterOutput() {
  }

  protected function sflmStore() {
    if (!empty($this->req->options['disableSflmStore'])) return;
    Sflm::frontend('js')->store('afterAction');
    Sflm::frontend('css')->store('afterAction');
  }

  protected function sflmInject() {
    $tags = Sflm::frontend('js')->getTags()."\n".Sflm::frontend('css')->getTags();
    $this->html = str_replace('{sflm}', $tags, $this->html);
  }

  protected $html;

  function getOutput() {
    if (!$this->controller) throw new Exception('Controller not defined');
    Err::noticeSwitch(false);
    $this->html = $this->controller->getOutput();
    $this->afterOutput();
    //$this->sflmStore();
    //$this->sflmInject();
    return $this->html;
  }

}