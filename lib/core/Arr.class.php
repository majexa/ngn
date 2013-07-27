<?php

class Arr {

  static function append(array $arr1, array $arr2, $withoutRepetitions = false) {
    if (!$withoutRepetitions) return array_merge($arr1, $arr2);
    for ($i = 0; $i < count($arr2); $i++) {
      if (in_array($arr2[$i], $arr1)) continue;
      $arr1[] = $arr2[$i];
    }
    return $arr1;
  }

  static function prepend(array $arr, $value) {
    $value = (array)$value;
    for ($i = 0; $i < count($arr); $i++) $value[] = $arr[$i];
    return $value;
  }

  static function assoc(array $arr, $k, $multi = false) {
    $res = [];
    foreach ($arr as $v) $multi ? $res[$v[$k]][] = $v : $res[$v[$k]] = $v;
    return $res;
  }

  static function drop(array $arr, $v) {
    for ($i = 0; $i < count($arr); $i++) {
      if ($arr[$i] != $v) $arr2[] = $arr[$i];
    }
    return isset($arr2) ? $arr2 : [];
  }

  static function dropK(array $arr, $k) {
    unset($arr[$k]);
    return $arr;
  }

  static function dropBySubKeys(array $arr, $k, $v, $assoc = false) {
    $new = [];
    $v = (array)$v;
    foreach ($arr as $key => $val) {
      if (isset($val[$k]) and in_array($val[$k], $v)) {
        continue;
      }
      $assoc ? $new[$key] = $val : $new[] = $val;
    }
    return $new;
  }

  static function dropArr(array &$arr, array $arr3) {
    for ($i = 0; $i < count($arr); $i++) {
      if (!in_array($arr[$i], $arr3)) $arr2[] = $arr[$i];
    }
    return $arr = $arr2 ? $arr2 : [];
  }

  static function dropN(array &$arr, $n) {
    $arr2 = [];
    for ($i = 0; $i < count($arr); $i++) {
      if ($i != $n) $arr2[] = $arr[$i];
    }
    $arr = $arr2;
  }

  static function dropCallback(array $arr, $func) {
    foreach ($arr as $v) if (!$func($v)) $r[] = $v;
    return $r;
  }


  /**
   * Вынимает значения из элементов хэша и возвращает их как массив
   *
   * @param   array   Массив с массивами
   * @param   string  Ключ элемента подмассива, элемент которого необходимо использовать,
   *                  как элемент результирующего массива
   * @param   string  Ключ элемента подмассива, значение которого необходимо
   *                  использовать в качестве ключа результирующего массива
   * @return  array
   */
  static function get(array $arr, $k, $kk = null) {
    $res = [];
    if ($kk == 'KEY') foreach ($arr as $KEY => $v) $res[$KEY] = is_array($v) ? $v[$k] : $v->$k;
    elseif ($kk) foreach ($arr as $v) $res[$v[$kk]] = is_array($v) ? $v[$k] : $v->$k;
    else foreach ($arr as $v) $res[] = is_array($v) ? $v[$k] : $v->$k;
    return $res;
  }

  static function get_value(array $arr, $k1, $v1, $k2) {
    foreach ($arr as $v) if (isset($v[$k1]) and isset($v[$k2]) and $v[$k1] == $v1) return $v[$k2];
    return false;
  }

  static function getValue(array $arr, $key) {
    return isset($arr[$key]) ? $arr[$key] : false;
  }

  static function getValueByKey(array $arr, $k1, $v1) {
    foreach ($arr as $v) {
      if ($v[$k1] == $v1) return $v;
    }
    return false;
  }

  static function first_key(array $arr) {
    foreach ($arr as $k => $v) return $k;
  }

  static function first(array $arr, $key = null) {
    foreach ($arr as $k => $v) {
      if ($key) return $v[$key];
      return $v;
    }
    return false;
  }

  static function last(array $arr) {
    $vv = false;
    foreach ($arr as $v) $vv = $v;
    return $vv;
  }

  static function last_key(array $arr) {
    $keys = array_keys($arr);
    return $keys[count($keys) - 1];
  }

  static function isEmpty($value) {
    if (is_array($value)) {
      foreach ($value as &$v) {
        if (is_array($v)) {
          if (!Arr::isEmpty($v)) return false;
        }
        elseif ($v) return false;
      }
      return true;
    }
    else {
      return $value ? false : true;
    }
  }

  static function flip2(array $arr) {
    foreach ($arr as $k => $v) {
      foreach ($v as $v2) {
        $arr2[$v2] = $k;
      }
    }
    return $arr2;
  }

  static function quote(array &$arr) {
    array_walk($arr, 'quoting');
    return $arr;
  }

