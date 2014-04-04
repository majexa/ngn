<?php

class ObjectCacherGen {

  function getObjectClass($chachedClass) {
    $r = new ReflectionMethod($chachedClass, 'getObject');
    $strings = file(Lib::getClassPath($chachedClass));
    for ($i = $r->getStartLine(); $i <= $r->getEndLine(); $i++) {
      if (preg_match('/return new (\w+)/', $strings[$i], $m)) return $m[1];
    }
    return false;
  }

  function gen() {
    foreach (ClassCore::getDescendants('ObjectCacher', false) as $v) {
      $class = $v['class'];
      $s = "/**\n";
      $objectClass = $this->getObjectClass($class);
      foreach ((new ReflectionClass($objectClass))->getMethods() as $v) {
        print '.';
        $params = Tt()->enum(Arr::get((new ReflectionMethod($objectClass, $v->name))->getParameters(), 'name'), ', ', '`$`.$v');
        $s .= " * @method void {$v->name}($params)\n";
      }
      $s .= " */\n";
      $file = Lib::getClassPath($class);
      file_put_contents($file, preg_replace("/(<\\?php.*)(class )/s", '$1'.$s.'$2', file_get_contents($file)));
    }
  }

}