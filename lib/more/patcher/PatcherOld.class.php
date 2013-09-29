<?

/**
 * Управление патчами.
 * Пока что все патчи являются принудительными.
 *
 */
abstract class PatcherOld {

  public $noCache = false;

  /**
   * Путь к каталогу с БД-патчами
   */
  public $patchesFolders = [];

  protected function initPatchesFolders() {
    // Определяем глобальную константу, потому что использование класса DbPatcher
    // возможно только при установленной NGN
    //die2(Ngn::$basePaths);
    $this->patchesFolders[] = LIB_PATH.'/more/patcher/dbPatches';
    //if (defined('MASTER_PATH') and MASTER_PATH)
    //  $this->patchesFolders[] = MASTER_PATH.'/dbPatches';
  }

  abstract function getSiteLastPatchN();

  abstract function updateSiteLastPatchN($n);

  private function getDescr($filePath) {
    if (!($c = file_get_contents($filePath))) return false;
    if (preg_match('/\*\*(.*)\*\//isU', $c, $matches)) {
      $m = $matches[1];
      $m = preg_replace('/ *\* */', '', $m);
      $m = trim($m);
      $m = str_replace("\n", '<br />', $m);
      preg_replace('/(\s*\*\s*)(.*)/', '$2', $m);
      if ($m) return $m;
    }
    return htmlspecialchars($c);
  }

  function getPatches() {
    $this->initPatchesFolders();
    static $patches;
    if ($patches) return $patches;
    foreach ($this->patchesFolders as $folder) {
      foreach (Dir::getFiles_noCache($folder) as $file) {
        if (!preg_match('/(\d+)_*(.*)\.php/', $file, $m)) continue;
        $patchN = (int)$m[1];
        if (!$patchN) throw new Exception("Wrong patch number in file $folder/$file");
        $patches[$patchN] = [
          'title'    => $m[2],
          'patchN'   => $patchN,
          'filename' => $file,
          'filepath' => $folder.'/'.$file,
          'descr'    => $this->getDescr($folder.'/'.$file),
          'status'   => isset($patchesInfo[$file]['status']) ? $patchesInfo[$file]['status'] : '',
          'modif'    => filemtime($folder.'/'.$file)
        ];
      }
    }
    foreach ($patches as $p)
    krsort($patches);
    return $patches;
  }

  function getPatchesCached() {
    $cache = NgnCache::c();
    if (!$patches = $cache->load($this->getCacheKey())) {
      $patches = $this->getPatches();
      $cache->save($patches, $this->getCacheKey());
    }
    return $patches;
  }

  protected function getCacheKey() {
    return 'patches'.get_class($this);
  }

  function make($patchN) {
    $patches = $this->getPatches();
    if (!isset($patches[$patchN])) throw new Exception("Patch #$patchN does not exists");
    if (!file_exists($patches[$patchN]['filepath'])) throw new Exception("File '{$patches[$patchN]['filepath']}' does not exists");
    ob_start();
    include_once $patches[$patchN]['filepath'];
    $c = ob_get_contents();
    ob_end_clean();
    return $c;
  }

  private $logger;

  function setLogger($logger) {
    $this->logger = $logger;
  }

  private function log($t) {
    if (!is_callable($this->logger)) return;
    call_user_func($this->logger, $t);
  }

  /**
   * Производит патч NGN с Состояния№N к состоянию №M
   *
   * @param integer состояние№N
   * @param integer состояние№M
   */
  function patch() {
    NgnCache::cleanTag($this->getCacheKey());
    // ---------------------------------
    $from = $this->getSiteLastPatchN();
    $to = $this->getNgnLastPatchN();
    if ($from > $to) throw new Exception('$from ('.$from.') can not be less than $to ('.$to.')');
    // Проверка текущего состояния БД и применение патчей
    $patches = $this->getPatches();
    $patchN = $from;
    while ($patchN < $to) {
      $patchN++;
      if (isset($patches[$patchN])) {
        $this->log("Применяем патч $patchN");
        try {
          $text = $this->make($patchN);
          if ($text) $this->log('Результат патча '.$patchN.': <pre>'.$text.'</pre>');
          $this->updateSiteLastPatchN($patchN);
        } catch (Exception $e) {
          $this->log("Патч #$patchN не прошел. Ошибка: ".Err::getErrorText($e));
        }
      }
      $patchNums[] = $patchN;
    }
    if (isset($patchNums)) {
      return true;
    }
    return false;
  }

  function getActualPatches() {
    $actualPatches = [];
    $curPatchN = $this->getSiteLastPatchN();
    foreach ($this->getPatches() as $k => $v) {
      if ($v['patchN'] > $curPatchN) {
        $actualPatches[$k] = $v;
      }
    }
    return $actualPatches;
  }

  /**
   * Определяем необходимость патчить файл
   *
   * @return bool
   */
  function need2patch() {
    return ($this->getNgnLastPatchN() > $this->getSiteLastPatchN());
  }

  /**
   * Возвращает последний номер актуального патча для версии NGN под которой запущен класс
   *
   * @return  integer   Последний номер патча
   */
  function getNgnLastPatchN() {
    return (int)max(array_keys($this->noCache ? $this->getPatches() : $this->getPatchesCached()));
  }

  function updateSiteLastPatchNFromNgn() {
    $this->updateSiteLastPatchN($this->getNgnLastPatchN());
  }

}