  static function toObjProp(array $arr, $obj, array $filter = []) {
    foreach ($arr as $k => $v) {
      if (!empty($filter) and !in_array($k, $filter)) continue;
      if (isset($obj->$k)) $obj->$k = $v;
    }
  }

  static function remove(&$arr, $_v) {
    foreach ($arr as $k => $v) {
      if ($v == $_v) {
        unset($arr[$k]);
      }
    }
  }

  static function filterByKeys(array $arr, $keys) {
    $keys = (array)$keys;
    $r = [];
    foreach ($arr as $k => $v) {
      if (in_array($k, $keys)) $r[$k] = $v;
    }
    return $r;
  }

  static function filterEmpties(array &$arr, $assoc = true) {
    $new = [];
    if ($assoc) {
      foreach ($arr as $k => $v) {
        if (!Arr::isEmpty($v)) {
          $new[$k] = $v;
        }
      }
    }
    else {
      foreach ($arr as $v) if (!Arr::isEmpty($v)) $new[] = $v;
    }
    return $new;
  }

  static function filterEmptyStrings(array $arr, $assoc = true) {
    $new = [];
    if ($assoc) {
      foreach ($arr as $k => $v) if ($v !== '') $new[$k] = $v;
    }
    else {
      foreach ($arr as $v) if ($v !== '') $new[] = $v;
    }
    return $new;
  }

  static function filterEmptiesR(array $arr, $assoc = true) {
    $new = [];
    if ($assoc) {
      foreach ($arr as $k => $v) {
        if (is_array($v)) $v = Arr::filterEmpties($v);
        if ($v) $new[$k] = $v;
      }
    }
    else {
      foreach ($arr as $v) {
        if (is_array($v)) $v = Arr::filterEmpties($v);
        if ($v) $new[] = $v;
      }
    }
    return $new;
  }

  static function getKeyByValue(array $arr, $subK, $subV) {
    foreach ($arr as $k => $v) if (isset($v[$subK]) and $v[$subK] == $subV) return $k;
    return false;
  }


  static function filterByExceptKeys(array $arr, $keys) {
    $keys = (array)$keys;
    $r = [];
    foreach ($arr as $key => $val) {
      if (in_array($key, $keys)) continue;
      $r[$key] = $val;
    }
    return $r;
  }

  static function filter_by_value(array $arr, $key, $value, $assoc = false, $ignore = false) {
    $r = [];
    $value = (array)$value;
    foreach ($arr as $k => $v) {
      if (isset($v[$key])) {
        if (in_array($v[$key], $value)) {
          $assoc ? $r[$k] = $v : $r[] = $v;
        }
      } elseif ($ignore) {
        $assoc ? $r[$k] = $v : $r[] = $v;
      }
    }
    return $r;
  }

  static function filterFunc(array $arr, $func, $assoc = true) {
    $r = [];
    if ($assoc) {
      foreach ($arr as $k => $v) if ($func($v, $k)) $r[$k] = $v;
    }
    else {
      foreach ($arr as $k => $v) if ($func($v, $k)) $r[] = $v;
    }
    return $r;
  }

  static function sliceFrom(array $arr, $n, $length = null) {
    return array_slice($arr, $n, $length ?: count($arr));
  }

  static function toOptions(array $arr, $key = null) {
    $r = [];
    if ($key) {
      foreach ($arr as $k => $v) {
        if (is_array($v[$key])) $r[$v[$key][0]] = $v[$key][1];
        else
          $r[$k] = $v[$key];
      }
    }
    else {
      foreach ($arr as $v) {
        if (is_array($v)) $r[$v[0]] = $v[1];
        else
          $r[$v] = $v;
      }
    }
    return $r;
  }

  static function toAssoc($arr, $key) {
    $arr2 = [];
    foreach ($arr as $v) $arr2[$v[$key]] = $v;
    return $arr2;
  }

  static function proximity(array $arr, $current, $loop = false) {
    for ($i = 0; $i < count($arr); $i++) {
      if ($current == $arr[$i]) {
        $prev = isset($arr[$i - 1]) ? $arr[$i - 1] : ($loop ? $arr[count($arr) - 1] : -1);
        $next = isset($arr[$i + 1]) ? $arr[$i + 1] : ($loop ? $arr[0] : -1);
      }
    }
    if (!isset($prev)) return false;
    return [$prev, $next];
  }

  static function str_replace($arr, $search, $replace) {
    foreach ($arr as $k => $v) {
      $arr[$k] = str_replace($search, $replace, $v);
    }
    return $arr;
  }

  static protected $depth;

  static function js(array $array, $formatValue = true, array $_isArray = [true]) {
    self::$depth = 0;
    return self::_js($array, $formatValue, $_isArray);
  }

