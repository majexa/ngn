<?php

abstract class CtrlMapper extends CtrlCammon {

  abstract function getMappingObject();
  
  protected function getActionObject($action) {
      return true;
  }
  
  protected function action() {
    $o = $this->getMappingObject();
      $method = $this->actionBase;
      if (method_exists($o, $method)) {
        $refl = new ReflectionMethod($o, $method);
        $p = [];
        foreach ($refl->getParameters() as $v)
          if (!isset($this->req->g[$v->name]))
            throw new Exception('=(');
          else {
            if (isset($o->required) and in_array($v->name, $o->required))
              Arr::checkEmpty($this->req->g, $v->name);
            $p[] = $this->req->g[$v->name];
          }
        if (($r = call_user_func_array([$o, $method], $p)) !== null) {
          if ($this->actionPrefix == 'ajax') {
            $this->ajaxOutput = $r;
          } elseif ($this->actionPrefix == 'json') {
            $this->json = $r;
          }
        }
      } else {
        throw new NoMethodException($method);
      }
  }

}
