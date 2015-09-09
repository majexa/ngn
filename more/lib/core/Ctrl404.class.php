<?php

class Ctrl404 extends CtrlCommon {

  /**
   * @var Exception
   */
  protected $error;

  function __construct(Router $router, Exception $error, array $options = []) {
    if (!($error instanceof NotLoggableError) and !($error instanceof Error404)) {
      Err::log($error);
    }
    $this->error = $error;
    parent::__construct($router, $options);
  }

  function action_default() {
    header('HTTP/1.0 404 Not Found');
    $this->d['text'] = (getConstant('IS_DEBUG') ? Err::outputException($this->error) : '');
    $this->d['mainTpl'] = 'errors/404';
  }

}
