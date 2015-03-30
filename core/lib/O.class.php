<?php

/**
 * Класс для подключения библиотек и создания объектов
 */
class O {

  const CLASS_NAME = __CLASS__;

  static private $storage;

  static private function getStoreId($class, $args) {
    for ($i = 1; $i < count($args); $i++) if (is_object($args[$i])) return false;
    return $class.md5(serialize($args));
    //return $class.(isset($argsForStoreId) ? '_'.implode(',', $argsForStoreId) : '');
  }

  /**
   * Возвращает объект
   *
   * @param string $path Путь до класса без расширения. Пример: "dd/DdItemsPage"
   * @return object
   * @throws Exception
   */
  static function get($path) {
    $classExists = false;
    if (!strstr($path, '/') and class_exists($path)) {
      $classExists = true;
      $class = $path;
    }
    if (!$classExists) {
      if (($filepath = Lib::getPath($path)) === false) throw new Exception("Class by path '$path' not found");
      $class = preg_replace('/.*\/([\w_]+)\.class\.php/', '$1', $filepath);
    }
    $args = func_get_args();
    // Объекты, в параметрах конструктора которых встречаются массивы или объекты,
    // не могут быть закэшированы !!!
    if (($canStore = $storeId = self::getStoreId($class, $args)) !== false) {
      // Вначале проверяем наличие объекта в ststic-хранилище
      if (isset(self::$storage[$storeId])) return self::$storage[$storeId];
    }
    // А если ни там ни там нет, тогда создаем объект
    if (!$classExists) require_once $filepath;
    $reflect = new ReflectionClass($class);
    $args = Arr::sliceFrom($args, 1);
    $obj = $args ? $reflect->newInstanceArgs($args) : $reflect->newInstance();
    if ($canStore) self::$storage[$storeId] = $obj; // Сохраняем в static-хранилище
    return $obj;
  }

  static function supportsCaching($class) {
    return self::getStoreId($class, func_get_args());
  }

  /**
   * Используется для получения экземпляров класса через Dependency Injection
   *
   * @param $class
   * @return mixed
   */
  static function di($class) {
    $args = func_get_args();
    if (isset(self::$injections[$class])) {
      foreach (self::$injections[$class] as $v) {
        if ($v['args'] === false) {
          $args[0] = $v['class'];
          break;
        }
        if (array_slice($args, 1, $v['strict'] ? count($args) : count($v['args'])) == $v['args']) {
          $args[0] = $v['class'];
          break;
        }
      }
    }
    return call_user_func_array(['self', 'get'], $args);
  }

  static public $injections = [];

  static function registerInjection($classToRewrite, $classRewriter, $args = [], $strict = true) {
    self::$injections[$classToRewrite][] = [
      'class'  => $classRewriter,
      'args'   => $args,
      'strict' => $strict
    ];
  }

  static function replaceInjection($classToRewrite, $classRewriter) {
    self::$injections[$classToRewrite] = [];
    self::registerInjection($classToRewrite, $classRewriter, [], false);
  }

  static function take($path) {
    return class_exists($path) ? forward_static_call_array(['O', 'get'], func_get_args()) : false;
  }

  static function delete($class) {
    $args = func_get_args();
    $storeId = self::getStoreId($class, $args);
    if ($storeId) unset(self::$storage[$storeId]);
  }

}
