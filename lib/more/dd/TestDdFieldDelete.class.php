<?php

class TestDdFieldDelete extends TestDd {

  function test() {
    $fm = O::gett('DdFieldsManager', 'a');
    $fieldId = $fm->create([
      'type' => 'ddTags',
      'name' => 'bbb'
    ]);
    DdCore::imDefault('a')->create(['bbb' => 'asdasd']);
    $fm->delete($fieldId);
    $r = db()->query("SELECT * FROM tags WHERE strName='a' AND groupName='bbb'");
    $this->assertTrue(empty($r), 'delete tag field error');
  }

}