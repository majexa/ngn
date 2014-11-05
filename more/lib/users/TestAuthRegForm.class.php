<?php

// что мне нужно. штука которая будет отправлять запросы на диалог и проверять ответы

class TestAuthRegForm extends ProjectTestCase {

  function test() {
    //
    print (new Curl)->get(TestCore::projectUrl().'/default/auth/json_auth');
    //;
    //;
    //'formAuth'
  }

  //protected function prepare

}