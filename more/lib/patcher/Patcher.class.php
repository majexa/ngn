<?php

class Patcher {

  protected $type;

  function getLibPatchFolders() {
    $folders = [];
    foreach (Ngn::$basePaths as $f) {
      if (!file_exists("$f/patches")) continue;
      if (!file_exists("$f/patches/.name")) throw new Exception("Patch folder '$f/patches' must contains '.name' file");
      $name = trim(file_get_contents("$f/patches/.name"));
      if (!file_exists("$f/patches/$this->type")) continue;
      $folders[$name] = "$f/patches/$this->type";
    }
    return $folders;
  }

  function getLibPatches($lib) {
    return $this->getFolderPatches($this->getLibPatchFolders()[$lib]);
  }

  function getFolderPatches($folder) {
    foreach (glob("$folder/*") as $v) {
      $file = basename($v);
      if (!preg_match('/(\d+)_*(.*)\.php/', $file, $m)) continue;
      $id = (int)$m[1];
      if (!$id) throw new Exception("Wrong patch number in file $folder/$file");
      $patches[$id] = [
        'title'    => $m[2],
        'id'   => $id,
        'filename' => $file,
        'filepath' => $folder.'/'.$file,
        //'descr'    => $this->getDescr($folder.'/'.$file),
        //'status'   => isset($patchesInfo[$file]['status']) ? $patchesInfo[$file]['status'] : '',
        'modif'    => filemtime($folder.'/'.$file)
      ];
    }
    ksort($patches);
    return array_values($patches);
  }

  function getLastPatchLibIds() {
    $r = [];
    foreach ($this->getLibPatchFolders() as $lib => $folder) {
      if (!($patches = $this->getFolderPatches($folder))) continue;
       $r[$lib] = $patches[count($patches)-1]['id'];
    }
    return $r;
  }

  function getProjectCurrentPatchIds() {
    return ProjectState::get("{$this->type}PatchLastIds");
  }

  function patch() {
    $libIds = $this->getLastPatchLibIds();
    $projectIds = $this->getProjectCurrentPatchIds();
    foreach ($projectIds as $lib => $projectId) if ($projectId < $libIds[$lib]) $this->runPatches($lib);
  }

  function runPatches($lib) {
    $projectId = $this->getProjectCurrentPatchIds()[$lib];
    foreach ($this->getLibPatches($lib) as $patch) {
      if ($patch['id'] > $projectId) {
        $this->runPatch($patch);
        ProjectState::updateSub("{$this->type}PatchLastIds", $lib, $patch['id']);
      }
    }
  }

  protected function runPatch(array $patch) {
    output("running {$this->type} patch {$patch['id']}".($patch['title'] ? " '{$patch['title']}'" : ''), true);
    include $patch['filepath'];
  }

  function updateProjectFromLib() {
    ProjectState::update("{$this->type}PatchLastIds", $this->getLastPatchLibIds());
  }

}