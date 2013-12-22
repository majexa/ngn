<?php

class TestA extends TestDd {

  function test() {
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
