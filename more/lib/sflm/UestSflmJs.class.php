<?php

class UestSflmJs extends ProjectTestCase {

  static function setUpBeforeClass() {
    Sflm::setFrontendName('custom');
  }



  function uest() {
    //Sflm::frontend('js')->addObject('Ngn.Grid.Dialog', 'direct', true);
    //Sflm::frontend('js')->store();
    //die2('-');
    $this->assertFalse((bool)strstr(file_get_contents(Sflm::frontend('js')->cacheFile()), 'Ngn.Form.El.Phone'), 'PHONE?:(');

    $v1 = Sflm::frontend('js')->version();
    $mtime1 = filemtime(Sflm::frontend('js')->cacheFile());
    Sflm::frontend('js')->store();
    $this->assertTrue((bool)strstr(file_get_contents(Sflm::frontend('js')->cacheFile()), 'Ngn.js'), 'Check if Ngn.js is preset in complete file');
    $mtime2 = filemtime(Sflm::frontend('js')->cacheFile());
    $v2 = Sflm::frontend('js')->version();
    $this->assertTrue($mtime1 == $mtime2, "mtime1:$mtime1 != mtime2:$mtime2");
    $this->assertTrue($v1 == $v2, "v1:$v1 != v2:$v2. Версии до store() и после не совпадают");

    $filesize1 = filesize(Sflm::frontend('js')->cacheFile());
    $this->assertFalse((bool)strstr(file_get_contents(Sflm::frontend('js')->cacheFile()), 'Ngn.Form.El.Phone = new'), 'Check if class is not preset in complete file');
    (new FieldEPhone(['name' => 'dummy'], new Form([])))->typeJs();
    Sflm::frontend('js')->getTags();
    $this->assertTrue((bool)strstr(file_get_contents(Sflm::frontend('js')->cacheFile()), 'Ngn.Form.El.Phone = new'), 'Check if class is preset in complete file '.Sflm::frontend('js')->cacheFile());

    $filesize2 = filesize(Sflm::frontend('js')->cacheFile());
    $this->assertTrue($filesize2 > $filesize1, "File size is larger then initial ($filesize2 > $filesize1)");

    // reset - эмитация 2-го открытия страницы. без очистки кэша!
    $v1 = Sflm::frontend('js')->version();
    $mtime1 = filemtime(Sflm::frontend('js')->cacheFile());
    (new FieldEPhone(['name' => 'dummy'], new Form([])))->typeJs();
    //$this->assertTrue(in_array('Ngn.Form.El.Phone', Sflm::resetFrontend('js')->classes->existingClasses));
    //$this->assertTrue(in_array('i/js/ngn/form/Ngn.Form.El.Phone.js', Sflm::resetFrontend('js')->paths));
    $v2 = Sflm::frontend('js')->version();
    $mtime2 = filemtime(Sflm::frontend('js')->cacheFile());
    $this->assertTrue($v1 == $v2, "v1:$v1 != v2:$v2");
    $this->assertTrue($mtime1 == $mtime2, "mtime1:$mtime1 != mtime2:$mtime2");

    File::replace(NGN_PATH.'/i/js/ngn/Ngn.js', '// -- check --', '// -- che --');
    Sflm::frontend('js')->store();
    $contains = (bool)strstr(Sflm::frontend('js')->code(), '// -- che --');
    $contains2 = (bool)strstr(file_get_contents(Sflm::frontend('js')->cacheFile()), '// -- che --');
    $v3 = Sflm::frontend('js')->version();
    File::replace(NGN_PATH.'/i/js/ngn/Ngn.js', '// -- che --', '// -- check --');
    $this->assertTrue($contains, 'Code does not contain new string');
    $this->assertTrue($contains2, 'Cached file does not contain new string');
    $this->assertTrue($v2 < $v3, "Version not changed after one of included files has changed");

    // @todo тест на добавления пакета, в путях которого не достаёт классов
  }

}