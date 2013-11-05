<?php

class TestUserEdit extends ProjectTestCase {

  function test() {
    db()->query('TRUNCATE TABLE sessions');
    $id = '55dngja8vaouahi6f50jltrea1';
    db()->insert('sessions',[
      'id' => $id,
      'expires' => 1385114109,
      'data' => 'auth|a:22:{s:2:"id";s:1:"1";s:5:"login";s:5:"admin";s:4:"pass";s:32:"7363a0d0604902af7b70b271a0b96480";s:9:"passClear";s:3:"123";s:6:"status";s:1:"0";s:6:"active";s:1:"1";s:5:"email";s:14:"dummy@test.com";s:5:"phone";N;s:10:"dateCreate";s:19:"2013-10-22 14:28:51";s:10:"dateUpdate";s:19:"2013-10-23 14:10:14";s:8:"lastTime";s:19:"2013-10-23 14:10:14";s:5:"image";s:0:"";s:4:"name";N;s:6:"lastIp";s:0:"";s:4:"text";s:0:"";s:3:"mat";s:1:"0";s:3:"sex";s:0:"";s:6:"access";s:0:"";s:7:"actCode";s:20:"qxsBCpJNlHMMoYY1CE4x";s:14:"userDataPageId";s:1:"0";s:4:"role";N;s:5:"extra";a:0:{}}'
    ]);
    DbModelCore::update('users', 1, ['pass' => 123]);
    $this->assertFalse(!!(db()->select("SELECT * FROM sessions WHERE id=?", $id)));
  }

}