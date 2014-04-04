<?php

class TestFieldDdCity extends TestFieldDdTagsAbstract {

  function createData() {
    return ['sample' => 822];
  }

  function runTests($request = false) {
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample']['childNodes'][0]['childNodes'][0]['childNodes'][0]['title'] == 'Нижний Новгород');
    $this->a(['Приволжский ФО', 'Нижегородская область', 'Нижний Новгород']);
    $this->updateItem(['sample' => 779], $request);
    $this->a(['Центральный', 'Московская область', 'Москва']);
  }

  function a(array $titles) {
    $this->fillForm();
    $item = static::$im->items->getItem($this->itemId);
    $html = static::$im->form->html();
    foreach ($titles as $t) $this->assertTrue((bool)strstr($html, ' selected>'.$t.'</option>'), "\n\n$html");
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), 'Россия → '.$titles[0].' → '.$titles[1].' → '.$titles[2]));
  }

}