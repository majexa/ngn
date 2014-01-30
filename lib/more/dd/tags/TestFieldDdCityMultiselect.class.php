<?php

class TestFieldDdCityMultiselect extends TestFieldDdTagsAbstract {

  function createItem() {
    return static::$im->create(['sample' => '822,779']);
  }

  function runTests() {
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample'][1][3]['title'] == 'Москва');
    $this->assertTrue($item['sample'][2][3]['title'] == 'Нижний Новгород');
    static::$im->requestUpdate($this->itemId);
    $this->assertTrue((bool)strstr(static::$im->form->html(), ".sample', [[779, 'Москва'], [822, 'Нижний Новгород']]"));
    $s = (new Ddo('a', 'siteItem'))->setItem($item)->els();
    $this->assertTrue((bool)strstr($s, 'Россия → Центральный → Московская область → Москва'));
    $this->assertTrue((bool)strstr($s, 'Россия → Приволжский ФО → Нижегородская область → Нижний Новгород'));
    static::$im->update($this->itemId, ['sample' => '822']);
    $this->assertTrue((bool)strstr(static::$im->form->html(), ".sample', [[822, 'Нижний Новгород']]"));
    $item = static::$im->items->getItem($this->itemId);
    $s = (new Ddo('a', 'siteItem'))->setItem($item)->els();
    $this->assertTrue((bool)strstr($s, 'Россия → Приволжский ФО → Нижегородская область → Нижний Новгород'));
    $this->assertFalse((bool)strstr($s, 'Россия → Центральный → Московская область → Москва'));
  }

}