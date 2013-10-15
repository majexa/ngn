<?php

class TestDdXls extends TestDd {

  function test() {
    $fm = new DdFieldsManager('a');
    $fm->create([
      'title' => 'Example Title',
      'name'  => 'sampleSttrrr',
      'type' => 'text'
    ]);
    $fm->create([
      'title' => 'Флажок',
      'name'  => 'flagg',
      'type' => 'bool'
    ]);
    $im = DdItemsManager::getDefault('a');
    for ($i=1; $i<=50; $i++) $im->create([
      'sampleSttrrr' => 'stringNumber'.$i,
      'flagg' => Misc::randNum(1) > 5,
    ]);
    $ddo = new Ddo('a', 'xls', ['fieldOptions' => ['getAll' => true]]);
    $ddo->text = true;
    File::delete(UPLOAD_PATH.'/temp/1.xls');
    $im->items->cond->setOrder('sampleSttrrr');
    $lj = new DdXls($im->items);
    LongJobCore::run($lj);
    sleep(3);
    $file = WEBROOT_PATH.LongJobCore::state($lj->id())->data();
    $c = file_get_contents($file);
    $this->assertTrue((bool)strstr($c, 'Example Title'));
    $this->assertTrue((bool)strstr($c, 'stringNumber50'));
  }

  static function tearDownAfterClass() {
  }

}