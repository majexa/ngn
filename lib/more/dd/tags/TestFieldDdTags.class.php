<?php

class TestFieldDdTags extends TestFieldDdTagsBase {

  function test() {
    $id = static::$im->create(['sample' => $this->v1]);
    static::$im->requestUpdate($id);
    $this->assertTrue(static::$im->items->getItem($id)['sample'][0]['title'] == $this->v1);
    $item = static::$im->items->getItemF($id);
    $this->assertTrue($item['sample'][0]['title'] == $this->v1);
    $this->assertTrue((bool)strstr(static::$im->form->html(), 'name="sample" value="'.$this->v1.'"'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTags">'.$this->v1.'</div>'));
    static::$im->update($id, ['sample' => 'one, two']);
    $item = static::$im->items->getItem($id);
    $this->assertTrue($item['sample'][0]['title'] == $this->v1);
    $this->assertTrue($item['sample'][1]['title'] == $this->v2);
    $item = static::$im->items->getItemF($id);
    $this->assertTrue($item['sample'][0]['title'] == $this->v1);
    $this->assertTrue($item['sample'][1]['title'] == $this->v2);
    static::$im->requestUpdate($id);
    $this->assertTrue((bool)strstr(static::$im->form->html(), 'name="sample" value="'.$this->v1.','.$this->v2.'" />'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTags">'.$this->v1.', '.$this->v2.'</div>'));
  }

}
