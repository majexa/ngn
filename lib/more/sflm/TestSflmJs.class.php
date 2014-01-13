<?php

class TestSflmJs extends ProjectTestCase {

  function test() {
    Sflm::$frontend = 'default';
    Sflm::clearCache();
    Sflm::flm('js')->store();
    (new FieldEWisiwigSimple2(['name' => 'dummy']))->typeJs();
    Sflm::flm('js')->getDeltaUrl();

    $newPaths = Sflm::reset('js')->newPaths;
    $this->assertFalse((bool)$newPaths, 'New paths must be empty after reset. Current: '.implode(', ', $newPaths));
    Sflm::flm('js')->store();
    Sflm::flm('js')->addLib('Ngn.Form.El.Phone');
    Sflm::clearCache();
    // ни статического ни динамического кэша не существует
    $this->assertFalse((bool)NgnCache::c()->load(Sflm::flm('js')->pathsCacheKey()), 'Dynamic cache must be empty');
    $this->assertFalse(file_exists(Sflm::flm('js')->cacheFile()), 'Static cache file can not exists. '.Sflm::flm('js')->cacheFile());

    Sflm::flm('js')->store();
    $this->assertFalse((bool)strstr(file_get_contents(Sflm::flm('js')->cacheFile()), 'Ngn.Form.El.Phone'), 'PHONE?:(');

    $v1 = Sflm::flm('js')->version();
    $mtime1 = filemtime(Sflm::flm('js')->cacheFile());
    Sflm::flm('js')->store();
    $this->assertTrue((bool)strstr(file_get_contents(Sflm::flm('js')->cacheFile()), 'Ngn.js'), 'Check if Ngn.js is preset in complete file');
    $mtime2 = filemtime(Sflm::flm('js')->cacheFile());
    $v2 = Sflm::flm('js')->version();
    $this->assertTrue($mtime1 == $mtime2, "mtime1:$mtime1 != mtime2:$mtime2");
    $this->assertTrue($v1 == $v2, "v1:$v1 != v2:$v2. Версии до store() и после не совпадают");

    $filesize1 = filesize(Sflm::flm('js')->cacheFile());
    file_put_contents('/tmp/1/111', file_get_contents(Sflm::flm('js')->cacheFile()));

    $this->assertFalse((bool)strstr(file_get_contents(Sflm::flm('js')->cacheFile()), 'Ngn.Form.El.Phone = new'), 'Check if class is not preset in complete file');
    (new FieldEPhone(['name' => 'dummy'], new Form([])))->typeJs();
    $this->assertTrue((bool)strstr(file_get_contents(Sflm::flm('js')->cacheFile()), 'Ngn.Form.El.Phone = new'), 'Check if class is preset in complete file');
    return;

    $filesize2 = filesize(Sflm::flm('js')->cacheFile());
    $this->assertTrue($filesize2 > $filesize1, "File size is larger then initial ($filesize2 > $filesize1)");
    file_put_contents('/tmp/1/222', file_get_contents(Sflm::flm('js')->cacheFile()));


    // reset - эмитация 2-го открытия страницы. без очистки кэша!
    $v1 = Sflm::flm('js')->version();
    $mtime1 = filemtime(Sflm::flm('js')->cacheFile());
    (new FieldEPhone(['name' => 'dummy'], new Form([])))->typeJs();
    //$this->assertTrue(in_array('Ngn.Form.El.Phone', Sflm::reset('js')->classes->existingClasses));
    //$this->assertTrue(in_array('i/js/ngn/form/Ngn.Form.El.Phone.js', Sflm::reset('js')->paths));
    $v2 = Sflm::flm('js')->version();
    $mtime2 = filemtime(Sflm::flm('js')->cacheFile());
    $this->assertTrue($v1 == $v2, "v1:$v1 != v2:$v2");
    $this->assertTrue($mtime1 == $mtime2, "mtime1:$mtime1 != mtime2:$mtime2");

    File::replace(NGN_PATH.'/i/js/ngn/Ngn.js', '// -- check --', '// -- che --');
    Sflm::flm('js')->store();
    $contains = (bool)strstr(Sflm::flm('js')->code(), '// -- che --');
    $contains2 = (bool)strstr(file_get_contents(Sflm::flm('js')->cacheFile()), '// -- che --');
    $v3 = Sflm::flm('js')->version();
    File::replace(NGN_PATH.'/i/js/ngn/Ngn.js', '// -- che --', '// -- check --');
    $this->assertTrue($contains, 'Code does not contain new string');
    $this->assertTrue($contains2, 'Cached file does not contain new string');
    $this->assertTrue($v2 < $v3, "Version not changed after one of included files has changed");

    // @todo тест на добавления пакета, в путях которого не достаёт классов
  }

}