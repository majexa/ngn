<?php

class TestFieldDdTags extends TestFieldDdTagsAbstract {

  function createData() {
    return ['sample' => $this->v1];
  }

  function runTests($request = false) {
    $this->assertTrue(static::$im->items->getItem($this->itemId)['sample'][0]['title'] == $this->v1);
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item['sample'][0]['title'] == $this->v1);
    $this->assertTrue((bool)strstr(static::$im->form->html(), 'name="sample" value="'.$this->v1.'"'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTags">'.$this->v1.'</div>'));
    $this->updateItem(['sample' => 'one, two'], $request);
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample'][0]['title'] == $this->v1);
    $this->assertTrue($item['sample'][1]['title'] == $this->v2);
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item['sample'][0]['title'] == $this->v1);
    $this->assertTrue($item['sample'][1]['title'] == $this->v2);
    $this->fillForm();
    $this->assertTrue((bool)strstr(static::$im->form->html(), 'name="sample" value="'.$this->v1.','.$this->v2.'" />'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTags">'.$this->v1.', '.$this->v2.'</div>'));
    $items = static::$im->items->addF('id', $this->itemId)->getItems();
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItems'))->setItems($items)->els(), '<div class="element f_sample t_ddTags">'.$this->v1.', '.$this->v2.'</div>'));
  }

}
