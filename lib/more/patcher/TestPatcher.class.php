<?php

class TestPatcher extends ProjectTestCase {

  function test() {
    $dbPatcher = new DbPatcher;
    $lastLibIds = $dbPatcher->getLastPatchLibIds();
    output('Patch lib IDs: '.implode(',', array_keys($lastLibIds)));
    $this->assertTrue(count($lastLibIds) == 2); // только если проект типа "sb"
    foreach ($dbPatcher->getProjectCurrentPatchIds() as $lib => $id) $this->assertTrue($lastLibIds[$lib] == $id); // новый проект должен быть создан уже пропатченым
    foreach ($dbPatcher->getLibPatchFolders() as $lib => $folder) {
      $newId = ($lastLibIds[$lib] + 10);
      file_put_contents($folder.'/'.$newId.'_sample.php', <<<CODE
<?php

q("CREATE TABLE IF NOT EXISTS new$lib (asd varchar(50) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
CODE
);
      $libId = $dbPatcher->getLastPatchLibIds($lib)[$lib];
      $this->assertTrue($libId == $newId, "$libId == $newId");
    }
    $dbPatcher->patch();
    foreach ($dbPatcher->getProjectCurrentPatchIds() as $lib => $id) $this->assertTrue($lastLibIds[$lib] == $id);
    $lastLibIds = $dbPatcher->getLastPatchLibIds();
    foreach (array_keys($dbPatcher->getLibPatchFolders()) as $lib) db()->exists("new$lib");
    foreach ($dbPatcher->getLibPatchFolders() as $lib => $folder) unlink($folder.'/'.$lastLibIds[$lib].'_sample.php');
  }

}