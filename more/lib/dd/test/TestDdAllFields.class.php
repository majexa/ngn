<?php

class TestDdAllFields extends TestDd {

  function testCreate() {
    ProjectConfig::updateVar("fieldE/configSelect1", [1, 2, 3]);
    /* @var $fm DdFieldsManager */
    $fm = O::di('DdFieldsManager', 'a');
    foreach (DdFieldCore::getTypes() as $type => $v) {
      if ($type != 'imagesPreview') continue;
      if (in_array($type, [
        'ddItemSelectDepending',
        'ddFieldsMultiselect',
        'ddItemsSelect',
        'ddItemSelect'
      ])) continue;
      if (DdFieldCore::isFileType($type)) {
        copy(__DIR__.'/dummy.jpg', TEMP_PATH."/$type.jpg");
        if (DdFieldCore::isMultiFileType($type)) {
          $fm->form->req->files[$type.'1'] = [
            [
              'tmp_name' => TEMP_PATH."/$type.jpg"
            ]
          ];
        }
        else {
          $fm->form->req->files[$type.'1'] = [
            'tmp_name' => TEMP_PATH."/$type.jpg"
          ];
        }
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