<?php

class TestDdItemsCache extends ProjectTestCase {

  function test() {
    $structureManager = new DdStructuresManager();
    $structureManager->deleteByName('asd');
    $structureManager->deleteByName('bsd');
    $structureId = $structureManager->create([
      'title' => 'asd',
      'name'  => 'asd'
    ]);
    $fieldsManager = new DdFieldsManager('asd');
    $fieldsManager->create([
      'title' => 'first',
      'type'  => 'text',
      'name'  => 'first'
    ]);
    $items = new DdDbItemsExtended('asd');
    //
    $items->create([
      'first' => 'Abc',
    ]);
    $items->getItem_cache(1);
    $id = $fieldsManager->create([
      'title' => 'second',
      'type'  => 'text',
      'name'  => 'second'
    ]);
    $this->assertFalse((bool)DdiCache::c(['strName' => 'asd'])->load('i1'), 'dd items cache is clean on dd field create');
    //
    $items->getItem_cache(1);
    $fieldsManager->update($id, [
      'title' => 'bird',
      'name'  => 'third',
      'type'  => 'text'
    ]);
    $this->assertFalse((bool)DdiCache::c(['strName' => 'asd'])->load('i1'), 'dd items cache is clean on dd field update');
    //
    $items->getItem_cache(1);
    $fieldsManager->delete($id);
    $this->assertFalse((bool)DdiCache::c(['strName' => 'asd'])->load('i1'), 'dd items cache is clean on dd field delete');
    //
    $items->getItem_cache(1);
    $structureManager->update($structureId, [
      'title' => 'Bsd',
      'name'  => 'bsd'
    ]);
    $this->assertFalse((bool)DdiCache::c(['strName' => 'asd'])->load('i1'), 'dd items cache is clean on dd structure rename');
    //
    $items = new DdDbItemsExtended('bsd');
    $items->getItem_cache(1);
    $structureManager->delete($structureId);
    $this->assertFalse((bool)DdiCache::c(['strName' => 'bsd'])->load('i1'), 'dd items cache is clean on dd structure delete');
  }

}