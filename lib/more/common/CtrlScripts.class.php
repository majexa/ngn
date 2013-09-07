<?php

class CtrlScripts extends CtrlCommon {

  protected function setAuthUserId() {
  }

  protected function init() {
    $this->hasOutput = false;
    if (!isset($this->req->params[1])) {
      // Если путь к с крипту не указан
      $this->printList();
      return;
    }
    // Получаем путь из исходного "s/path/to/script" в обрезаный "path/to/script"
    $path = preg_replace('/[^\/]*\/(.*)/', '$1', O::get('Req')->path);
    if (strstr($path, 'js/')) {
      header('Content-type: text/javascript; charset='.CHARSET);
      foreach (Sflm::flm('js')->pathVariants($path) as $_path) if (Lib::required($_path, $this->req, '')) return;
    }
    elseif (strstr($path, 'css/')) {
      header('Content-type: text/css; charset='.CHARSET);
      foreach (Sflm::flm('css')->pathVariants($path) as $_path) if (Lib::required($_path, $this->req, '')) return;
    }
    else {
      header("Content-type: text/html; charset=".CHARSET);
    }
    throw new Error404("path '$path' not found");
  }

}