  static protected function _js(array $array, $formatValue = true, array $_isArray = [true]) {
    self::$depth++;
    $isArray = isset($_isArray[self::$depth-1]) ? $_isArray[self::$depth-1] : $_isArray[count($_isArray)-1];
    if ($isArray) {
      $bracketO = '[';
      $bracketC = ']';
    }
    else {
      $bracketO = '{';
      $bracketC = '}';
    }
    $jsArray = $bracketO;
    $temp = [];
    foreach ($array as $key => $value) {
      $jsKey = $isArray ? '' : "'".$key."': ";
      if (is_array($value)) {
        $temp[] = $jsKey.Arr::_js($value, true, $_isArray);
      }
      else {
        if (is_numeric($value)) {
          $jsKey .= $value;
        }
        elseif (is_bool($value)) {
          $jsKey .= ($value ? 'true' : 'false')."";
        }
        elseif ($value === null) {
          $jsKey .= "null";
        }
        else {
          if ($formatValue) {
            $jsKey .= self::jsString($value);
          }
          else {
            $jsKey .= $value;
          }
        }
        $temp[] = $jsKey;
      }
    }
    $jsArray .= implode(', ', $temp);
    $jsArray .= $bracketC;
    self::$depth--;
    return $jsArray;
  }

  static function jsString($s) {
    return "'".str_replace(["\\", "'", "\r", "\n"], ['\\\\', '\\\'', '\r', '\n'], $s)."'";
  }

  static function jsObj($array, $formatFirstLevelValue = true) {
    return Arr::js($array, $formatFirstLevelValue, [false]);
  }

  static function jsArr($array, $formatFirstLevelValue = true) {
    return Arr::js($array, $formatFirstLevelValue);
  }

  static function jsValue($v) {
    if (is_bool($v)) return $v ? 'true' : 'false';
    elseif ($v === null) return 'null';
    elseif (is_numeric($v)) return $v;
    elseif (is_array($v)) return self::jsArr($v);
    else return self::jsString($v);
  }

  static function formatValue($v, $stringBools = true, $depth = 0) {
    if (is_array($v)) {
      $values = [];
      $assoc = self::isAssoc($v);
      foreach ($v as $kk => &$vv) {
        if ($assoc) {
          $values[] = (is_int($kk) ? $kk : "'$kk'")." => ".Arr::formatValue($vv, $stringBools, $depth + 1);
        }
        else
          $values[] = Arr::formatValue($vv, $stringBools, $depth + 1);
      }
      if (count($values) == 1) return "[$values[0]]";
      $r = "[\n";
      foreach ($values as $i => $v) $r .= str_repeat('  ', $depth + 1).$v.($i != count($values) - 1 ? ',' : '')."\n";
      $r .= str_repeat('  ', $depth)."]";
      return $r;
    }
    elseif ($v == 'true' or $v == 'false') {
      return $v == 'true' ? ($stringBools ? 'true' : true) : ($stringBools ? 'false' : false);
    }
    elseif (is_bool($v)) {
      return $v ? ($stringBools ? 'true' : true) : ($stringBools ? 'false' : false);
    }
    elseif (is_int($v)) {
      return $v;
    }
    else {
      if (strstr($v, "\n")) return "<<<TEXT\n$v\nTEXT\n";
      $v = str_replace("'", "\\'", $v);
      return "'$v'";
    }
  }

  static function formatValue2($v) {
    return self::formatValue($v, false);
  }

  /**
   * Преобразует строку вида "'asd'" '"asd"' или "false" в соответствующее
   * значение типа string или boolen
   *
   * @param   array
   * @return  array
   */
  static function deformatValue($v) {
    if (($v[0] == "'" and $v[count($v) - 1] == "'") or ($v[0] == '"' and $v[count($v) - 1] == '"')) return substr($v, 1, count($v) - 2);
    else {
      if ($v == 'true') return true;
      elseif ($v == 'false') return false;
      return $v;
    }
  }

  /**
   * Меняет строки в многомерном массиве на интежеры
   *
   * @param   array
   * @return  array
   */
  static function transformValue($v) {
    if (is_array($v)) {
      foreach ($v as &$vv) $vv = Arr::transformValue($vv);
      return $v;
    }
    elseif (is_numeric($v)) return (int)$v;
    else return $v;
  }

  static function checkEmpty(array $arr, $keys, $quitely = false) {
    $keys = (array)$keys;
    foreach ($keys as $k) {
      if (empty($arr[$k])) {
        LogWriter::v('CHECK_EMPTY', [$k, $arr]);
        if ($quitely) return false;
        else throw new Exception("Key '$k' has empty value in array: ".getPrr($arr));
      }
    }
    return $arr;
  }

  static function checkNotEmptyAny(array $arr, array $keys) {
    foreach ($keys as $k) {
      if (!empty($arr[$k])) return;
    }
    throw new Exception("Array has only empty values: ".getPrr($arr));
  }

