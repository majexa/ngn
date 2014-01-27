<?php

class TestDdForm extends TestDd {

  function test() {
    Dir::make(SITE_PATH.'/hooks/dd');
    file_put_contents(SITE_PATH.'/hooks/dd/formInit.php', '<?php
if ($this->strName == "a") $this->jsInlineDynamic = "// one check";
');
    $this->assertTrue((bool)strstr((new DdForm(new DdFields('a'), 'a'))->html(), 'one check'));
  }

}