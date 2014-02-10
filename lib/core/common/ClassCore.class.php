<?php

class ClassCore {

  static function getAncestors($class) {
    Lib::checkExistance($class);
    for ($classes[] = $class; $class = get_parent_class($class); $classes[] = $class) ;
    return $classes;
  }

  static function getAncestorsByPrefix($class, $prefix) {
    return array_filter(self::getAncestors($class), function($str) use ($prefix) {
      return self::hasPrefix($prefix, $str);
    });
  }

  static function getAncestorNames($class, $prefix) {
    return Arr::filterEmptiesR(array_map(function($v) use ($prefix) {
      return lcfirst(Misc::removePrefix($prefix, $v));
    }, self::getAncestorsByPrefix($class, $prefix)));
  }

  static function getFirstAncestor($name, $masterPrefix, $prefix) {
    if (!class_exists($masterPrefix.ucfirst($name))) return false;
    $r = array_map(function($v) use ($masterPrefix, $prefix) {
      return $prefix.Misc::removePrefix($masterPrefix, $v);
    }, self::getAncestorsByPrefix($masterPrefix.ucfirst($name), $masterPrefix));
    if (empty($r)) return false;
    foreach ($r as $class) if (class_exists($class)) return $class;
    return false;
  }

  /**
   * Определяет есть ли в классах-предках класса $class класс $ancestor
   *
   * @param   string  Имя класса или объект
   * @param   string  Имя предполагаемого класса предка
   * @param   bool    ..
   */
  static function hasAncestor($class, $ancestor, $strict = false) {
    if (!$strict and $class == $ancestor) return true;
    if (is_object($class)) $class = get_class($class);
    while (($cl = get_parent_class($class)) !== false) {
      if ($cl == $ancestor) return true;
      $class = $cl;
    }
    return false;
  }

  static function hasPrefix($prefix, $class) {
    if (!Misc::hasPrefix($prefix, $class)) return false;
    if (!($cutted = Misc::removePrefix($prefix, $class))) return false;
    return ctype_upper($cutted[0]);
  }

  /**
   * Возвращает имена неабстрактных потомков класса.
   * Имя абстрактного класса должно иметь вид: PrefixAbstract
   * Имя неабстрактного класса потомка должно иметь вид: PrefixName
   *
   * @param   string  Имя класса-предка
   *                  Пример: GrabberSourceAbstract
   * @return  array   Имена классов
   */
  static function getDescendants($ancestorClass, $prefix = false) {
    $classes = [];
    if ($prefix === false) $prefix = str_replace('Abstract', '', $ancestorClass);
    $n = 0;
    foreach (Lib::getClassesListCached() as $class => $v) {
      if ($prefix and !self::hasPrefix($prefix, $class)) continue;
      $reflection = new ReflectionClass($class);
      if ($reflection->isAbstract()) continue;
      if (!self::hasAncestor($class, $ancestorClass)) continue;
      $classes[$n] = [
        'class' => $class,
        'name'  => self::classToName($prefix, $class)
      ];
      if (isset($class::$title)) $classes[$n]['title'] = self::getStaticProperty($class, 'title');
      $n++;
    }
    return $classes;
  }

  static function classToName($prefix, $class) {
    $prefix = ucfirst($prefix);
    if (is_object($class)) $class = get_class($class);
    return lcfirst(Misc::removePrefix($prefix, $class));
  }

  static function nameToClass($prefix, $name) {
    return $prefix.ucfirst($name);
  }

  static function getObjectsByNames($prefix, array $names) {
    $objects = [];
    foreach ($names as $name) {
      $objects[] = O::get(self::nameToClass($prefix, $name));
    }
    return $objects;
  }

  static function getExistingClass($prefix, array $names) {
    die2([$prefix, $names]);
    foreach ($names as $name) {
      $class = self::nameToClass($prefix, $name);
      if (class_exists($class)) return $class;
    }
    return false;
  }

  static function getStaticProperties($classPrefix, $prop, $orderProp = null) {
    $properties = [];
    foreach (array_keys(Lib::getClassesListCached()) as $class) {
      if (preg_match('/'.$classPrefix.'(.*)/', $class, $m)) {
        if (!isset($class::$$prop)) continue;
        if ($orderProp) {
          $properties[lcfirst($m[1])] = [
            $prop      => self::getStaticProperty($class, $prop),
            $orderProp => self::getStaticProperty($class, $orderProp)
          ];
        }
        else {
          $properties[lcfirst($m[1])] = self::getStaticProperty($class, $prop);
        }
      }
    }
    if ($orderProp) {
      return Arr::toOptions(Arr::sortByOrderKey($properties, $orderProp), $prop);
    }
    return $properties;
  }

  static function getStaticProperty($class, $prop, $strict = true) {
    if (!isset($class::$$prop)) {
      if ($strict) throw new Exception("Static proprty '$prop' does not exists in class '$class'");
      else
        return false;
    }
    return $class::$$prop;
  }

  static function getClassesByPrefix($prefix) {
    $classes = [];
    foreach (array_keys(Lib::getClassesListCached()) as $class) {
      if (preg_match('/^'.$prefix.'.*/', $class)) $classes[] = $class;
    }
    return $classes;
  }

  static function getNames($prefix) {
    return array_map(function($class) use ($prefix) {
      return ClassCore::classToName($prefix, $class);
    }, array_filter(self::getClassesByPrefix($prefix), function($class) {
        $refl = new ReflectionClass($class);
        return !$refl->isAbstract();
      }));
  }

  static function getParents($class) {
    $pars = [];
    while (($par = get_parent_class($class)) !== false) {
      $pars[] = $par;
      $class = $par;
    }
    return $pars;
  }

  static function checkInstance($obj, $class) {
    if (!is_a($obj, $class)) throw new Exception("Class '$obj' must be instance of '$class'");
  }

  static function checkExistance($class, $method = null) {
    if ($method) {
      if (is_object($class)) $class = get_class($class);
      if (!method_exists($class, $method)) throw new Exception("Method '$class::$method' does not exists");
    } elseif (!class_exists($class)) {
      throw new Exception("Class '$class' does not exists");
    }
  }

  static function clon($obj) {
    return clone $obj;
  }

  static function getDocComment($str, $_tag) {
    $tag = '@'.$_tag;
    if ($_tag == 'options') {
      if (!preg_match("/".$tag."\\s+(.*)(\\r\\n|\\r|\\n)/s", $str, $m)) return false;
      if (!empty($m[1])) return trim($m[1]);
      return false;
    } elseif ($_tag == 'param') {
      if (!preg_match_all("/\\*\\s".$tag."\\s+([^\n]+)\n/sm", $str, $m)) return false;
      $r = $m[1];
      foreach ($r as &$v) {
        if (preg_match('/(\\S+)\\s+(.+)/', $v, $m)) $v = [$m[1], $m[2]];
      }
      return $r;
    } else {
      throw new Exception("Tag '$_tag' npt realized'");
    }
  }

  static function hasTrait($class, $trait) {
    if (is_object($class)) $class = get_class($class);
    foreach (self::getAncestors($class) as $cls) {
      if (in_array($trait, (new ReflectionClass($cls))->getTraitNames())) return true;
    }
    return false;
  }

}

