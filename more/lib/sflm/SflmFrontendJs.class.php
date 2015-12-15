<?php

class SflmFrontendJs extends SflmFrontend {

  /**
   * @var SflmJsClasses
   */
  public $classes;

  /**
   * @var SflmMtDependencies
   */
  protected $mtDependencies;

  /**
   * @var string
   */
  protected $mtCode = '';

  protected function init() {
    $this->classes = new SflmJsClasses($this);
    $this->mtDependencies = O::get('SflmMtDependencies', NGN_ENV_PATH.'/mootools');
  }

  protected function __addPath($path, $source = null) {
    $this->addPath($path, $source);
  }

  function addPath($path, $source = 'root') {
    $this->classes->processPath($path, $source);
  }

  function _addLib($lib) {
    if (!$this->base->exists($lib)) throw new Exception("Lib '$lib' does not exists");
    $this->log("Adding lib '$lib'");
    foreach ($this->base->getPaths($lib) as $path) $this->_addPath($path);
    return $this;
  }

  function addClass($name, $source = 'root', $strict = false) {
    $this->checkNotStored();
    return $this->classes->addClass($name, $source, $strict);
  }

  /**
   * @param string $str Class name or its part; path or part of it
   * @return bool
   */
  function exists($str) {
    return Arr::strExists(Sflm::frontend('js')->getPaths(), $str);
  }

  protected function getStaticPaths() {
    return $this->base->getPaths('core', true);
  }

  function store($source = 'root') {
    parent::store($source);
    $this->classes->frontendClasses->store();
  }

  function processCode($code, $source) {
    $this->checkNotStored();
    R::set('code', $code);
    $this->classes->processCode($code, $source);
    $this->mtProcessCode($code);
  }

  function mtProcessCode($code) {
    $this->mtCode .= $this->mtDependencies->parse($code);
  }

  function processHtml($html, $source) {
    $this->checkNotStored();
    if (!preg_match_all('!<script>(.*)</script>!Us', $html, $m)) return false;
    foreach ($m[1] as $code) $this->processCode($code, $source);
    return $html;
  }

  function _code() {
    $code = parent::_code();
    foreach ($this->debugPaths as $path) {
      $this->mtCode .= $this->mtDependencies->parse(file_get_contents($this->base->getAbsPath($path)));
    }
    $this->mtCode .= $this->mtDependencies->parse($code);
    return $this->mtCode.$code;
  }

  protected function orderDebugPaths() {
    $classes = [];
    foreach ($this->debugPaths as $path) {
      $path = Misc::removeSuffix('.js', $path);
      $classes[] = preg_replace('/.*\\/([^\\/]+)/', '$1', $path);
    }
    $tree = [];
    foreach ($classes as $i => $path) {
      if (!strstr($path, '.')) continue;
      // ������� ��������� ����� � ������ (.Xxx)
      $parent = preg_replace('/(.+)\\.[^.]+/', '$1', $path);
      // ������� ������ ��������
      if (($n = array_search($parent, $classes))) {
        $tree[$i] = [
          'index' => $i,
          'class' => $path,
          'parent' => $n
        ];
      } else {
        $tree[$i] = [
          'index' => $i,
          'class' => $path,
          'parent' => 0
        ];
      }
    }
    $all = [];
    $output = [];
    $dangling = [];
    foreach ($tree as $id => $entry) {
      $entry['children'] = [];
      // If this is a top-level node, add it to the output immediately
      if (!$entry['parent']) {
        $all[$id] = $entry;
        $output[] =& $all[$id];
      } else {
        // If this isn't a top-level node, we have to process it later
        $dangling[$id] = $entry;
      }
    }
    // Process all 'dangling' nodes
    while (count($dangling) > 0) {
      foreach($dangling as $id => $entry) {
        $pid = $entry['parent'];
        // If the parent has already been added to the output, it's
        // safe to add this node too
        if (isset($all[$pid])) {
          $all[$id] = $entry;
          $all[$pid]['children'][] =& $all[$id];
          unset($dangling[$id]);
        }
      }
    }
    $result = [];
    foreach ($output as $v) {
      $this->orderDebugPathsAddChildrenToResult($result, $v);
    }
    $this->debugPaths = $result;
  }


  function orderDebugPathsAddChildrenToResult(array &$r, array $v) {
    $r[] = $this->debugPaths[$v['index']];
    if ($v['children']) {
      foreach ($v['children'] as $child) {
        $this->orderDebugPathsAddChildrenToResult($r, $child);
      }
    }
  }

  protected function addDebugTags() {
    $html = '';
    if ($this->debugPaths) {
      $this->orderDebugPaths();
      foreach ($this->debugPaths as $path) {
        $html .= $this->base->getTag((isset(Sflm::$debugUrl) ? Sflm::$debugUrl : '').'/'.ltrim($path, '/'));
      }
    }
    return $html;
  }

  protected function uglify($file) {
    //sys("uglifyjs $file --compress --mangle -o $file");
  }

}