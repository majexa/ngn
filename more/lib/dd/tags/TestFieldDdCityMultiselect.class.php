<?php

class TestFieldDdCityMultiselect extends TestFieldDdTagsAbstract {

  function createData() {
    return [static::$tagFieldName => '779,822'];
  }

  function runTests($request = false) {
    static::$im->items->addTagFilter(static::$tagFieldName, 300);
    $this->assertTrue(!empty(static::$im->items->getItems()[$this->itemId]), 'tag add fails');
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item[static::$tagFieldName][1][3]['title'] == 'Москва');
    $this->assertTrue($item[static::$tagFieldName][2][3]['title'] == 'Нижний Новгород');
    $this->fillForm();
    $this->assertTrue((bool)strstr(static::$im->form->html(), ".sample', [[779, 'Москва'], [822, 'Нижний Новгород']]"));
    $s = (new Ddo('a', 'siteItem'))->setItem($item)->els();
    $this->assertTrue((bool)strstr($s, 'Россия → Центральный → Московская область → Москва'));
    $this->assertTrue((bool)strstr($s, 'Россия → Приволжский ФО → Нижегородская область → Нижний Новгород'));
    $this->updateItem([static::$tagFieldName => '822'], $request);
    $this->assertTrue((bool)strstr(static::$im->form->html(), ".sample', [[822, 'Нижний Новгород']]"));
    $item = static::$im->items->getItem($this->itemId);
    $s = (new Ddo('a', 'siteItem'))->setItem($item)->els();
    $this->assertTrue((bool)strstr($s, 'Россия → Приволжский ФО → Нижегородская область → Нижний Новгород'));
    $this->assertFalse((bool)strstr($s, 'Россия → Центральный → Московская область → Москва'));
  }

}