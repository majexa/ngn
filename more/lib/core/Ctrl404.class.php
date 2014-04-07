<?php

class Ctrl404 extends CtrlCommon {

  function __construct(Router $router, Exception $error, array $options = []) {
    $this->error = $error;
    parent::__construct($router, $options);
  }

  protected function initAction() {
    $this->setAction('default');
  }

  /**
   * @var Exception
   */
  public $error;

  function action_default() {
    header('HTTP/1.0 404 Not Found');
    $this->d['text'] = '404 — Страница не найдена'.(getConstant('IS_DEBUG') ? Err::outputException($this->error) : '');
    $this->d['mainTpl'] = 'errors/404';
  }

}
