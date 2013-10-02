<?php

class TestDdImage extends TestDd {

  function test() {
    copy(TestRunnerAbstract::$folder.'/fixture/image.jpg', TEMP_PATH.'/image.jpg');
    $this->assertFalse(file_exists(UPLOAD_PATH."/dd/a"));
    (new DdFieldsManager('a'))->create([
      'title' => 'image',
      'name' => 'image',
      'type' => 'imagePreview'
    ]);
    $im = DdItemsManager::getDefault('a');
    $im->form->req['formId'] = $im->form->id();
    $id = $im->requestCreate();
    $this->assertTrue(file_exists(UPLOAD_PATH."/dd/a/$id/image.jpg"));
    $this->assertTrue(file_exists(UPLOAD_PATH."/dd/a/$id/sm_image.jpg"));
    $filesize = filesize(UPLOAD_PATH."/dd/a/$id/image.jpg");
    $smFilesize = filesize(UPLOAD_PATH."/dd/a/$id/sm_image.jpg");
    copy(TestRunnerAbstract::$folder.'/fixture/image2.jpg', TEMP_PATH.'/image.jpg');
    $_FILES = [
      'image' => [
        'tmp_name'  => TEMP_PATH.'/image.jpg'
      ]
    ];
    $im->requestUpdate($id);
    $this->assertTrue(filesize(UPLOAD_PATH."/dd/a/$id/image.jpg") != $filesize);
    $this->assertTrue(filesize(UPLOAD_PATH."/dd/a/$id/sm_image.jpg") != $smFilesize);
    unlink(UPLOAD_PATH."/dd/a/$id/image.jpg");
    copy(TestRunnerAbstract::$folder.'/fixture/image2.jpg', TEMP_PATH.'/image.jpg');
    $_FILES = [
      'image' => [
        'tmp_name'  => TEMP_PATH.'/image.jpg'
      ]
    ];
    $im->requestUpdate($id);
    $this->assertFalse(empty($im->items->getItem($id)['image']));
    $this->assertFalse(empty($im->items->addF('id', $id)->getItems()[$id]['image']));
  }

}