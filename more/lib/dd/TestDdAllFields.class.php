<?php

class TestDdAllFields extends TestDd {

  function testCreate() {
    Sflm::setFrontend('default');
    SiteConfig::updateVar("fieldE/configSelect1", [1, 2, 3]);
    /* @var $fm DdFieldsManager */
    $fm = O::gett('DdFieldsManager', 'a');
    foreach (DdFieldCore::getTypes() as $type => $v) {
      if (in_array($type, [
        'ddItemSelectDepending',
        'ddFieldsMultiselect',
        'ddItemsSelect',
        'ddItemSelect'
      ])
      ) continue;
      if (DdFieldCore::isFileType($type)) {
        copy(__DIR__.'/dummy.jpg', TEMP_PATH."/$type.jpg");
        $fm->form->req->files[$type.'1'] = [
          'tmp_name' => TEMP_PATH."/$type.jpg"
        ];
      }
      $id = $fm->create([
        'name'  => $type.'1',
        'title' => $v['title'],
        'type'  => $type
      ]);
      $this->assertTrue((bool)$id);
    }
  }

  static function tearDownAfterClass() {
  }

  function testRenderForm() {
    DdCore::imDefault('a')->form->setElementsData()->html();
  }

}