  static function checkIsset(array $arr, $keys) {
    $keys = (array)$keys;
    foreach ($keys as $k) if (!isset($arr[$k])) throw new Exception("Key '$k' does not exists in array: ".getPrr($arr));
  }

  static function explodeCommas($s) {
    $s = explode(',', $s);
    array_walk($s, 'trim');
    return $s;
  }

  static function replaceKey(array $arr, $oldKey, $newKey) {
    $new = [];
    foreach ($arr as $k => $v) {
      if ($k == $oldKey) {
        $k = $newKey;
      }
      $new[$k] = $v;
    }
    return $new;
  }

  static function unsetKey(array $arr, $key) {
    unset($arr[$key]);
    return $arr;
  }

  static function subValueExists(array $arr, $key, $value) {
    foreach ($arr as $v) {
      foreach ($v as $kk => $vv) {
        if ($kk == $key and $vv == $value) return true;
      }
    }
    return false;
  }

  static function filterByKeys2(array $arr, array $keys) {
    $r = [];
    foreach ($arr as $k => $v) {
      $r[$k] = self::filterByKeys($v, $keys);
    }
    return $r;
  }

  static function isAssoc(array $arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
  }

  static function rand(array $arr) {
    return $arr[array_rand($arr)];
  }

  static function unserializeble($s) {
    return preg_match('/^a:\d+:\{/', $s);
  }

  static function unserialize(array $data) {
    foreach ($data as $k => $v) {
      if (!empty($v) and is_string($v) and Arr::unserializeble($v)) {
        $_v = $v;
        $v = unserialize($v);
        if ($v === false) {
          throw new Exception('Error unserialization $v: "'.getPrr($_v).'"');
        }
        else {
          $data[$k] = $v;
        }
      }
    }
    return $data;
  }

  static function serialize(array $data) {
    foreach ($data as $k => $v) if (is_array($v)) $data[$k] = serialize($data[$k]);
    return $data;
  }

  static function injectAfter(array $array, $k, array $values, $assocKey = false) {
    $r = [];
    if ($assocKey) {
      foreach ($array as $kk => $vv) {
        $r[$kk] = $vv;
        if ($kk == $k) foreach ($values as $value) $r[$value[$assocKey]] = $value;
      }
    }
    else {
      for ($i = 0; $i < count($array); $i++) {
        $r[] = $array[$i];
        if ($i == $k) foreach ($values as $value) $r[] = $value;
      }
    }
    return $r;
  }

  static function injectAfterBySubkey(array $array, $k, array $values, $subkey) {
    $r = [];
    foreach ($array as $kk => $vv) {
      $r[$kk] = $vv;
      if (isset($vv[$subkey]) and $vv[$subkey] == $k) foreach ($values as $value) $r[$value[$subkey]] = $value;
    }
    return $r;
  }

  static function injectAfterBySubkey2(array $array, $k, array $values, $subkey) {
    $r = [];
    for ($i=0; $i<count($array); $i++) {
      $r[] = $array[$i];
      if (isset($array[$i][$subkey]) and $array[$i][$subkey] == $k) {
        foreach ($values as $value) $r[] = $value;
      }
    }
    return $r;
  }

  static function replaceSubValue(array $arr, $key, $find, $replace) {
    foreach ($arr as $k => $v) if ($v[$key] == $find) $v[$key] = $replace;
    return $arr;
  }

  static function sortByArray(array $array, array $orderKeys) {
    $ordered = [];
    foreach ($orderKeys as $key) {
      if (array_key_exists($key, $array)) {
        $ordered[$key] = $array[$key];
        unset($array[$key]);
      }
    }
    return $ordered + $array;
  }

  static function sortByOrderKey(array $arr, $key, $order = SORT_ASC) {
    if (!$arr) return [];
    foreach ($arr as $k => $v) {
      $o[$k] = isset($v[$key]) ? $v[$key] : 0;
    }
    array_multisort($o, $order, $arr);
    return $arr;
  }

  /**
   * Проверяет является ли $subset подмножеством $set
   * @static
   * @param $set
   * @param $subset
   */
  static function isSubset(array $set, $subset) {
    foreach ($subset as $v) if (!in_array($v, $set)) return false;
    return true;
  }

  static function isSubsetOfAnyOther(array $set, $index) {
    foreach ($set as $k => $v) {
      if ($k == $index) continue;
      if (self::isSubset($v, $set[$index])) return true;
    }
    return false;
  }

  static function filterSubsets(array $set, $invert = false) {
    $r = [];
    foreach (array_keys($set) as $k) {
      if (self::isSubsetOfAnyOther($set, $k) === $invert) {
        $r[] = $set[$k];
      }
    }
    return $r;
  }

  static function incr(array &$a, $k) {
    if (!isset($a[$k])) {
      $a[$k] = 1;
      return;
    }
    $a[$k]++;
  }

}
