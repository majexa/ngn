<?php

class TestSflmJs extends NgnTestCase {

  function test() {
    Sflm::$frontend = 'default';
    Sflm::get('js')->store();
    Sflm::get('js')->addLib('Ngn.Form.El.Phone');
    Sflm::clearCache();
    // ни статического ни динамического кэша не существует
    $this->assertFalse((bool)NgnCache::c()->load(Sflm::get('js')->pathsCacheKey()), 'Dynamic cache must be empty');
    $this->assertFalse(file_exists(Sflm::get('js')->cacheFile()), 'Static cache file can not exists');

    Sflm::get('js')->store();
    $v1 = Sflm::get('js')->version();
    $mtime1 = filemtime(Sflm::get('js')->cacheFile());
    Sflm::get('js')->store();
    $this->assertTrue((bool)strstr(file_get_contents(Sflm::get('js')->cacheFile()), 'Ngn.js'), 'Check if Ngn.js is preset in complete file');
    $mtime2 = filemtime(Sflm::get('js')->cacheFile());
    $v2 = Sflm::get('js')->version();
    $this->assertTrue($mtime1 == $mtime2, "mtime1:$mtime1 != mtime2:$mtime2");
    $this->assertTrue($v1 == $v2, "v1:$v1 != v2:$v2");

    $filesize1 = filesize(Sflm::get('js')->cacheFile());
    (new FieldEPhone(['name' => 'dummy'], new Form([])))->js();
    $this->assertTrue((bool)strstr(file_get_contents(Sflm::get('js')->cacheFile()), 'Ngn.Form.El.Phone = new'), 'Check if class is preset in complete file');
    //die2('----');
    $this->assertTrue(filesize(Sflm::get('js')->cacheFile()) > $filesize1, 'File size is larger then initial');

    // reset - эмитация 2-го открытия страницы. без очистки кэша!
    $v1 = Sflm::get('js')->version();
    $mtime1 = filemtime(Sflm::get('js')->cacheFile());
    (new FieldEPhone(['name' => 'dummy'], new Form([])))->js();
    //$this->assertTrue(in_array('Ngn.Form.El.Phone', Sflm::reset('js')->classes->existingClasses));
    //$this->assertTrue(in_array('i/js/ngn/form/Ngn.Form.El.Phone.js', Sflm::reset('js')->paths));
    $v2 = Sflm::get('js')->version();
    $mtime2 = filemtime(Sflm::get('js')->cacheFile());
    $this->assertTrue($v1 == $v2, "v1:$v1 != v2:$v2");
    $this->assertTrue($mtime1 == $mtime2, "mtime1:$mtime1 != mtime2:$mtime2");
    return;

    Sflm::$frontend = 'admin';
    $adminMtime2 = filemtime(UPLOAD_PATH.'/'.Sflm::get('js')->store()->filePath());
    $this->assertTrue($adminMtime1 == $adminMtime2, "mtime1:$adminMtime1 != mtime2:$adminMtime2");
    return;

    //Sflm::$frontend = 'admin';
    //$adminMtime1 = filemtime(UPLOAD_PATH.'/'.Sflm::get('js')->store()->filePath());
    // 1. Тест на то, что время изменения до store() и после не изменяется
    //Sflm::get('js')->store();
    //$filesize1 = filesize($file);
    // 2. Тест на то, что версия фронтенда 'default' до storeDefault() и после не изменяется
    // 3. Тест на то, что версия фронтенда 'default' до new FieldE{...} и после неизменяется

    $v1 = Sflm::get('js')->version();
    $mtime1 = filemtime($file);
    (new FieldEPhone(['name' => 'dummy'], new Form([])))->js();
    // reset - эмитация 2-го открытия страницы
    $this->assertTrue(in_array('Ngn.Form.El.Phone', Sflm::reset('js')->classes->existingClasses));
    $this->assertTrue(in_array('i/js/ngn/form/Ngn.Form.El.Phone.js', Sflm::reset('js')->paths));
    $v2 = Sflm::get('js')->version();
    $this->assertTrue($v1 == $v2, "v1:$v1 != v2:$v2");
    Sflm::get('js')->store();
    $mtime2 = filemtime($file);
    $this->assertTrue($mtime1 == $mtime2, "mtime1:$mtime1 != mtime2:$mtime2");

    // Файл другого фроненда (admin) не менялся
  }

}