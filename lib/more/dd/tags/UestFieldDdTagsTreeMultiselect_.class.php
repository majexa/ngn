<?php

class TestFieldDdTagsTreeMultiselect_ extends TestDd {

  function test() {
    $fm = O::gett('DdFieldsManager', 'a');
    $fm->create([
      'name'  => 'sample',
      'title' => 'sample',
      'type'  => 'ddTags'
    ]);
    $im = DdItemsManager::getDefault('a');
    $v1 = 'one';
    $v2 = 'two';
    $id = $im->create(['sample' => $v1]);
    $im->requestUpdate($id);
    $this->assertTrue($im->items->getItem($id)['sample'][0]['title'] == $v1);
    $item = $im->items->getItemF($id);
    $this->assertTrue($item['sample'][0]['title'] == $v1);
    $this->assertTrue((bool)strstr($im->form->html(), 'name="sample" value="'.$v1.'"'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTags">'.$v1.'</div>'));
    $im->update($id, ['sample' => 'one, two']);
    $item = $im->items->getItem($id);
    $this->assertTrue($item['sample'][0]['title'] == $v1);
    $this->assertTrue($item['sample'][1]['title'] == $v2);
    $item = $im->items->getItemF($id);
    $this->assertTrue($item['sample'][0]['title'] == $v1);
    $this->assertTrue($item['sample'][1]['title'] == $v2);
    $im->requestUpdate($id);
    $this->assertTrue((bool)strstr($im->form->html(), 'name="sample" value="'.$v1.','.$v2.'" />'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTags">'.$v1.', '.$v2.'</div>'));
    //-----------
    return;
    //$this->assertTrue(strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTags">'.$v1.'</div>'));
    //$this->assertTrue();
    //$r4 = ;
    //die2($r4);
    //
    //
    //die2([$r, $r2, $r3, $r4]);
    //$this->assertTrue($item[$v] == '');
    //$im->requestUpdate($id);
    //$item = $im->items->getItem($id);

    //;

    die2((new DdTagsTagsTree(new DdTagsGroup($im->strName, 'metro')))->getParentIds($item['metro']));
    //$im->requestUpdate($id);
    $item = $im->items->getItemF($id);
    //
    //$im->requestUpdate($id);
    //prr($im->form->html());
    $this->assertTrue($item['metro'][0]['childNodes'][0]['childNodes'][0]['title'] == 'Спасская');
    //print "\n-----------\n".(new Ddo('a', 'siteItem'))->setItem($item)->els()."\n-----------\n";
    $im->update($id, ['metro' => [257, 252]]);
    $item = $im->items->getItem($id);
    die2($item['metro']);
    //prr($im->items->getItem($id));
  }

  function uest() {
    $fm = O::gett('DdFieldsManager', 'a');
    $fm->create([
      'name'  => 'metro',
      'title' => 'metro',
      'type'  => 'ddMetroMultiselect'
    ]);
    $im = DdItemsManager::getDefault('a');
    $id = $im->create(['metro' => [257, 252]]);
    $item = $im->items->getItem($id);

    //;

    die2((new DdTagsTagsTree(new DdTagsGroup($im->strName, 'metro')))->getParentIds($item['metro']));
    //$im->requestUpdate($id);
    $item = $im->items->getItemF($id);
    //
    //$im->requestUpdate($id);
    //prr($im->form->html());
    $this->assertTrue($item['metro'][0]['childNodes'][0]['childNodes'][0]['title'] == 'Спасская');
    //print "\n-----------\n".(new Ddo('a', 'siteItem'))->setItem($item)->els()."\n-----------\n";
    $im->update($id, ['metro' => [257, 252]]);
    $item = $im->items->getItem($id);
    die2($item['metro']);
    //prr($im->items->getItem($id));
  }

}
