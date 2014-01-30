<?php

class TestFieldDdCity extends TestFieldDdTagsAbstract {

  function createItem() {
    return static::$im->create(['sample' => 822]);
  }

  function runTests() {
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample']['childNodes'][0]['childNodes'][0]['childNodes'][0]['title'] == 'Нижний Новгород');

    //static::$im->requestUpdate($this->itemId);
    //print static::$im->form->html();
    //print "\n--------------\n".(new Ddo('a', 'siteItem'))->setItem($item)->els();
  }

}